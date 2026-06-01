<?php

namespace App\Http\Controllers\ApiMobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrderSmm;
use App\Models\HistoryDeposit;
use App\Models\TicketMember;
use App\Models\ServiceSmm;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Fetch mobile dashboard statistics, new histories, charts, and recommendations.
     */
    public function index(Request $request)
    {
        // 1. Authenticate user via api_key
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

        $userId = $user->id;

        // 2. Balance Reminder Alert (when balance is <= $3)
        $balanceAlert = false;
        $balanceMessage = '';
        if ((float)$user->balance <= 3.00) {
            $balanceAlert = true;
            $balanceMessage = 'Your SMM balance is low ($' . number_format($user->balance, 2) . '). Please add funds soon to prevent service interruptions.';
        }

        // 3. SMM Order counts grouped by status
        // DB Enum: ['Pending', 'In Progres', 'Partial', 'Cancel', 'Error', 'Success', 'Finish']
        $orderPending = OrderSmm::where('id_user', $userId)->where('status_order', 'Pending')->count();
        $orderInProgress = OrderSmm::where('id_user', $userId)->where('status_order', 'In Progres')->count();
        $orderSuccess = OrderSmm::where('id_user', $userId)->whereIn('status_order', ['Success', 'Finish'])->count();
        $orderError = OrderSmm::where('id_user', $userId)->whereIn('status_order', ['Error', 'Cancel'])->count();
        $orderPartial = OrderSmm::where('id_user', $userId)->where('status_order', 'Partial')->count();

        // 4. Ticket Active count (where status is not Closed)
        $activeTicketsCount = TicketMember::where('id_user', $userId)
            ->where('status', '!=', 'Closed')
            ->count();

        // 5. New History: 5 latest SMM orders
        $recentOrders = OrderSmm::with('service')
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
                    'price' => number_format($order->price_sale, 4, '.', ''),
                    'status' => $order->status_order,
                    'created_at' => $order->create_at->toDateTimeString()
                ];
            });

        // 5. New History: 5 latest deposits
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

        // 6. Filterable Order Graph Data (filter: day, week, month, previous_month, previous_year)
        $filter = $request->input('filter', 'week');
        $chartData = [];

        if ($filter === 'day') {
            $start = now()->startOfDay();
            $end = now()->endOfDay();

            $results = OrderSmm::where('id_user', $userId)
                ->whereBetween('create_at', [$start, $end])
                ->selectRaw('HOUR(create_at) as hour, COUNT(*) as count, SUM(price_sale) as total')
                ->groupBy('hour')
                ->get()
                ->keyBy('hour');

            for ($i = 0; $i < 24; $i++) {
                $label = sprintf('%02d:00', $i);
                $chartData[] = [
                    'label' => $label,
                    'orders_count' => isset($results[$i]) ? (int)$results[$i]->count : 0,
                    'orders_amount' => isset($results[$i]) ? round((float)$results[$i]->total, 4) : 0.0000
                ];
            }
        } elseif ($filter === 'week') {
            $start = now()->subDays(6)->startOfDay();
            $end = now()->endOfDay();

            $results = OrderSmm::where('id_user', $userId)
                ->whereBetween('create_at', [$start, $end])
                ->selectRaw('DATE(create_at) as date, COUNT(*) as count, SUM(price_sale) as total')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $label = now()->subDays($i)->format('D, d M');
                $chartData[] = [
                    'label' => $label,
                    'date' => $date,
                    'orders_count' => isset($results[$date]) ? (int)$results[$date]->count : 0,
                    'orders_amount' => isset($results[$date]) ? round((float)$results[$date]->total, 4) : 0.0000
                ];
            }
        } elseif ($filter === 'month') {
            $start = now()->subDays(29)->startOfDay();
            $end = now()->endOfDay();

            $results = OrderSmm::where('id_user', $userId)
                ->whereBetween('create_at', [$start, $end])
                ->selectRaw('DATE(create_at) as date, COUNT(*) as count, SUM(price_sale) as total')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $label = now()->subDays($i)->format('d M');
                $chartData[] = [
                    'label' => $label,
                    'date' => $date,
                    'orders_count' => isset($results[$date]) ? (int)$results[$date]->count : 0,
                    'orders_amount' => isset($results[$date]) ? round((float)$results[$date]->total, 4) : 0.0000
                ];
            }
        } elseif ($filter === 'previous_month') {
            $start = now()->subMonth()->startOfMonth();
            $end = now()->subMonth()->endOfMonth();

            $results = OrderSmm::where('id_user', $userId)
                ->whereBetween('create_at', [$start, $end])
                ->selectRaw('DATE(create_at) as date, COUNT(*) as count, SUM(price_sale) as total')
                ->groupBy('date')
                ->get()
                ->keyBy('date');

            $daysInMonth = $start->daysInMonth;
            for ($i = 0; $i < $daysInMonth; $i++) {
                $dayDate = $start->copy()->addDays($i);
                $dateStr = $dayDate->format('Y-m-d');
                $label = $dayDate->format('d M');
                $chartData[] = [
                    'label' => $label,
                    'date' => $dateStr,
                    'orders_count' => isset($results[$dateStr]) ? (int)$results[$dateStr]->count : 0,
                    'orders_amount' => isset($results[$dateStr]) ? round((float)$results[$dateStr]->total, 4) : 0.0000
                ];
            }
        } elseif ($filter === 'previous_year') {
            $start = now()->subYear()->startOfYear();
            $end = now()->subYear()->endOfYear();

            $results = OrderSmm::where('id_user', $userId)
                ->whereBetween('create_at', [$start, $end])
                ->selectRaw('MONTH(create_at) as month, COUNT(*) as count, SUM(price_sale) as total')
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            for ($i = 1; $i <= 12; $i++) {
                $monthDate = now()->subYear()->month($i);
                $label = $monthDate->format('M Y');
                $chartData[] = [
                    'label' => $label,
                    'month_index' => $i,
                    'orders_count' => isset($results[$i]) ? (int)$results[$i]->count : 0,
                    'orders_amount' => isset($results[$i]) ? round((float)$results[$i]->total, 4) : 0.0000
                ];
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid filter parameter. Allowed values: day, week, month, previous_month, previous_year.'
            ], 400);
        }

        // 7. SMM service recommendations (highly ordered)
        $topServiceIds = OrderSmm::select('id_service_smm')
            ->selectRaw('COUNT(*) as order_count')
            ->groupBy('id_service_smm')
            ->orderByDesc('order_count')
            ->limit(5)
            ->pluck('id_service_smm');

        $recommendedServices = ServiceSmm::with('category')
            ->whereIn('id', $topServiceIds)
            ->where('status', 'Active')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name_service,
                    'category' => $service->category ? $service->category->name_category_smm : 'General',
                    'price' => number_format($service->price_sale, 4, '.', ''),
                    'min' => $service->min,
                    'max' => $service->max
                ];
            });

        // Fallback: If no orders exist, fetch random active services
        if ($recommendedServices->isEmpty()) {
            $recommendedServices = ServiceSmm::with('category')
                ->where('status', 'Active')
                ->limit(5)
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name_service,
                        'category' => $service->category ? $service->category->name_category_smm : 'General',
                        'price' => number_format($service->price_sale, 4, '.', ''),
                        'min' => $service->min,
                        'max' => $service->max
                    ];
                });
        }

        // 8. Return response
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'balance' => number_format($user->balance, 4, '.', ''),
                ],
                'balance_reminder' => [
                    'alert' => $balanceAlert,
                    'message' => $balanceMessage
                ],
                'metrics' => [
                    'total_pending' => $orderPending,
                    'total_in_progress' => $orderInProgress,
                    'total_success' => $orderSuccess,
                    'total_error' => $orderError,
                    'total_partial' => $orderPartial,
                    'active_tickets' => $activeTicketsCount
                ],
                'recent_history' => [
                    'orders' => $recentOrders,
                    'deposits' => $recentDeposits
                ],
                'chart' => [
                    'filter' => $filter,
                    'data' => $chartData
                ],
                'recommended_services' => $recommendedServices
            ]
        ]);
    }
}
