<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\CategorySmm;
use App\Models\ServiceSmm;
use App\Models\OrderSmm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SmmController extends Controller
{
    /**
     * Show the member dashboard with real statistics and order overview chart.
     */
    public function dashboard()
    {
        $userId = auth()->id();

        // 1. Total Spending (all orders)
        $totalSpent = OrderSmm::where('id_user', $userId)->sum('price_sale');

        // 2. Total Orders count
        $totalOrders = OrderSmm::where('id_user', $userId)->count();

        // 3. Monthly percentage calculation
        $thisMonthSpent = OrderSmm::where('id_user', $userId)
            ->whereMonth('create_at', now()->month)
            ->whereYear('create_at', now()->year)
            ->sum('price_sale');

        $lastMonthSpent = OrderSmm::where('id_user', $userId)
            ->whereMonth('create_at', now()->subMonth()->month)
            ->whereYear('create_at', now()->subMonth()->year)
            ->sum('price_sale');

        $percentageChange = 0;
        if ($lastMonthSpent > 0) {
            $percentageChange = (($thisMonthSpent - $lastMonthSpent) / $lastMonthSpent) * 100;
        } elseif ($thisMonthSpent > 0) {
            $percentageChange = 100;
        }

        // 4. Chart Data: Group daily spending
        $rawChartData = OrderSmm::where('id_user', $userId)
            ->selectRaw('DATE(create_at) as date, SUM(price_sale) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartData = [];
        foreach ($rawChartData as $item) {
            $chartData[] = [
                \Carbon\Carbon::parse($item->date)->timestamp * 1000,
                round((float)$item->total, 2)
            ];
        }

        // If no data, supply some dummy/empty data or a default point
        if (empty($chartData)) {
            $chartData[] = [
                now()->timestamp * 1000,
                0
            ];
        }

        // 5. Recent Orders (last 5 SMM orders)
        $recentOrders = OrderSmm::with('service')
            ->where('id_user', $userId)
            ->orderBy('create_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Deposits calculation
        $totalDeposit = \App\Models\HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->sum('amount');

        $successDepositsCount = \App\Models\HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->count();

        $thisMonthDeposit = \App\Models\HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->whereMonth('create_at', now()->month)
            ->whereYear('create_at', now()->year)
            ->sum('amount');

        $lastMonthDeposit = \App\Models\HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->whereMonth('create_at', now()->subMonth()->month)
            ->whereYear('create_at', now()->subMonth()->year)
            ->sum('amount');

        $depositPercentageChange = 0;
        if ($lastMonthDeposit > 0) {
            $depositPercentageChange = (($thisMonthDeposit - $lastMonthDeposit) / $lastMonthDeposit) * 100;
        } elseif ($thisMonthDeposit > 0) {
            $depositPercentageChange = 100;
        }

        // 7. Deposit Chart Data: Group daily successful deposits
        $rawDepositChartData = \App\Models\HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->selectRaw('DATE(create_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $depositChartData = [];
        foreach ($rawDepositChartData as $item) {
            $depositChartData[] = [
                \Carbon\Carbon::parse($item->date)->timestamp * 1000,
                round((float)$item->total, 2)
            ];
        }

        if (empty($depositChartData)) {
            $depositChartData[] = [
                now()->timestamp * 1000,
                0
            ];
        }

        // 8. Active Orders Count (Pending, Processing, In Progress)
        $activeOrdersCount = OrderSmm::where('id_user', $userId)
            ->whereIn('status_order', ['Pending', 'Processing', 'In Progress'])
            ->count();

        return view('member.index', compact(
            'totalSpent', 
            'totalOrders', 
            'percentageChange', 
            'chartData', 
            'recentOrders',
            'totalDeposit',
            'successDepositsCount',
            'depositPercentageChange',
            'depositChartData',
            'activeOrdersCount'
        ));
    }

    /**
     * Show the member order form with categories.
     */
    public function orderForm(Request $request)
    {
        $categories = CategorySmm::where('status', 'Active')->orderBy('name', 'asc')->get();
        $selectedService = null;
        if ($request->has('service_id')) {
            $selectedService = ServiceSmm::where('status', 'Active')->find($request->service_id);
        }
        return view('member.smm.order.index', compact('categories', 'selectedService'));
    }

    /**
     * Get active SMM services for a given category.
     */
    public function getServices($categoryId)
    {
        $user = auth()->user();
        $isSeller = $user ? $user->is_seller : false;

        $services = ServiceSmm::where('id_category_smm', $categoryId)
            ->where('status', 'Active')
            ->orderBy('name_service', 'asc')
            ->get();

        if ($isSeller) {
            $services->map(function ($service) {
                $service->price_sale = $service->price_reseller;
                return $service;
            });
        }

        return response()->json($services);
    }

    /**
     * Process SMM Order submission.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'id_category_smm' => 'required|exists:category_smm,id',
            'id_service_smm' => 'required|exists:service_smm,id',
        ]);

        $service = ServiceSmm::with('api')->findOrFail($request->id_service_smm);
        $user = auth()->user();

        // Validate inputs depending on type
        $type = $service->type;
        $rules = [];
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
            $rules['target'] = 'required|string'; // target is username for Subscriptions
            $rules['min'] = 'required|integer|min:1';
            $rules['max'] = 'required|integer|gte:min';
            $rules['posts'] = 'required|integer|min:1';
            $rules['delay'] = 'required|in:0,5,10,15,30,60,90';
        }

        $request->validate($rules);

        // Calculate amount and total price with reseller check
        $amount = 0;
        $totalPrice = 0;
        $pricePerThousand = $user->is_seller ? $service->price_reseller : $service->price_sale;

        if ($type === 'Default' || $type === 'Package' || $type === 'Poll') {
            $amount = intval($request->amount);
            $totalPrice = ($amount * $pricePerThousand) / 1000;
        } elseif ($type === 'Custom Comments') {
            // Number of lines in comments is the amount
            $commentsList = preg_split('/\r\n|\r|\n/', trim($request->comments));
            $commentsList = array_filter($commentsList); // remove empty elements
            $amount = count($commentsList);
            
            if ($amount < $service->min_order || $amount > $service->max_order) {
                return back()->withErrors(['comments' => "Comments count must be between {$service->min_order} and {$service->max_order}."])->withInput();
            }
            $totalPrice = ($amount * $pricePerThousand) / 1000;
        } elseif ($type === 'Subscriptions') {
            $posts = intval($request->posts);
            $max = intval($request->max);
            $amount = $posts * $max; // max possible capacity
            $totalPrice = ($posts * $max * $pricePerThousand) / 1000;
        }

        // Check if user has sufficient balance
        if ($user->balance < $totalPrice) {
            return back()->withErrors(['balance' => 'Insufficient balance to place this order.'])->withInput();
        }

        // Check API configuration
        $api = $service->api;
        if (!$api || !$api->url || !$api->api_key) {
            return back()->withErrors(['service' => 'The API provider for this service is not configured correctly.'])->withInput();
        }

        // Prepare parameters for API call
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
            // Send POST Request using Laravel's Http client (equivalent to cURL)
            $response = Http::external()->asForm()->post($api->url, $postFields);

            if ($response->failed()) {
                return back()->withErrors(['api' => 'Failed to connect to the SMM API Provider.'])->withInput();
            }

            $data = $response->json();

            if (isset($data['status']) && $data['status'] === 'success') {
                $orderId = $data['order'];

                // Deduct user balance and create order records transactionally
                \Illuminate\Support\Facades\DB::transaction(function () use ($user, $totalPrice, $service, $api, $orderId, $request, $amount) {
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

                return redirect()->route('member.smm.history')->with('success', 'Order placed successfully!');
            } else {
                $errorMsg = $data['error'] ?? 'Failed API response.';
                return back()->withErrors(['api' => 'API Error: ' . $errorMsg])->withInput();
            }

        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'INSUFFICIENT_BALANCE') {
                return back()->withErrors(['balance' => 'Insufficient balance to place this order.'])->withInput();
            }
            return back()->withErrors(['api' => 'System error: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'System error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Sync order status with API provider for the member.
     */
    public function orderSync($id)
    {
        $order = OrderSmm::with('api')
            ->where('id_user', auth()->id())
            ->findOrFail($id);

        if (!$order->api || !$order->api->url || !$order->api->api_key) {
            return back()->withErrors(['api' => 'API Provider for this order has not been configured!']);
        }

        if (!$order->sid) {
            return back()->withErrors(['api' => 'This order does not have a SID (API Order ID)!']);
        }

        try {
            $response = Http::external()->asForm()->post($order->api->url, [
                'action' => 'status',
                'key' => $order->api->api_key,
                'order' => $order->sid
            ]);

            if ($response->failed()) {
                return back()->withErrors(['api' => 'Failed to connect to API Provider! Status: ' . $response->status()]);
            }

            $data = $response->json();

            if (isset($data['status'])) {
                $apiStatus = strtolower($data['status']);
                $dbStatus = 'Pending';

                if ($apiStatus === 'pending') {
                    $dbStatus = 'Pending';
                } elseif ($apiStatus === 'processing' || $apiStatus === 'in progress') {
                    $dbStatus = 'In Progres';
                } elseif ($apiStatus === 'partial') {
                    $dbStatus = 'Partial';
                } elseif ($apiStatus === 'canceled' || $apiStatus === 'cancel') {
                    $dbStatus = 'Cancel';
                } elseif ($apiStatus === 'error' || $apiStatus === 'fail') {
                    $dbStatus = 'Error';
                } elseif ($apiStatus === 'completed' || $apiStatus === 'success' || $apiStatus === 'finish') {
                    $dbStatus = 'Success';
                }

                $order->update([
                    'status_order' => $dbStatus,
                    'start_count' => intval($data['start_count'] ?? $order->start_count),
                    'remains' => intval($data['remains'] ?? $order->remains),
                ]);

                return back()->with('success', 'Order status #' . $order->invoice . ' successfully synchronized!');
            } else {
                $errorMsg = $data['error'] ?? 'Invalid API response format.';
                return back()->withErrors(['api' => 'Failed to synchronize status: ' . $errorMsg]);
            }

        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the member order history list.
     */
    public function historyIndex()
    {
        $orders = OrderSmm::with('service')
            ->where('id_user', auth()->id())
            ->orderBy('create_at', 'desc')
            ->get();
        return view('member.smm.history.index', compact('orders'));
    }

    /**
     * Show member SMM order details.
     */
    public function historyShow($invoice)
    {
        $order = OrderSmm::with(['service.category', 'api'])
            ->where('id_user', auth()->id())
            ->where('invoice', $invoice)
            ->firstOrFail();

        return view('member.smm.history.show', compact('order'));
    }

    /**
     * Generate or regenerate a new API key for the authenticated user.
     */
    public function generateApiKey()
    {
        $user = auth()->user();
        $user->api_key = bin2hex(random_bytes(32));
        $user->save();

        return redirect()->back()->with('success', 'API Key successfully generated!');
    }
}
