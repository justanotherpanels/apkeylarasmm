<?php

namespace App\Http\Controllers\ApiMobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CategorySmm;
use App\Models\ServiceSmm;
use App\Models\OrderSmm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Helper to authenticate mobile client
     */
    private function authUser(Request $request)
    {
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
            return null;
        }

        return User::where('api_key', $apiKey)->where('status', 'Active')->first();
    }

    /**
     * Retrieve SMM Categories for Mobile.
     */
    public function categories(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $categories = CategorySmm::where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'code' => $cat->code
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Retrieve SMM Services for Mobile.
     */
    public function services(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:category_smm,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = ServiceSmm::with('category')->where('status', 'Active');

        if ($request->has('category_id')) {
            $query->where('id_category_smm', $request->category_id);
        }

        $services = $query->orderBy('id_category_smm', 'asc')
            ->orderBy('price_sale', 'asc')
            ->get()
            ->map(function ($svc) use ($user) {
                // Return price based on reseller status
                $price = $user->is_seller ? $svc->price_reseller : $svc->price_sale;

                return [
                    'id' => $svc->id,
                    'category_id' => $svc->id_category_smm,
                    'category_name' => $svc->category ? $svc->category->name : 'General',
                    'name' => $svc->name_service,
                    'min_order' => $svc->min_order,
                    'max_order' => $svc->max_order,
                    'price' => number_format($price, 4, '.', ''),
                    'type' => $svc->type,
                    'desc' => $svc->desc ?? '',
                    'refill' => (bool)$svc->refill
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

    /**
     * Place SMM Order via Mobile.
     */
    public function store(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        // Support alias parameters for mobile clients
        if ($request->has('service') && !$request->has('id_service_smm')) {
            $serviceVal = $request->input('service');
            $serviceObj = ServiceSmm::where('id', $serviceVal)->orWhere('pid', $serviceVal)->first();
            if ($serviceObj) {
                $request->merge([
                    'id_service_smm' => $serviceObj->id,
                    'id_category_smm' => $serviceObj->id_category_smm
                ]);
            }
        } elseif ($request->has('id_service_smm') && !$request->has('id_category_smm')) {
            $serviceObj = ServiceSmm::find($request->input('id_service_smm'));
            if ($serviceObj) {
                $request->merge([
                    'id_category_smm' => $serviceObj->id_category_smm
                ]);
            }
        }
        if ($request->has('link') && !$request->has('target')) {
            $request->merge(['target' => $request->input('link')]);
        }
        if ($request->has('quantity') && !$request->has('amount')) {
            $request->merge(['amount' => $request->input('quantity')]);
        }

        // Validate basic inputs
        $validator = Validator::make($request->all(), [
            'id_category_smm' => 'required|exists:category_smm,id',
            'id_service_smm' => 'required|exists:service_smm,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = ServiceSmm::with('api')->findOrFail($request->id_service_smm);
        $type = $service->type;
        $rules = [];

        // Validate details according to SMM service type
        if ($type === 'Default' || $type === 'Package') {
            $rules['target'] = 'required|string';
            $rules['amount'] = 'required|integer|min:' . $service->min_order . '|max:' . $service->max_order;
        } elseif ($type === 'Custom Comments') {
            $rules['target'] = 'required|string';
            $rules['comments'] = 'required|string';
        } elseif ($type === 'Poll') {
            $rules['target'] = 'required|string';
            $rules['amount'] = 'required|integer|min:' . $service->min_order . '|max:' . $service->max_order;
            $rules['answer_number'] = 'required|string';
        } elseif ($type === 'Subscriptions') {
            $rules['target'] = 'required|string';
            $rules['min'] = 'required|integer|min:1';
            $rules['max'] = 'required|integer|gte:min';
            $rules['posts'] = 'required|integer|min:1';
            $rules['delay'] = 'required|in:0,5,10,15,30,60,90';
        }

        $inputValidator = Validator::make($request->all(), $rules);
        if ($inputValidator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $inputValidator->errors()
            ], 422);
        }

        // Calculate amount and final price
        $amount = 0;
        $totalPrice = 0;
        $pricePerThousand = $user->is_seller ? $service->price_reseller : $service->price_sale;

        if ($type === 'Default' || $type === 'Package' || $type === 'Poll') {
            $amount = intval($request->amount);
            $totalPrice = ($amount * $pricePerThousand) / 1000;
        } elseif ($type === 'Custom Comments') {
            $commentsList = preg_split('/\r\n|\r|\n/', trim($request->comments));
            $commentsList = array_filter($commentsList);
            $amount = count($commentsList);
            
            if ($amount < $service->min_order || $amount > $service->max_order) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'comments' => ["Comments count must be between {$service->min_order} and {$service->max_order}."]
                    ]
                ], 422);
            }
            $totalPrice = ($amount * $pricePerThousand) / 1000;
        } elseif ($type === 'Subscriptions') {
            $posts = intval($request->posts);
            $max = intval($request->max);
            $amount = $posts * $max;
            $totalPrice = ($posts * $max * $pricePerThousand) / 1000;
        }

        // Check balance
        if ($user->balance < $totalPrice) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient balance to place this order.'
            ], 400);
        }

        // Check API provider setup
        $api = $service->api;
        if (!$api || !$api->url || !$api->api_key) {
            return response()->json([
                'status' => 'error',
                'message' => 'The API provider for this service is not configured correctly.'
            ], 500);
        }

        // Call SMM Provider API
        $postFields = [
            'action' => 'add',
            'key' => $api->api_key,
            'service' => $service->pid,
        ];

        if ($type === 'Default' || $type === 'Package') {
            $postFields['link'] = $request->target;
            $postFields['quantity'] = $amount;
        } elseif ($type === 'Custom Comments') {
            $postFields['link'] = $request->target;
            $commentsList = preg_split('/\r\n|\r|\n/', trim($request->comments));
            $commentsList = array_filter($commentsList);
            $postFields['comments'] = implode("\n", $commentsList);
        } elseif ($type === 'Poll') {
            $postFields['link'] = $request->target;
            $postFields['quantity'] = $amount;
            $postFields['answer_number'] = $request->answer_number;
        } elseif ($type === 'Subscriptions') {
            $postFields['username'] = $request->target;
            $postFields['min'] = $request->min;
            $postFields['max'] = $request->max;
            $postFields['posts'] = $request->posts;
            $postFields['post'] = $request->posts;
            $postFields['delay'] = $request->delay;
        }

        try {
            $response = Http::external()->asForm()->post($api->url, $postFields);

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to connect to the SMM API Provider.'
                ], 502);
            }

            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'success') {
                $orderId = $data['order'];
                $invoice = '';
                $balanceAfter = 0;

                // Process database updates inside transaction
                DB::transaction(function () use ($user, $totalPrice, $service, $api, $orderId, $request, $amount, &$invoice, &$balanceAfter) {
                    $userRefresh = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

                    if ($userRefresh->balance < $totalPrice) {
                        throw new \RuntimeException('INSUFFICIENT_BALANCE');
                    }

                    $userRefresh->balance -= $totalPrice;
                    $userRefresh->save();
                    $balanceAfter = (float) $userRefresh->balance;

                    // Generate unique invoice
                    $invoice = 'INV-' . strtoupper(Str::random(10));
                    while (OrderSmm::where('invoice', $invoice)->exists()) {
                        $invoice = 'INV-' . strtoupper(Str::random(10));
                    }

                    // Save SMM Order
                    OrderSmm::create([
                        'id_user' => $userRefresh->id,
                        'id_service_smm' => $service->id,
                        'id_api_smm' => $api->id,
                        'sid' => $orderId,
                        'invoice' => $invoice,
                        'target' => $request->target,
                        'amount' => $amount,
                        'price_api' => ($amount * $service->price_api) / 1000,
                        'price_sale' => $totalPrice,
                        'price_reseller' => ($amount * $service->price_reseller) / 1000,
                        'status_order' => 'Pending',
                        'start_count' => 0,
                        'remains' => $amount,
                        'refill' => $service->refill,
                    ]);
                });

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully!',
                    'data' => [
                        'invoice' => $invoice,
                        'order_id' => $orderId,
                        'amount' => $amount,
                        'price' => number_format($totalPrice, 4, '.', ''),
                        'balance_remaining' => number_format($balanceAfter, 4, '.', ''),
                        'min_order' => $service->min_order,
                        'max_order' => $service->max_order,
                        'description' => $service->desc ?? '',
                    ]
                ], 201);
            } else {
                $errorMsg = $data['error'] ?? 'Unknown SMM provider error.';
                return response()->json([
                    'status' => 'error',
                    'message' => 'SMM Provider Error: ' . $errorMsg
                ], 400);
            }
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient balance to place this order.'
                ], 400);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'System error occurred: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'System error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve SMM Order History for Mobile.
     */
    public function history(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        // Validate input parameters
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

        // Build query
        $query = OrderSmm::with('service')
            ->where('id_user', $user->id)
            ->orderBy('create_at', 'desc');

        if ($status) {
            $query->where(function ($q) use ($status) {
                $q->where('status_order', $status)
                  ->orWhere('status_order', ucfirst(strtolower($status)));
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhere('target', 'like', "%{$search}%");
            });
        }

        // Paginate
        $orders = $query->paginate($limit);

        // Format history items
        $formattedOrders = collect($orders->items())->map(function ($order) {
            return [
                'id' => $order->id,
                'invoice' => $order->invoice,
                'service_name' => $order->service ? $order->service->name_service : 'Unknown Service',
                'target' => $order->target,
                'amount' => $order->amount,
                'price' => number_format($order->price_sale, 4, '.', ''),
                'status' => $order->status_order,
                'remains' => $order->remains,
                'start_count' => $order->start_count,
                'created_at' => $order->create_at ? $order->create_at->toDateTimeString() : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedOrders,
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem()
            ]
        ]);
    }
}
