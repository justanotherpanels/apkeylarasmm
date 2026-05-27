<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\HistoryDeposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private function allowedRedirectHosts(): array
    {
        $hosts = [];

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (is_string($appHost) && $appHost !== '') {
            $hosts[] = strtolower($appHost);
        }

        $extraHosts = explode(',', (string) env('PAYMENT_REDIRECT_ALLOWED_HOSTS', ''));
        foreach ($extraHosts as $host) {
            $host = strtolower(trim($host));
            if ($host !== '') {
                $hosts[] = $host;
            }
        }

        return array_values(array_unique($hosts));
    }

    private function isAllowedRedirectUrl(?string $url): bool
    {
        if (!$url) {
            return false;
        }

        $parsed = parse_url($url);
        if (!is_array($parsed) || empty($parsed['scheme']) || empty($parsed['host'])) {
            return false;
        }

        $scheme = strtolower((string) $parsed['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $strictHosts = filter_var(env('PAYMENT_REDIRECT_STRICT_HOSTS', false), FILTER_VALIDATE_BOOL);
        if (!$strictHosts) {
            return true;
        }

        return in_array(strtolower((string) $parsed['host']), $this->allowedRedirectHosts(), true);
    }

    /**
     * Helper to log payment errors to storage/logs/error.log
     */
    private function logError($message, $context = [])
    {
        try {
            $logPath = storage_path('logs/error.log');
            $timestamp = now()->toDateTimeString();
            $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
            $formattedMessage = "[{$timestamp}] PAYMENT ERROR: {$message}{$contextStr}" . PHP_EOL;
            file_put_contents($logPath, $formattedMessage, FILE_APPEND);
        } catch (\Exception $e) {
            // Fallback to standard Laravel log if file writing fails
            \Illuminate\Support\Facades\Log::error("Failed to write to error.log: " . $e->getMessage() . " | Original Error: " . $message);
        }
    }

    /**
     * Show deposit add form.
     */
    public function add()
    {
        $paypal = PaymentGateway::where('type', 'Paypal')->first();
        $cryptomus = PaymentGateway::where('type', 'Cryptomus')->first();

        // Check if at least one gateway is configured
        $paypalConfigured = !empty($paypal->api_config['client_id']) && !empty($paypal->api_config['client_secret']);
        $cryptomusConfigured = !empty($cryptomus->api_config['merchant_id']) && !empty($cryptomus->api_config['payment_key']);

        return view('member.payment.add', compact('paypalConfigured', 'cryptomusConfigured'));
    }

    /**
     * Store and redirect to payment gateway.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:paypal,cryptomus',
        ], [
            'amount.min' => 'Minimum deposit is $1.',
            'payment_method.required' => 'Please select a payment method.',
        ]);

        $amount = (float)$request->amount;
        $paymentMethod = $request->payment_method;
        $userId = auth()->id();

        // Generate invoice ID
        $invoice = 'DEP-' . strtoupper(Str::random(10));
        while (HistoryDeposit::where('invoice', $invoice)->exists()) {
            $invoice = 'DEP-' . strtoupper(Str::random(10));
        }

        // Create pending deposit history record
        $deposit = HistoryDeposit::create([
            'id_user' => $userId,
            'invoice' => $invoice,
            'amount' => $amount,
            'status_payment' => 'Pending',
            'detail_transaction' => [
                'payment_method' => ucfirst($paymentMethod),
                'amount_raw' => $amount
            ]
        ]);

        if ($paymentMethod === 'paypal') {
            return $this->processPaypal($deposit, $invoice, $amount);
        } else {
            return $this->processCryptomus($deposit, $invoice, $amount);
        }
    }

    /**
     * Process PayPal Payment
     */
    private function processPaypal(HistoryDeposit $deposit, $invoice, $amount)
    {
        $gateway = PaymentGateway::where('type', 'Paypal')->first();
        if (!$gateway || empty($gateway->api_config['client_id'])) {
            $this->logError("PayPal gateway configuration missing or incomplete for invoice {$invoice}");
            return back()->withErrors(['error' => 'PayPal gateway has not been configured by the administrator.']);
        }

        $config = $gateway->api_config;
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $mode = $config['mode'] ?? 'sandbox';

        $url = $mode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        try {
            // 1. Get Access Token
            $tokenResponse = Http::external()
                ->asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post("{$url}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if ($tokenResponse->failed()) {
                $this->logError("PayPal Token Request failed for invoice {$invoice}", [
                    'status' => $tokenResponse->status(),
                    'body' => $tokenResponse->body()
                ]);
                return back()->withErrors(['error' => 'Failed to connect to PayPal API. Please check the PayPal credentials in the admin panel.']);
            }

            $token = $tokenResponse->json()['access_token'] ?? null;
            if (!$token) {
                $this->logError("PayPal Access Token not found in response for invoice {$invoice}", [
                    'response' => $tokenResponse->json()
                ]);
                return back()->withErrors(['error' => 'Failed to retrieve PayPal access token.']);
            }

            // 2. Create Order
            $orderResponse = Http::external()
                ->withToken($token)
                ->post("{$url}/v2/checkout/orders", [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => 'USD',
                                'value' => number_format($amount, 2, '.', '')
                            ],
                            'description' => 'Deposit accounts #' . auth()->user()->username . ' (Invoice: ' . $invoice . ')'
                        ]
                    ],
                    'application_context' => [
                        'return_url' => route('member.payment.paypal.callback'),
                        'cancel_url' => route('member.payment.add'),
                    ]
                ]);

            if ($orderResponse->failed()) {
                $this->logError("PayPal Order Creation failed for invoice {$invoice}", [
                    'status' => $orderResponse->status(),
                    'body' => $orderResponse->body()
                ]);
                return back()->withErrors(['error' => 'Failed to create PayPal transaction: ' . ($orderResponse->json()['message'] ?? 'API Error')]);
            }

            $orderData = $orderResponse->json();
            $paypalOrderId = $orderData['id'];

            // Find approval link
            $approveUrl = null;
            foreach ($orderData['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approveUrl = $link['href'];
                    break;
                }
            }

            if (!$approveUrl) {
                $this->logError("PayPal Approve URL not found in response for invoice {$invoice}", [
                    'response' => $orderData
                ]);
                return back()->withErrors(['error' => 'Failed to retrieve PayPal payment link.']);
            }

            // Update details
            $deposit->update([
                'detail_transaction' => [
                    'paypal_order_id' => $paypalOrderId,
                    'payment_method' => 'Paypal',
                    'amount_raw' => $amount,
                    'redirect_url' => $approveUrl
                ]
            ]);

            return redirect()->away($approveUrl);

        } catch (\Exception $e) {
            $this->logError("Exception while processing PayPal payment for invoice {$invoice}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'A PayPal system error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Process Cryptomus Payment
     */
    private function processCryptomus(HistoryDeposit $deposit, $invoice, $amount)
    {
        $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
        if (!$gateway || empty($gateway->api_config['merchant_id'])) {
            $this->logError("Cryptomus gateway configuration missing or incomplete for invoice {$invoice}");
            return back()->withErrors(['error' => 'Cryptomus gateway has not been configured by the administrator.']);
        }

        $config = $gateway->api_config;
        $merchantId = $config['merchant_id'];
        $paymentKey = $config['payment_key'];

        try {
            $postData = [
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'USD',
                'order_id' => $invoice,
                'url_callback' => route('member.payment.cryptomus.callback'),
                'url_return' => route('member.payment.history'),
                'url_success' => route('member.payment.history'),
            ];

            // Signature generation: md5(base64_encode(json) . paymentKey)
            $jsonData = json_encode($postData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $sign = md5(base64_encode($jsonData) . $paymentKey);

            $response = Http::external()
                ->withHeaders([
                    'merchant' => $merchantId,
                    'sign' => $sign,
                ])
                ->withBody($jsonData, 'application/json')
                ->post('https://api.cryptomus.com/v1/payment');

            if ($response->failed()) {
                $this->logError("Cryptomus Payment API Request failed for invoice {$invoice}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return back()->withErrors(['error' => 'Failed to process Cryptomus payment: ' . ($response->json()['message'] ?? 'API Error')]);
            }

            $resData = $response->json();

            if (isset($resData['result']['url'])) {
                $checkoutUrl = $resData['result']['url'];
                $uuid = $resData['result']['uuid'] ?? '';

                $deposit->update([
                    'detail_transaction' => [
                        'cryptomus_uuid' => $uuid,
                        'payment_method' => 'Cryptomus',
                        'amount_raw' => $amount,
                        'redirect_url' => $checkoutUrl
                    ]
                ]);

                return redirect()->away($checkoutUrl);
            } else {
                $errorMsg = $resData['message'] ?? 'Invalid Cryptomus response.';
                $this->logError("Cryptomus Payment URL missing in response for invoice {$invoice}", [
                    'response' => $resData
                ]);
                return back()->withErrors(['error' => 'Cryptomus Error: ' . $errorMsg]);
            }

        } catch (\Exception $e) {
            $this->logError("Exception while processing Cryptomus payment for invoice {$invoice}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'A Cryptomus system error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * PayPal Payment Callback.
     */
    public function paypalCallback(Request $request)
    {
        $token = $request->query('token'); // PayPal Order ID is passed as token
        if (!$token) {
            $this->logError("PayPal Callback request has no token parameter", [
                'query_params' => $request->all()
            ]);
            return redirect()->route('member.payment.add')->withErrors(['error' => 'PayPal payment token is invalid or cancelled.']);
        }

        $deposit = HistoryDeposit::where('status_payment', 'Pending')
            ->whereJsonContains('detail_transaction->paypal_order_id', $token)
            ->first();

        if (!$deposit) {
            $this->logError("PayPal Callback: Pending deposit record not found for token {$token}");
            return redirect()->route('member.payment.history')->with('success', 'Your payment is being or has been processed.');
        }

        $gateway = PaymentGateway::where('type', 'Paypal')->first();
        $config = $gateway->api_config;
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $mode = $config['mode'] ?? 'sandbox';

        $url = $mode === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        try {
            // Get Access Token
            $tokenResponse = Http::external()
                ->asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post("{$url}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if ($tokenResponse->failed()) {
                $this->logError("PayPal Callback Token Request failed for deposit #{$deposit->invoice}", [
                    'status' => $tokenResponse->status(),
                    'body' => $tokenResponse->body()
                ]);
                return redirect()->route('member.payment.add')->withErrors(['error' => 'Failed to verify payment (API Auth Error).']);
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Capture the PayPal Order
            $captureResponse = Http::external()
                ->withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$url}/v2/checkout/orders/{$token}/capture");

            if ($captureResponse->failed()) {
                $this->logError("PayPal Callback Capture failed for deposit #{$deposit->invoice}", [
                    'status' => $captureResponse->status(),
                    'body' => $captureResponse->body()
                ]);
                return redirect()->route('member.payment.add')->withErrors(['error' => 'Payment failed to process by PayPal.']);
            }

            $captureData = $captureResponse->json();
            $status = $captureData['status'] ?? '';

            if ($status === 'COMPLETED') {
                DB::transaction(function () use ($deposit, $captureData) {
                    $user = User::findOrFail($deposit->id_user);
                    $user->balance += $deposit->amount;
                    $user->save();

                    $deposit->update([
                        'status_payment' => 'Success',
                        'detail_transaction' => array_merge($deposit->detail_transaction, [
                            'capture_details' => $captureData
                        ])
                    ]);
                });

                $redirectUrl = $deposit->detail_transaction['url_success'] ?? $deposit->detail_transaction['url_return'] ?? null;
                if ($this->isAllowedRedirectUrl($redirectUrl)) {
                    return redirect()->away($redirectUrl);
                }

                return redirect()->route('member.payment.history')->with('success', 'PayPal deposit of $' . number_format($deposit->amount, 2) . ' was successfully credited to your balance!');
            } else {
                $this->logError("PayPal Callback Capture completed with non-completed status: {$status} for deposit #{$deposit->invoice}", [
                    'response' => $captureData
                ]);
                $deposit->update([
                    'status_payment' => 'Error',
                    'detail_transaction' => array_merge($deposit->detail_transaction, [
                        'capture_details' => $captureData
                    ])
                ]);

                $redirectUrl = $deposit->detail_transaction['url_cancel'] ?? $deposit->detail_transaction['url_return'] ?? null;
                if ($this->isAllowedRedirectUrl($redirectUrl)) {
                    return redirect()->away($redirectUrl);
                }

                return redirect()->route('member.payment.add')->withErrors(['error' => 'PayPal payment not completed. Status: ' . $status]);
            }

        } catch (\Exception $e) {
            $this->logError("Exception in PayPal callback processing for deposit #{$deposit->invoice}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $redirectUrl = isset($deposit) ? ($deposit->detail_transaction['url_cancel'] ?? $deposit->detail_transaction['url_return'] ?? null) : null;
            if ($this->isAllowedRedirectUrl($redirectUrl)) {
                return redirect()->away($redirectUrl);
            }

            return redirect()->route('member.payment.add')->withErrors(['error' => 'An error occurred during PayPal callback processing: ' . $e->getMessage()]);
        }
    }

    /**
     * Cryptomus Webhook Callback.
     */
    public function cryptomusCallback(Request $request)
    {
        $data = $request->all();
        $receivedSign = $data['sign'] ?? null;

        if (!$receivedSign) {
            $this->logError("Cryptomus Callback received without signature header/body", [
                'payload' => $data
            ]);
            return response()->json(['message' => 'Missing signature'], 400);
        }

        // Remove signature from data for validation
        unset($data['sign']);

        $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
        if (!$gateway) {
            $this->logError("Cryptomus Callback: Gateway configuration not found in database");
            return response()->json(['message' => 'Gateway not found'], 404);
        }
        $paymentKey = $gateway->api_config['payment_key'] ?? '';

        // Verify signature: md5(base64_encode(json) . paymentKey)
        $jsonPayload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = md5(base64_encode($jsonPayload) . $paymentKey);

        if (!hash_equals($hash, $receivedSign)) {
            // Fallback try with escaped slash options
            $jsonPayloadAlternative = json_encode($data, JSON_UNESCAPED_UNICODE);
            $hashAlternative = md5(base64_encode($jsonPayloadAlternative) . $paymentKey);
            if (!hash_equals($hashAlternative, $receivedSign)) {
                $this->logError("Cryptomus Callback Signature verification failed", [
                    'received' => $receivedSign,
                    'calculated_standard' => $hash,
                    'calculated_alternative' => $hashAlternative,
                    'payload' => $data
                ]);
                return response()->json(['message' => 'Invalid signature'], 400);
            }
        }

        // Find the deposit record
        $invoice = $data['order_id'] ?? '';
        $deposit = HistoryDeposit::where('invoice', $invoice)->where('status_payment', 'Pending')->first();

        if (!$deposit) {
            $this->logError("Cryptomus Callback: Pending deposit record not found or already processed for invoice {$invoice}");
            return response()->json(['message' => 'Deposit record not found or already processed'], 200);
        }

        $status = strtolower($data['status'] ?? '');

        if ($status === 'paid' || $status === 'paid_over') {
            DB::transaction(function () use ($deposit, $data) {
                $user = User::findOrFail($deposit->id_user);
                $user->balance += $deposit->amount;
                $user->save();

                $deposit->update([
                    'status_payment' => 'Success',
                    'detail_transaction' => array_merge($deposit->detail_transaction ?? [], [
                        'callback_details' => $data
                    ])
                ]);
            });
            return response()->json(['message' => 'Payment successful and balance credited'], 200);
        } elseif (in_array($status, ['cancel', 'system_fail', 'fail'])) {
            $this->logError("Cryptomus Webhook reported cancelled/failed payment for invoice {$invoice}", [
                'status' => $status,
                'payload' => $data
            ]);
            $deposit->update([
                'status_payment' => 'Cancel',
                'detail_transaction' => array_merge($deposit->detail_transaction ?? [], [
                    'callback_details' => $data
                ])
            ]);
            return response()->json(['message' => 'Payment cancelled/failed'], 200);
        }

        $this->logError("Cryptomus Webhook received non-processed status: {$status} for invoice {$invoice}", [
            'payload' => $data
        ]);
        return response()->json(['message' => 'Webhook received with status: ' . $status], 200);
    }

    /**
     * Show deposit/payment history.
     */
    public function history()
    {
        $deposits = HistoryDeposit::where('id_user', auth()->id())
            ->orderBy('create_at', 'desc')
            ->get();
        return view('member.payment.history.index', compact('deposits'));
    }

    /**
     * Show deposit details.
     */
    public function showDeposit($invoice)
    {
        $deposit = HistoryDeposit::where('id_user', auth()->id())
            ->where('invoice', $invoice)
            ->firstOrFail();

        return view('member.payment.history.show', compact('deposit'));
    }

    /**
     * Sync deposit status with payment gateway.
     */
    public function syncDeposit($invoice)
    {
        $deposit = HistoryDeposit::where('id_user', auth()->id())
            ->where('invoice', $invoice)
            ->firstOrFail();

        if ($deposit->status_payment !== 'Pending') {
            return back()->with('success', 'This deposit has already been processed.');
        }

        $method = strtolower($deposit->detail_transaction['payment_method'] ?? '');

        if ($method === 'paypal') {
            $gateway = PaymentGateway::where('type', 'Paypal')->first();
            if (!$gateway || empty($gateway->api_config['client_id'])) {
                return back()->withErrors(['error' => 'PayPal gateway has not been configured by the administrator.']);
            }

            $config = $gateway->api_config;
            $clientId = $config['client_id'];
            $clientSecret = $config['client_secret'];
            $mode = $config['mode'] ?? 'sandbox';

            $url = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
            $paypalOrderId = $deposit->detail_transaction['paypal_order_id'] ?? null;

            if (!$paypalOrderId) {
                return back()->withErrors(['error' => 'PayPal Order ID is missing for this transaction.']);
            }

            try {
                // Get Access Token
                $tokenResponse = Http::external()
                    ->asForm()
                    ->withBasicAuth($clientId, $clientSecret)
                    ->post("{$url}/v1/oauth2/token", [
                        'grant_type' => 'client_credentials'
                    ]);

                if ($tokenResponse->failed()) {
                    return back()->withErrors(['error' => 'Failed to connect to PayPal API (Auth Error).']);
                }

                $accessToken = $tokenResponse->json()['access_token'];

                // Retrieve Order Details
                $orderResponse = Http::external()
                    ->withToken($accessToken)
                    ->get("{$url}/v2/checkout/orders/{$paypalOrderId}");

                if ($orderResponse->failed()) {
                    return back()->withErrors(['error' => 'Failed to retrieve PayPal order details.']);
                }

                $orderData = $orderResponse->json();
                $paypalStatus = $orderData['status'] ?? '';

                if ($paypalStatus === 'COMPLETED') {
                    DB::transaction(function () use ($deposit, $orderData) {
                        $user = User::findOrFail($deposit->id_user);
                        $user->balance += $deposit->amount;
                        $user->save();

                        $deposit->update([
                            'status_payment' => 'Success',
                            'detail_transaction' => array_merge($deposit->detail_transaction, [
                                'capture_details' => $orderData
                            ])
                        ]);
                    });

                    return back()->with('success', 'PayPal payment detected. Deposit successfully credited!');
                } elseif ($paypalStatus === 'APPROVED') {
                    // Try to capture APPROVED order
                    $captureResponse = Http::external()
                        ->withToken($accessToken)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post("{$url}/v2/checkout/orders/{$paypalOrderId}/capture");

                    if ($captureResponse->successful()) {
                        $captureData = $captureResponse->json();
                        if (($captureData['status'] ?? '') === 'COMPLETED') {
                            DB::transaction(function () use ($deposit, $captureData) {
                                $user = User::findOrFail($deposit->id_user);
                                $user->balance += $deposit->amount;
                                $user->save();

                                $deposit->update([
                                    'status_payment' => 'Success',
                                    'detail_transaction' => array_merge($deposit->detail_transaction, [
                                        'capture_details' => $captureData
                                    ])
                                ]);
                            });
                            return back()->with('success', 'PayPal payment captured. Deposit successfully credited!');
                        }
                    }
                    return back()->withErrors(['error' => 'PayPal payment is approved but failed to capture.']);
                } elseif (in_array($paypalStatus, ['VOIDED', 'EXPIRED'])) {
                    $deposit->update(['status_payment' => 'Failed']);
                    return back()->withErrors(['error' => 'PayPal order has expired or was voided. Status: ' . $paypalStatus]);
                } else {
                    return back()->withErrors(['error' => 'PayPal order status is: ' . $paypalStatus]);
                }

            } catch (\Exception $e) {
                $this->logError("Exception in syncDeposit PayPal: " . $e->getMessage());
                return back()->withErrors(['error' => 'An error occurred during synchronization: ' . $e->getMessage()]);
            }

        } elseif ($method === 'cryptomus') {
            $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
            if (!$gateway || empty($gateway->api_config['merchant_id'])) {
                return back()->withErrors(['error' => 'Cryptomus gateway has not been configured by the administrator.']);
            }

            $config = $gateway->api_config;
            $merchantId = $config['merchant_id'];
            $paymentKey = $config['payment_key'];

            try {
                $postData = [
                    'order_id' => $deposit->invoice
                ];

                $jsonData = json_encode($postData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $sign = md5(base64_encode($jsonData) . $paymentKey);

                $response = Http::external()
                    ->withHeaders([
                        'merchant' => $merchantId,
                        'sign' => $sign
                    ])
                    ->withBody($jsonData, 'application/json')
                    ->post('https://api.cryptomus.com/v1/payment/info');

                if ($response->failed()) {
                    return back()->withErrors(['error' => 'Failed to retrieve Cryptomus payment details.']);
                }

                $resData = $response->json();
                if (isset($resData['result']['status'])) {
                    $cryptoStatus = strtolower($resData['result']['status']);

                    if ($cryptoStatus === 'paid' || $cryptoStatus === 'paid_over') {
                        DB::transaction(function () use ($deposit, $resData) {
                            $user = User::findOrFail($deposit->id_user);
                            $user->balance += $deposit->amount;
                            $user->save();

                            $deposit->update([
                                'status_payment' => 'Success',
                                'detail_transaction' => array_merge($deposit->detail_transaction ?? [], [
                                    'callback_details' => $resData['result']
                                ])
                            ]);
                        });
                        return back()->with('success', 'Cryptomus payment detected. Deposit successfully credited!');
                    } elseif (in_array($cryptoStatus, ['cancel', 'system_fail', 'fail'])) {
                        $deposit->update([
                            'status_payment' => 'Cancel',
                            'detail_transaction' => array_merge($deposit->detail_transaction ?? [], [
                                                    'callback_details' => $resData['result']
                            ])
                        ]);
                        return back()->withErrors(['error' => 'Cryptomus payment failed or was cancelled. Status: ' . $cryptoStatus]);
                    } else {
                        return back()->withErrors(['error' => 'Cryptomus payment is still pending. Status: ' . $cryptoStatus]);
                    }
                } else {
                    return back()->withErrors(['error' => 'Invalid status response from Cryptomus API.']);
                }

            } catch (\Exception $e) {
                $this->logError("Exception in syncDeposit Cryptomus: " . $e->getMessage());
                return back()->withErrors(['error' => 'An error occurred during Cryptomus synchronization: ' . $e->getMessage()]);
            }
        }

        return back()->withErrors(['error' => 'Unknown payment method.']);
    }

    /**
     * Redirect to the payment gateway to pay the pending deposit.
     */
    public function payDeposit($invoice)
    {
        $deposit = HistoryDeposit::where('id_user', auth()->id())
            ->where('invoice', $invoice)
            ->firstOrFail();

        if (strtolower($deposit->status_payment) !== 'pending') {
            return redirect()->route('member.payment.history')->withErrors(['error' => 'This deposit is not pending.']);
        }

        $redirectUrl = $deposit->detail_transaction['redirect_url'] ?? null;
        if ($redirectUrl) {
            return redirect()->away($redirectUrl);
        }

        // Fallback/Re-generate if redirectUrl is missing
        $method = strtolower($deposit->detail_transaction['payment_method'] ?? '');
        if ($method === 'paypal') {
            $paypalOrderId = $deposit->detail_transaction['paypal_order_id'] ?? null;
            if ($paypalOrderId) {
                $gateway = PaymentGateway::where('type', 'Paypal')->first();
                $mode = $gateway ? ($gateway->api_config['mode'] ?? 'sandbox') : 'sandbox';
                $domain = $mode === 'live' ? 'www.paypal.com' : 'www.sandbox.paypal.com';
                return redirect()->away("https://{$domain}/checkoutnow?token={$paypalOrderId}");
            }
        }

        // If we cannot find a checkout URL, redirect to the show page
        return redirect()->route('member.payment.history.show', $invoice)->withErrors(['error' => 'Could not retrieve payment link automatically. Please check status or try again.']);
    }
}
