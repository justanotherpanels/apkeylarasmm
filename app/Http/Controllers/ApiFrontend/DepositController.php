<?php

namespace App\Http\Controllers\ApiFrontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PaymentGateway;
use App\Models\HistoryDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DepositController extends Controller
{
    /**
     * Create a deposit transaction via API.
     */
    public function store(Request $request)
    {
        // 1. Authenticate user via API key
        $apiKey = $request->input('api_key') ?: $request->input('key');
        
        if (!$apiKey) {
            $authHeader = $request->header('Authorization');
            if ($authHeader) {
                if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                    $apiKey = $matches[1];
                } else {
                    $apiKey = $authHeader;
                }
            }
        }

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key is missing.'
            ], 401);
        }

        $user = User::where('api_key', $apiKey)->where('status', 'Active')->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key.'
            ], 401);
        }

        // 2. Validate request parameters
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:paypal,cryptomus',
            'url_return' => 'nullable|url',
            'url_success' => 'nullable|url',
            'url_cancel' => 'nullable|url',
        ], [
            'amount.min' => 'Minimum deposit is $1.',
            'payment_method.required' => 'Please select a payment method.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $amount = (float)$request->amount;
        $paymentMethod = strtolower($request->payment_method);
        $userId = $user->id;

        // Generate unique invoice ID
        $invoice = 'DEP-' . strtoupper(Str::random(10));
        while (HistoryDeposit::where('invoice', $invoice)->exists()) {
            $invoice = 'DEP-' . strtoupper(Str::random(10));
        }

        // Prepare details
        $detailTransaction = [
            'payment_method' => ucfirst($paymentMethod),
            'amount_raw' => $amount,
        ];
        if ($request->has('url_return')) {
            $detailTransaction['url_return'] = $request->url_return;
        }
        if ($request->has('url_success')) {
            $detailTransaction['url_success'] = $request->url_success;
        }
        if ($request->has('url_cancel')) {
            $detailTransaction['url_cancel'] = $request->url_cancel;
        }

        // Create pending deposit history record
        $deposit = HistoryDeposit::create([
            'id_user' => $userId,
            'invoice' => $invoice,
            'amount' => $amount,
            'status_payment' => 'Pending',
            'detail_transaction' => $detailTransaction
        ]);

        if ($paymentMethod === 'paypal') {
            return $this->processPaypal($deposit, $invoice, $amount, $user, $request);
        } else {
            return $this->processCryptomus($deposit, $invoice, $amount, $request);
        }
    }

    private function processPaypal(HistoryDeposit $deposit, $invoice, $amount, $user, Request $request)
    {
        $gateway = PaymentGateway::where('type', 'Paypal')->first();
        if (!$gateway || empty($gateway->api_config['client_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'PayPal gateway has not been configured by the administrator.'
            ], 500);
        }

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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to connect to PayPal API. Please check configuration.'
                ], 502);
            }

            $token = $tokenResponse->json()['access_token'] ?? null;
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to retrieve PayPal access token.'
                ], 502);
            }

            // Create Order
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
                        'cancel_url' => route('member.payment.add'),
                    ]
                ]);

            if ($orderResponse->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create PayPal transaction.'
                ], 502);
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to retrieve PayPal payment link.'
                ], 502);
            }

            // Update details
            $deposit->update([
                'detail_transaction' => array_merge($deposit->detail_transaction, [
                    'paypal_order_id' => $paypalOrderId,
                ])
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'PayPal transaction created successfully.',
                'data' => [
                    'invoice' => $invoice,
                    'payment_method' => 'Paypal',
                    'amount' => number_format($amount, 2, '.', ''),
                    'redirect_url' => $approveUrl
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'A PayPal system error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processCryptomus(HistoryDeposit $deposit, $invoice, $amount, Request $request)
    {
        $gateway = PaymentGateway::where('type', 'Cryptomus')->first();
        if (!$gateway || empty($gateway->api_config['merchant_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cryptomus gateway has not been configured by the administrator.'
            ], 500);
        }

        $config = $gateway->api_config;
        $merchantId = $config['merchant_id'];
        $paymentKey = $config['payment_key'];

        try {
            $urlReturn = $request->input('url_return') ?: route('member.payment.history');
            $urlSuccess = $request->input('url_success') ?: $urlReturn;

            $postData = [
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'USD',
                'order_id' => $invoice,
                'url_callback' => route('member.payment.cryptomus.callback'),
                'url_return' => $urlReturn,
                'url_success' => $urlSuccess,
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to process Cryptomus payment: ' . ($response->json()['message'] ?? 'API Error')
                ], 502);
            }

            $resData = $response->json();

            if (isset($resData['result']['url'])) {
                $checkoutUrl = $resData['result']['url'];
                $uuid = $resData['result']['uuid'] ?? '';

                $deposit->update([
                    'detail_transaction' => array_merge($deposit->detail_transaction, [
                        'cryptomus_uuid' => $uuid,
                    ])
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cryptomus transaction created successfully.',
                    'data' => [
                        'invoice' => $invoice,
                        'payment_method' => 'Cryptomus',
                        'amount' => number_format($amount, 2, '.', ''),
                        'redirect_url' => $checkoutUrl
                    ]
                ], 201);
            } else {
                $errorMsg = $resData['message'] ?? 'Invalid Cryptomus response.';
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cryptomus Error: ' . $errorMsg
                ], 502);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'A Cryptomus system error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve Deposit History via API.
     */
    public function history(Request $request)
    {
        // 1. Authenticate user via API key
        $apiKey = $request->input('api_key') ?: $request->input('key');
        
        if (!$apiKey) {
            $authHeader = $request->header('Authorization');
            if ($authHeader) {
                if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                    $apiKey = $matches[1];
                } else {
                    $apiKey = $authHeader;
                }
            }
        }

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key is missing.'
            ], 401);
        }

        $user = User::where('api_key', $apiKey)->where('status', 'Active')->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key.'
            ], 401);
        }

        // 2. Validate input parameters
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'status' => 'nullable|string',
            'search' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $limit = $request->input('limit', 10);
        $status = $request->input('status');
        $search = $request->input('search');

        // 3. Build query
        $query = HistoryDeposit::where('id_user', $user->id)
            ->orderBy('create_at', 'desc');

        if ($status) {
            $query->where(function ($q) use ($status) {
                $q->where('status_payment', $status)
                  ->orWhere('status_payment', ucfirst(strtolower($status)));
            });
        }

        if ($search) {
            $query->where('invoice', 'like', "%{$search}%");
        }

        // 4. Paginate
        $deposits = $query->paginate($limit);

        // 5. Format items
        $formattedDeposits = collect($deposits->items())->map(function ($deposit) {
            return [
                'id' => $deposit->id,
                'invoice' => $deposit->invoice,
                'amount' => number_format($deposit->amount, 2, '.', ''),
                'status' => $deposit->status_payment,
                'payment_method' => $deposit->detail_transaction['payment_method'] ?? 'Unknown',
                'created_at' => $deposit->create_at ? $deposit->create_at->toDateTimeString() : null
            ];
        });

        // 6. Return response
        return response()->json([
            'status' => 'success',
            'data' => $formattedDeposits,
            'pagination' => [
                'total' => $deposits->total(),
                'per_page' => $deposits->perPage(),
                'current_page' => $deposits->currentPage(),
                'last_page' => $deposits->lastPage(),
                'from' => $deposits->firstItem(),
                'to' => $deposits->lastItem()
            ]
        ]);
    }
}
