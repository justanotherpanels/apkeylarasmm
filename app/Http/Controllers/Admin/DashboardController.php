<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderSmm;
use App\Models\HistoryDeposit;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Setup Date Filters
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $selectedDate = Carbon::createFromDate($year, $month, 1);
        $lastMonthDate = $selectedDate->copy()->subMonth();
        
        $lastMonth = $lastMonthDate->format('m');
        $lastYear = $lastMonthDate->format('Y');

        // 1. Total Deposit (Success only)
        $totalDeposit = HistoryDeposit::where('status_payment', 'Success')
            ->whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->sum('amount');
            
        $lastMonthDeposit = HistoryDeposit::where('status_payment', 'Success')
            ->whereMonth('create_at', $lastMonth)
            ->whereYear('create_at', $lastYear)
            ->sum('amount');

        // 2. Total SMM Orders (Count all)
        $totalOrders = OrderSmm::whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->count();

        // 3. Total Profit (price_sale - price_api) for non-failed orders
        $totalProfit = OrderSmm::where('status_order', '!=', 'Failed')
            ->whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->selectRaw('SUM(price_sale - price_api) as profit')
            ->value('profit') ?? 0;
            
        $lastMonthProfit = OrderSmm::where('status_order', '!=', 'Failed')
            ->whereMonth('create_at', $lastMonth)
            ->whereYear('create_at', $lastYear)
            ->selectRaw('SUM(price_sale - price_api) as profit')
            ->value('profit') ?? 0;
            
        // Total Gross Revenue (Pendapatan Kotor SMM)
        $totalRevenue = OrderSmm::where('status_order', '!=', 'Failed')
            ->whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->sum('price_sale');
            
        $lastMonthRevenue = OrderSmm::where('status_order', '!=', 'Failed')
            ->whereMonth('create_at', $lastMonth)
            ->whereYear('create_at', $lastYear)
            ->sum('price_sale');

        // 4. Total Users (Lifetime vs New this month)
        $totalUsers = User::count(); // Usually total users is lifetime
        $newUsersThisMonth = User::whereMonth('create_at', $month)
                                 ->whereYear('create_at', $year)
                                 ->count();

        // 5. Recent 10 Deposits
        $recentDeposits = HistoryDeposit::with('user')
            ->whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // 6. Recent 10 Registered Users
        $recentUsers = User::whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        // 7. Recent SMM Orders (for sales/profit report)
        $recentOrders = OrderSmm::with(['user', 'service'])
            ->whereMonth('create_at', $month)
            ->whereYear('create_at', $year)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('admin.index', compact(
            'month',
            'year',
            'totalDeposit',
            'lastMonthDeposit',
            'totalOrders',
            'totalProfit',
            'lastMonthProfit',
            'totalRevenue',
            'lastMonthRevenue',
            'totalUsers',
            'newUsersThisMonth',
            'recentDeposits',
            'recentUsers',
            'recentOrders'
        ));
    }
}
