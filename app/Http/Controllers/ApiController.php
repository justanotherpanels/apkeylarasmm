<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceSmm;
use App\Models\OrderSmm;
use App\Models\HistoryDeposit;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Handle SMM API v2 Requests.
     */
    public function handle(Request $request)
    {
        // 1. Authenticate by key
        $apiKey = $request->input('key');
        if (empty($apiKey)) {
            return response()->json(['error' => 'API key is missing.'], 401);
        }

        $user = User::where('api_key', $apiKey)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid API key.'], 401);
        }

        // 2. Route by action
        $action = $request->input('action');
        if (empty($action)) {
            return response()->json(['error' => 'Action parameter is missing.'], 400);
        }

        switch ($action) {
            case 'services':
                return $this->getServices();
            case 'balance':
                return $this->getBalance($user);
            case 'add':
                return $this->placeOrder($request, $user);
            case 'status':
                return $this->getOrderStatus($request, $user);
            case 'create_deposit':
                return $this->createDeposit($request, $user);
            case 'deposit_status':
                return $this->getDepositStatus($request, $user);
            default:
                return response()->json(['error' => 'Invalid action.'], 400);
        }
    }

    /**
     * Action: services
     */
    private function getServices()
    {
        $services = ServiceSmm::with('category')
            ->where('status', 'Active')
            ->get()
            ->map(function ($s) {
                return [
                    'service' => $s->id,
                    'name' => $s->name_service,
                    'type' => $s->type,
                    'rate' => number_format($s->price_sale, 2, '.', ''),
                    'min' => $s->min_order,
                    'max' => $s->max_order,
                    'category' => $s->category ? $s->category->name : 'Uncategorized',
                    'refill' => (bool)$s->refill,
                    'desc' => $s->desc ?? '',
                ];
            });

        return response()->json($services);
    }

    /**
     * Action: balance
     */
    private function getBalance(User $user)
    {
        return response()->json([
            'status' => 'success',
            'balance' => number_format($user->balance, 4, '.', ''),
            'currency' => 'USD'
        ]);
    }

    /**
     * Action: add (Place Order)
     */
    private function placeOrder(Request $request, User $user)
    {
        $serviceId = $request->input('service');
        $link = $request->input('link');
        $quantity = intval($request->input('quantity'));

        if (empty($serviceId) || empty($link) || empty($quantity)) {
            return response()->json(['error' => 'Missing parameters: service, link, and quantity are required.'], 400);
        }

        $service = ServiceSmm::with('api')
            ->where('status', 'Active')
            ->find($serviceId);

        if (!$service) {
            return response()->json(['error' => 'Service not found or inactive.'], 404);
        }

        // Validate Quantity
        if ($quantity < $service->min_order || $quantity > $service->max_order) {
            return response()->json(['error' => "Quantity must be between {$service->min_order} and {$service->max_order}."], 400);
        }

        // Calculate Cost
        $totalPrice = ($quantity * $service->price_sale) / 1000;

        if ($user->balance < $totalPrice) {
            return response()->json(['error' => 'Insufficient balance.'], 400);
        }

        $api = $service->api;
        if (!$api || !$api->url || !$api->api_key) {
            return response()->json(['error' => 'Provider API connection is not configured correctly.'], 500);
        }

        // Call provider SMM API
        try {
            $postFields = [
                'action' => 'add',
                'key' => $api->api_key,
                'service' => $service->pid,
                'link' => $link,
                'quantity' => $quantity
            ];

            $response = Http::external()->asForm()->post($api->url, $postFields);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to connect to API provider.'], 502);
            }

            $resData = $response->json();

            if (isset($resData['status']) && $resData['status'] === 'success') {
                $providerOrderId = $resData['order'];

                // Deduct balance and create order records
                DB::transaction(function () use ($user, $totalPrice, $service, $api, $providerOrderId, $link, $quantity) {
                    $userRefresh = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

                    if ($userRefresh->balance < $totalPrice) {
                        throw new \RuntimeException('INSUFFICIENT_BALANCE');
                    }

                    $userRefresh->balance -= $totalPrice;
                    $userRefresh->save();

                    // Generate unique invoice
                    $invoice = 'INV-' . strtoupper(Str::random(10));
                    while (OrderSmm::where('invoice', $invoice)->exists()) {
                        $invoice = 'INV-' . strtoupper(Str::random(10));
                    }

                    OrderSmm::create([
                        'id_user' => $userRefresh->id,
                        'id_service_smm' => $service->id,
                        'id_api_smm' => $api->id,
                        'sid' => $providerOrderId,
                        'invoice' => $invoice,
                        'target' => $link,
                        'amount' => $quantity,
                        'price_api' => ($quantity * $service->price_api) / 1000,
                        'price_sale' => $totalPrice,
                        'price_reseller' => ($quantity * $service->price_reseller) / 1000,
                        'status_order' => 'Pending',
                        'start_count' => 0,
                        'remains' => $quantity,
                        'refill' => $service->refill,
                    ]);
                });

                return response()->json([
                    'status' => 'success',
                    'order' => $providerOrderId
                ]);
            } else {
                return response()->json(['error' => 'Provider Error: ' . ($resData['error'] ?? 'Unknown Error')], 400);
            }

        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return response()->json(['error' => 'Insufficient balance.'], 400);
            }
            return response()->json(['error' => 'System error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'System error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Action: status (Check SMM Order status)
     */
    private function getOrderStatus(Request $request, User $user)
    {
        $orderId = $request->input('order');
        if (empty($orderId)) {
            return response()->json(['error' => 'Order parameter is required.'], 400);
        }

        // Find by SMM Order ID or internal invoice belonging to user
        $order = OrderSmm::where('id_user', $user->id)
            ->where(function ($q) use ($orderId) {
                $q->where('sid', $orderId)->orWhere('invoice', $orderId);
            })
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        return response()->json([
            'status' => ucfirst($order->status_order),
            'charge' => number_format($order->price_sale, 4, '.', ''),
            'start_count' => $order->start_count,
            'remains' => $order->remains,
            'currency' => 'USD'
        ]);
    }

    /**
     * Action: create_deposit (API Deposit Creation)
     */
    private function createDeposit(Request $request, User $user)
    {
        $amount = floatval($request->input('amount'));
        $method = strtolower($request->input('payment_method'));

        if (empty($amount) || $amount < 1) {
            return response()->json(['error' => 'Amount must be a numeric value of at least 1.00 USD.'], 400);
        }

        if (!in_array($method, ['paypal', 'cryptomus'])) {
            return response()->json(['error' => 'Invalid payment_method. Allowed: paypal, cryptomus'], 400);
        }

        // Generate Invoice
        $invoice = 'DEP-' . strtoupper(Str::random(10));
        while (HistoryDeposit::where('invoice', $invoice)->exists()) {
            $invoice = 'DEP-' . strtoupper(Str::random(10));
        }

        // Create pending record
        $deposit = HistoryDeposit::create([
            'id_user' => $user->id,
            'invoice' => $invoice,
            'amount' => $amount,
            'status_payment' => 'Pending',
            'detail_transaction' => [
                'payment_method' => ucfirst($method),
                'amount_raw' => $amount
            ]
        ]);

        if ($method === 'paypal') {
            return $this->apiProcessPaypal($deposit, $invoice, $amount, $user);
        } else {
            return $this->apiProcessCryptomus($deposit, $invoice, $amount);
        }
    }

    /**
     * Action: deposit_status
     */
    private function getDepositStatus(Request $request, User $user)
    {
        $invoice = $request->input('invoice');
        if (empty($invoice)) {
            return response()->json(['error' => 'Invoice parameter is required.'], 400);
        }

        $deposit = HistoryDeposit::where('id_user', $user->id)
            ->where('invoice', $invoice)
            ->first();

        if (!$deposit) {
            return response()->json(['error' => 'Deposit record not found.'], 404);
        }

        return response()->json([
            'status' => $deposit->status_payment,
            'amount' => number_format($deposit->amount, 2, '.', ''),
            'payment_method' => $deposit->detail_transaction['payment_method'] ?? 'Unknown',
            'created_at' => $deposit->create_at ? $deposit->create_at->format('Y-m-d H:i:s') : null,
            'currency' => 'USD'
        ]);
    }

    /**
     * Internal: Process PayPal API Creation
     */
    private function apiProcessPaypal(HistoryDeposit $deposit, $invoice, $amount, User $user)
    {
        $gateway = PaymentGateway::where('type', 'Paypal')->first();
        if (!$gateway || empty($gateway->api_config['client_id'])) {
            return response()->json(['error' => 'PayPal gateway not configured.'], 500);
        }

        $config = $gateway->api_config;
        $clientId = $config['client_id'];
        $clientSecret = $config['client_secret'];
        $mode = $config['mode'] ?? 'sandbox';

        $url = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        try {
            $tokenResponse = Http::external()
                ->asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post("{$url}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials'
                ]);

            if ($tokenResponse->failed()) {
                return response()->json(['error' => 'Failed to connect to PayPal API.'], 502);
            }

            $token = $tokenResponse->json()['access_token'] ?? null;
            if (!$token) {
                return response()->json(['error' => 'Failed to retrieve PayPal access token.'], 502);
            }

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
                            'description' => 'Deposit accounts #' . $user->username . ' (Invoice: ' . $invoice . ')'
                        ]
                    ],
                    'application_context' => [
                        'return_url' => route('member.payment.paypal.callback'),
                        'cancel_url' => route('member.payment.history')
                    ]
                ]);

            if ($orderResponse->failed()) {
                return response()->json(['error' => 'Failed to create PayPal order.'], 502);
            }

            $orderData = $orderResponse->json();
            $paypalOrderId = $orderData['id'] ?? null;
            $approveUrl = null;

            if (isset($orderData['links'])) {
                foreach ($orderData['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approveUrl = $link['href'];
                        break;
                    }
                }
            }

            if (!$approveUrl) {
                return response()->json(['error' => 'PayPal checkout URL not found.'], 502);
            }

            $deposit->update([
                'detail_transaction' => [
                    'paypal_order_id' => $paypalOrderId,
                    'payment_method' => 'Paypal',
                    'amount_raw' => $amount
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'invoice' => $invoice,
                'checkout_url' => $approveUrl
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'PayPal setup error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Internal: Process Cryptomus API Creation
     */
    private function apiProcessCryptomus(HistoryDeposit $deposit, $invoice, $amount)
    {
        $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
        if (!$gateway || empty($gateway->api_config['merchant_id'])) {
            return response()->json(['error' => 'Cryptomus gateway not configured.'], 500);
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
                return response()->json(['error' => 'Failed to communicate with Cryptomus API.'], 502);
            }

            $resData = $response->json();

            if (isset($resData['result']['url'])) {
                $checkoutUrl = $resData['result']['url'];
                $uuid = $resData['result']['uuid'] ?? '';

                $deposit->update([
                    'detail_transaction' => [
                        'cryptomus_uuid' => $uuid,
                        'payment_method' => 'Cryptomus',
                        'amount_raw' => $amount
                    ]
                ]);

                return response()->json([
                    'status' => 'success',
                    'invoice' => $invoice,
                    'checkout_url' => $checkoutUrl
                ]);
            } else {
                return response()->json(['error' => 'Cryptomus API error: ' . ($resData['message'] ?? 'Invalid response')], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Cryptomus setup error: ' . $e->getMessage()], 500);
        }
    }
}
