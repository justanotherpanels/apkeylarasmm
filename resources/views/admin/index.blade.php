@extends('layouts.admin.master')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    
    <div class="row">
        <!-- Total Revenue SMM -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Penjualan SMM</div>
                        <h4 class="mb-0">${{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                    <i class="fas fa-shopping-bag fa-2x text-white-50"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Profit SMM -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Keuntungan Bersih</div>
                        <h4 class="mb-0">${{ number_format($totalProfit, 2) }}</h4>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-white-50"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Total Deposit -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Deposit Masuk</div>
                        <h4 class="mb-0">${{ number_format($totalDeposit, 2) }}</h4>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-white-50"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Pengguna Aktif</div>
                        <h4 class="mb-0">{{ number_format($totalUsers, 0, ',', '.') }} Member</h4>
                    </div>
                    <i class="fas fa-users fa-2x text-white-50"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Laporan Komparasi Bulanan -->
        <div class="col-xl-5">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-chart-bar me-1"></i> Report</div>
                    <form action="{{ route('admin.index') }}" method="GET" class="d-flex align-items-center">
                        <select name="month" class="form-select form-select-sm me-2" style="width: auto;">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" class="form-select form-select-sm me-2" style="width: auto;">
                            @for($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-3">Keterangan</th>
                                    <th><small class="text-muted">({{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }})</small></th>
                                    <th>Bulan Sebelumnya</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-bold ps-3"><i class="fas fa-shopping-bag me-2 text-primary"></i> Bruto</td>
                                    <td class="text-primary fw-bold">${{ number_format($totalRevenue, 2) }}</td>
                                    <td class="text-muted">${{ number_format($lastMonthRevenue, 2) }}</td>
                                    <td>
                                        @if($totalRevenue > $lastMonthRevenue)
                                            <span class="badge bg-success"><i class="fas fa-arrow-up"></i> Naik</span>
                                        @elseif($totalRevenue < $lastMonthRevenue)
                                            <span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Turun</span>
                                        @else
                                            <span class="badge bg-secondary">- Stabil</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold ps-3"><i class="fas fa-chart-line me-2 text-success"></i> Profit</td>
                                    <td class="text-success fw-bold">${{ number_format($totalProfit, 2) }}</td>
                                    <td class="text-muted">${{ number_format($lastMonthProfit, 2) }}</td>
                                    <td>
                                        @if($totalProfit > $lastMonthProfit)
                                            <span class="badge bg-success"><i class="fas fa-arrow-up"></i> Naik</span>
                                        @elseif($totalProfit < $lastMonthProfit)
                                            <span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Turun</span>
                                        @else
                                            <span class="badge bg-secondary">- Stabil</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-bold ps-3"><i class="fas fa-dollar-sign me-2 text-warning"></i> Deposit</td>
                                    <td class="fw-bold">${{ number_format($totalDeposit, 2) }}</td>
                                    <td class="text-muted">${{ number_format($lastMonthDeposit, 2) }}</td>
                                    <td>
                                        @if($totalDeposit > $lastMonthDeposit)
                                            <span class="badge bg-success"><i class="fas fa-arrow-up"></i> Naik</span>
                                        @elseif($totalDeposit < $lastMonthDeposit)
                                            <span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Turun</span>
                                        @else
                                            <span class="badge bg-secondary">- Stabil</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMM Sales Report -->
        <div class="col-xl-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-table me-1"></i> Riwayat Pesanan SMM Terakhir</div>
                    <a href="{{ route('admin.smm.order') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover text-center mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>User</th>
                                    <th>Service</th>
                                    <th>Jual</th>
                                    <th class="text-success">Profit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    @php
                                        $profit = $order->price_sale - $order->price_api;
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($order->create_at)->format('d M Y, H:i') }}</td>
                                        <td><span class="badge bg-secondary">{{ $order->invoice }}</span></td>
                                        <td>{{ $order->user->username ?? 'Unknown' }}</td>
                                        <td class="text-start">{{ Str::limit($order->service->name ?? 'Service Removed', 30) }}</td>
                                        <td class="text-primary">${{ number_format($order->price_sale, 2) }}</td>
                                        <td class="text-success fw-bold">
                                            @if($order->status_order != 'Failed' && $order->status_order != 'Error' && $order->status_order != 'Canceled')
                                                +${{ number_format($profit, 2) }}
                                            @else
                                                <span class="text-muted">$0 (Canceled)</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status_order == 'Success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif(in_array($order->status_order, ['Pending', 'Processing']))
                                                <span class="badge bg-warning text-dark">{{ $order->status_order }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $order->status_order }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada pesanan SMM pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Deposits -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-money-bill-wave me-1"></i> Data Deposit Terbaru</div>
                    <a href="{{ route('admin.payment.history') }}" class="btn btn-primary btn-sm">Kelola Deposit</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDeposits as $deposit)
                                    <tr>
                                        <td class="ps-3">{{ \Carbon\Carbon::parse($deposit->create_at)->format('d M Y, H:i') }}</td>
                                        <td>{{ $deposit->user->username ?? 'Unknown' }}</td>
                                        <td>${{ number_format($deposit->amount, 2) }}</td>
                                        <td>
                                            @if($deposit->status_payment == 'Success')
                                                <span class="badge bg-success">Success</span>
                                            @elseif($deposit->status_payment == 'Pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-danger">{{ $deposit->status_payment }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada deposit pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-user-plus me-1"></i> Register Pengguna Baru</div>
                    <a href="{{ route('admin.user.index') }}" class="btn btn-primary btn-sm">Kelola Pengguna</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Register Date</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                    <tr>
                                        <td class="ps-3">{{ \Carbon\Carbon::parse($user->create_at)->format('d M Y, H:i') }}</td>
                                        <td>{{ $user->full_name }}</td>
                                        <td>
                                            @if($user->level == 'Admin')
                                                <span class="badge bg-danger">Admin</span>
                                            @elseif($user->level == 'Reseller')
                                                <span class="badge bg-info">Reseller</span>
                                            @else
                                                <span class="badge bg-primary">Member</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada pengguna baru pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
