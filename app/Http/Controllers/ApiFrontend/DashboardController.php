<?php

namespace App\Http\Controllers\ApiFrontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrderSmm;
use App\Models\HistoryDeposit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Fetch user dashboard statistics, latest activities, and chart data.
     */
    public function index(Request $request)
    {
        // Extract API key from inputs or Authorization header
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

        // Authenticate active user
        $user = User::where('api_key', $apiKey)->where('status', 'Active')->first();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive API key.'
            ], 401);
        }

        $userId = $user->id;

        // 1. Core metrics
        $totalOrders = OrderSmm::where('id_user', $userId)->count();
        $totalPending = OrderSmm::where('id_user', $userId)->where('status_order', 'Pending')->count();
        $totalSuccess = OrderSmm::where('id_user', $userId)->where('status_order', 'Success')->count();
        
        $totalDeposit = HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->sum('amount');

        // 2. Latest Transactions (5 latest SMM orders)
        $recentTransactions = OrderSmm::with('service')
            ->where('id_user', $userId)
            ->orderBy('create_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'invoice' => $order->invoice,
                    'service_name' => $order->service ? $order->service->name_service : 'Unknown Service',
                    'target' => $order->target,
                    'amount' => $order->amount,
                    'price' => number_format($order->price_sale, 2, '.', ''),
                    'status' => $order->status_order,
                    'created_at' => $order->create_at->toDateTimeString()
                ];
            });

        // 3. Latest Deposits (5 latest deposits)
        $recentDeposits = HistoryDeposit::where('id_user', $userId)
            ->orderBy('create_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($deposit) {
                return [
                    'id' => $deposit->id,
                    'invoice' => $deposit->invoice,
                    'amount' => number_format($deposit->amount, 2, '.', ''),
                    'status' => $deposit->status_payment,
                    'created_at' => $deposit->create_at->toDateTimeString()
                ];
            });

        // 4. Graph Data (last 30 days of daily stats)
        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[$date] = [
                'date' => $date,
                'orders_count' => 0,
                'orders_amount' => 0.00,
                'deposits_amount' => 0.00
            ];
        }

        // Fetch SMM orders grouped by date
        $dailyOrders = OrderSmm::where('id_user', $userId)
            ->where('create_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(create_at) as date, COUNT(*) as count, SUM(price_sale) as total')
            ->groupBy('date')
            ->get();

        foreach ($dailyOrders as $do) {
            $date = $do->date;
            if (isset($chartData[$date])) {
                $chartData[$date]['orders_count'] = (int)$do->count;
                $chartData[$date]['orders_amount'] = round((float)$do->total, 2);
            }
        }

        // Fetch deposits grouped by date
        $dailyDeposits = HistoryDeposit::where('id_user', $userId)
            ->where('status_payment', 'Success')
            ->where('create_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(create_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        foreach ($dailyDeposits as $dd) {
            $date = $dd->date;
            if (isset($chartData[$date])) {
                $chartData[$date]['deposits_amount'] = round((float)$dd->total, 2);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'balance' => number_format($user->balance, 4, '.', ''),
                ],
                'metrics' => [
                    'total_order' => $totalOrders,
                    'total_pending' => $totalPending,
                    'total_success' => $totalSuccess,
                    'total_deposit' => number_format($totalDeposit, 2, '.', ''),
                ],
                'recent_transactions' => $recentTransactions,
                'recent_deposits' => $recentDeposits,
                'chart_data' => array_values($chartData)
            ]
        ]);
    }
}
