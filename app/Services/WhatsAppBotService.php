<?php

namespace App\Services;

use App\Models\User;
use App\Models\ServiceSmm;
use App\Models\CategorySmm;
use App\Models\OrderSmm;
use App\Models\HistoryDeposit;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppBotService
{
    /**
     * Handle incoming WhatsApp message
     */
    public static function handleMessage($sender, $message)
    {
        try {
            // Extract actual sender from group chat or normalize personal chat
            $actualSender = $sender;
            
            // If it's a group chat (contains @g.us), we need to get the actual sender from message
            if (str_contains($sender, '@g.us')) {
                // For group messages, we need the participant info from the message structure
                // For now, skip group messages or return error
                Log::info("Group message detected from: {$sender}");
                return self::sendMessage($sender, "❌ *Group Chat Not Supported*\n\nBot can only be used in private chat. Please send a direct message to the bot number.");
            }
            
            // Normalize phone number for DB lookup (strip non-digits, remove country code if present)
            $digitsSender = preg_replace('/\D+/', '', (string) $actualSender);
            $normalizedPhone = $digitsSender;
            if (strlen($digitsSender) > 10 && str_starts_with($digitsSender, '62')) {
                $normalizedPhone = substr($digitsSender, 2);
            } elseif (strlen($digitsSender) > 10 && str_starts_with($digitsSender, '0')) {
                $normalizedPhone = substr($digitsSender, 1);
            }
            
            // Find user by phone number
            $user = User::where('phone', $normalizedPhone)->first();
            
            if (!$user) {
                return self::sendMessage($sender, "❌ *Access Denied*\n\nYour number is not registered in the system. Please register on the website first.");
            }

            // Check if user is active
            if ($user->status !== 'Active') {
                return self::sendMessage($sender, "❌ *Account Inactive*\n\nYour account has not been verified. Please log in to the website to verify OTP.");
            }

            // Parse command
            $message = trim($message);
            $lowerMessage = strtolower($message);
            
            if ($lowerMessage === '/service') {
                return self::handleService($sender);
            } elseif ($lowerMessage === '/order') {
                $form = "📋 *SMM Order Form*\n\nPlease *Copy* the form below, fill in the data, and send it back to the bot:\n\n/order:\n[\nid:,\ntarget:,\namount:\n]";
                return self::sendMessage($sender, $form);
            } elseif (str_starts_with($lowerMessage, '/order:')) {
                // Pass the whole message to parse id, target, and jumlah
                return self::handleOrder($sender, $user, $message);
            } elseif (str_starts_with($lowerMessage, '/status:')) {
                $invoice = trim(substr($message, 8));
                return self::handleStatus($sender, $user, $invoice);
            } elseif ($lowerMessage === '/balance') {
                return self::handleBalance($sender, $user);
            } elseif (str_starts_with($lowerMessage, '/deposit.cryptomus:')) {
                $amount = trim(substr($message, 19));
                return self::handleDeposit($sender, $user, $amount);
            } elseif ($lowerMessage === '/help') {
                return self::handleHelp($sender);
            } else {
                return self::handleHelp($sender);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Bot Error: ' . $e->getMessage());
            return self::sendMessage($sender, "❌ *Error Occurred*\n\nPlease try again later or contact the admin.");
        }
    }

    /**
     * Send message via WhatsApp
     */
    private static function sendMessage($recipient, $message)
    {
        // Normalize recipient to digits and ensure country code (ID: 62)
        $number = preg_replace('/\D+/', '', (string) $recipient);

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . $number;
        }

        // Use Bot type WhatsApp account
        $botAccount = WhatsAppService::getAccountByType('Bot');
        $sessionId = $botAccount ? $botAccount->phone_number : null;

        return WhatsAppService::sendMessage($number, $message, $sessionId);
    }

    /**
     * Handle /service command
     */
    private static function handleService($sender)
    {
        $categories = CategorySmm::with(['services' => function($query) {
            $query->where('status', 'Active')->orderBy('id');
        }])->get();

        if ($categories->isEmpty()) {
            return self::sendMessage($sender, "📋 *Service List*\n\nNo services available yet.");
        }

        $response = "📋 *SMM SERVICE LIST*\n\n";
        
        foreach ($categories as $category) {
            if ($category->services->isEmpty()) continue;
            
            $response .= "📂 *{$category->name}*\n";
            
            foreach ($category->services as $service) {
                $price = number_format($service->price_sale, 2, '.', ',');
                $response .= "• ID: {$service->id}\n";
                $response .= "  {$service->name_service}\n";
                $response .= "  💰 \${$price}\n";
                $response .= "  📉 Min: {$service->min_order} | 📈 Max: {$service->max_order}\n";
                $response .= "  ───────────────\n";
            }
            $response .= "\n";
        }

        $response .= "💡 *How to Order:*\nType command: /order\n\n";
        $response .= "Then fill in the form provided by the bot.";

        return self::sendMessage($sender, $response);
    }

    /**
     * Handle /order command
     */
    private static function handleOrder($sender, $user, $message)
    {
        $patternId = '/id:\s*(\d+)/i';
        $patternTarget = '/target:\s*([^,\n\]]+)/i';
        $patternJumlah = '/amount:\s*(\d+)/i';
        
        preg_match($patternId, $message, $matchId);
        preg_match($patternTarget, $message, $matchTarget);
        preg_match($patternJumlah, $message, $matchJumlah);
        
        $serviceId = $matchId[1] ?? null;
        $target = isset($matchTarget[1]) ? trim($matchTarget[1]) : null;
        $amount = isset($matchJumlah[1]) ? (int)$matchJumlah[1] : null;

        if (!$serviceId || !$target || !$amount) {
            return self::sendMessage($sender, "❌ *Invalid Format*\n\nUse format:\n/order:\n[\nid:123,\ntarget:username_or_link,\namount:100\n]");
        }

        $service = ServiceSmm::with('api')
            ->where('id', $serviceId)
            ->where('status', 'Active')
            ->first();

        if (!$service) {
            return self::sendMessage($sender, "❌ *Service Not Found*\n\nService ID {$serviceId} is invalid or inactive.");
        }

        // Calculate total price
        $totalPrice = ($amount * $service->price_sale) / 1000;

        // Check balance
        if ($user->balance < $totalPrice) {
            $needed = $totalPrice - $user->balance;
            return self::sendMessage($sender, "❌ *Insufficient Balance*\n\n💰 Price: $" . number_format($totalPrice, 2, '.', ',') . "\n💳 Your Balance: $" . number_format($user->balance, 2, '.', ',') . "\n📉 Short: $" . number_format($needed, 2, '.', ',') . "\n\nPlease deposit first:\n/deposit.cryptomus:AMOUNT");
        }

        // Check API configuration
        $api = $service->api;
        if (!$api || !$api->url || !$api->api_key) {
            return self::sendMessage($sender, "❌ *System Error*\n\nAPI Provider for this service is not configured.");
        }

        $postFields = [
            'action' => 'add',
            'key' => $api->api_key,
            'service' => $service->pid,
            'link' => $target,
            'quantity' => $amount
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::external()->asForm()->post($api->url, $postFields);

            if ($response->failed()) {
                return self::sendMessage($sender, "❌ *Connection Failed*\n\nFailed to connect to SMM API Provider.");
            }

            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'success') {
                $orderId = $data['order'];

                $invoice = 'INV-' . strtoupper(Str::random(10));
                while (OrderSmm::where('invoice', $invoice)->exists()) {
                    $invoice = 'INV-' . strtoupper(Str::random(10));
                }
                
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $totalPrice, $service, $api, $orderId, $target, $amount, $invoice) {
                    $userRefresh = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

                    if ($userRefresh->balance < $totalPrice) {
                        throw new \RuntimeException('INSUFFICIENT_BALANCE');
                    }

                    $userRefresh->decrement('balance', $totalPrice);

                    OrderSmm::create([
                        'invoice' => $invoice,
                        'id_user' => $userRefresh->id,
                        'id_service_smm' => $service->id,
                        'id_api_smm' => $api->id,
                        'sid' => $orderId,
                        'price_api' => ($amount * $service->price_api) / 1000,
                        'price_sale' => $totalPrice,
                        'price_reseller' => ($amount * $service->price_reseller) / 1000,
                        'status_order' => 'Pending',
                        'start_count' => 0,
                        'remains' => $amount,
                        'target' => $target,
                        'amount' => $amount,
                        'refill' => $service->refill,
                    ]);
                });

                // Send invoice
                $resMsg = "✅ *ORDER SUCCESSFULLY FORWARDED*\n\n";
                $resMsg .= "📄 *INVOICE: {$invoice}*\n";
                $resMsg .= "🛒 Service: {$service->name_service}\n";
                $resMsg .= "🎯 Target: {$target}\n";
                $resMsg .= "📦 Amount: {$amount}\n";
                $resMsg .= "💰 Price: $" . number_format($totalPrice, 2, '.', ',') . "\n";
                $resMsg .= "💳 Deducted Balance: $" . number_format($totalPrice, 2, '.', ',') . "\n";
                $resMsg .= "📊 Status: Pending\n\n";
                $resMsg .= "💡 *Check Status:*\n/status:{$invoice}";

                return self::sendMessage($sender, $resMsg);
            } else {
                $errorMsg = $data['error'] ?? 'Failed from API Provider.';
                return self::sendMessage($sender, "❌ *Order Failed*\n\nError Message from API:\n{$errorMsg}");
            }
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return self::sendMessage($sender, "❌ *Insufficient Balance*\n\nYour balance is no longer enough for this order. Please top up and retry.");
            }
            return self::sendMessage($sender, "❌ *System Error*\n\nA system error occurred: " . substr($e->getMessage(), 0, 100));
        } catch (\Exception $e) {
            return self::sendMessage($sender, "❌ *System Error*\n\nA system error occurred: " . substr($e->getMessage(), 0, 100));
        }
    }

    /**
     * Handle /status command
     */
    private static function handleStatus($sender, $user, $invoice)
    {
        $order = OrderSmm::where('invoice', $invoice)
            ->where('id_user', $user->id)
            ->first();

        if (!$order) {
            return self::sendMessage($sender, "❌ *Invoice Not Found*\n\nInvoice {$invoice} is invalid or doesn't belong to you.");
        }

        $service = $order->service;
        
        $statusIcon = match($order->status_order) {
            'Pending' => '⏳',
            'Processing' => '🔄',
            'Completed' => '✅',
            'Partial' => '⚠️',
            'Error' => '❌',
            default => '❓'
        };

        $response = "📊 *ORDER STATUS*\n\n";
        $response .= "📄 Invoice: {$order->invoice}\n";
        $response .= "🛒 Service: {$service->name_service}\n";
        $response .= "💰 Price: $" . number_format($order->price_sale, 2, '.', ',') . "\n";
        $response .= "📈 Status: {$statusIcon} {$order->status_order}\n";
        
        if ($order->start_count) {
            $response .= "🔢 Start Count: {$order->start_count}\n";
        }
        
        if ($order->remains) {
            $response .= "📉 Remains: {$order->remains}\n";
        }
        
        $response .= "\n📅 Date: {$order->create_at->format('d M Y H:i')}";

        return self::sendMessage($sender, $response);
    }

    /**
     * Handle /balance command
     */
    private static function handleBalance($sender, $user)
    {
        $response = "💳 *BALANCE INFORMATION*\n\n";
        $response .= "👤 Name: {$user->full_name}\n";
        $response .= "📧 Email: {$user->email}\n";
        $response .= "💰 Balance: $" . number_format($user->balance, 2, '.', ',') . "\n\n";
        $response .= "💡 *Deposit:*\n/deposit.cryptomus:AMOUNT\n\n";
        $response .= "Example: /deposit.cryptomus:10";

        return self::sendMessage($sender, $response);
    }

    /**
     * Handle /deposit command
     */
    private static function handleDeposit($sender, $user, $amount)
    {
        if (!is_numeric($amount) || $amount < 1) {
            return self::sendMessage($sender, "❌ *Invalid Amount*\n\nMinimum deposit is $1\n\nExample: /deposit.cryptomus:10");
        }

        // Get Cryptomus Gateway Configuration
        $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
        if (!$gateway || empty($gateway->api_config['merchant_id']) || empty($gateway->api_config['payment_key'])) {
            return self::sendMessage($sender, "❌ *System Error*\n\nCryptomus gateway is not configured.");
        }

        // Create deposit record
        $invoice = 'DEP-' . strtoupper(Str::random(8));
        
        $deposit = HistoryDeposit::create([
            'invoice' => $invoice,
            'id_user' => $user->id,
            'payment_gateway' => 'cryptomus',
            'amount' => $amount,
            'status' => 'Pending',
            'data' => null
        ]);

        // Process Cryptomus Request
        $postData = [
            'amount' => (string) $amount,
            'currency' => 'USD',
            'order_id' => $invoice,
            'url_callback' => route('member.payment.cryptomus.callback'),
            'url_success' => route('member.payment.history'),
            'url_return' => route('member.payment.history'),
        ];
        
        $sign = md5(base64_encode(json_encode($postData, JSON_UNESCAPED_UNICODE)) . $gateway->api_config['payment_key']);
        
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'merchant' => $gateway->api_config['merchant_id'],
                'sign' => $sign,
                'Content-Type' => 'application/json'
            ])->post('https://api.cryptomus.com/v1/payment', $postData);

            $resData = $response->json();

            if ($response->successful() && isset($resData['result']['url'])) {
                $paymentUrl = $resData['result']['url'];
                $uuid = $resData['result']['uuid'];

                // Update deposit record with data
                $deposit->update([
                    'data' => [
                        'cryptomus_uuid' => $uuid,
                        'payment_method' => 'Cryptomus',
                        'url' => $paymentUrl
                    ]
                ]);

                $msg = "💳 *DEPOSIT REQUEST*\n\n";
                $msg .= "📄 Invoice: {$invoice}\n";
                $msg .= "💰 Amount: $" . number_format($amount, 2, '.', ',') . "\n";
                $msg .= "💳 Method: Cryptomus\n";
                $msg .= "📊 Status: Pending\n\n";
                $msg .= "🔗 *Click the link below to pay directly:*\n";
                $msg .= "{$paymentUrl}";

                return self::sendMessage($sender, $msg);
            } else {
                $errorMsg = $resData['message'] ?? 'Invalid Cryptomus response.';
                return self::sendMessage($sender, "❌ *Cryptomus Error*\n\n" . $errorMsg);
            }
        } catch (\Exception $e) {
            return self::sendMessage($sender, "❌ *System Error*\n\nFailed to connect to Cryptomus.");
        }
    }

    /**
     * Handle /help command
     */
    private static function handleHelp($sender)
    {
        $response = "🤖 *SMM ORDER BOT - HELP*\n\n";
        $response .= "📋 *Command List:*\n\n";
        $response .= "🔸 /service\n";
        $response .= "   Show all services\n\n";
        $response .= "🔸 /order\n";
        $response .= "   Request order form\n\n";
        $response .= "🔸 /status:INVOICE\n";
        $response .= "   Check order status (example: /status:INV-12345678)\n\n";
        $response .= "🔸 /balance\n";
        $response .= "   Check account balance\n\n";
        $response .= "🔸 /deposit.cryptomus:AMOUNT\n";
        $response .= "   Request deposit (example: /deposit.cryptomus:10)\n\n";
        $response .= "🔸 /help\n";
        $response .= "   Show this help message\n\n";
        $response .= "💡 *Notes:*\n";
        $response .= "• WhatsApp number must be registered on the website\n";
        $response .= "• Account must be active (OTP verified)\n";
        $response .= "• Minimum deposit $1";

        return self::sendMessage($sender, $response);
    }
}
