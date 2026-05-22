@extends('layouts.admin.master')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <h3>Admin Dashboard</h3>
                </div>
                <div class="col-12 col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="home-item" href="{{ route('admin.index') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Home</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Container-fluid starts-->
    <div class="container-fluid dashboard-default-sec">
        
        <!-- 4 Top Widgets (Clean Design) -->
        <div class="row">
            
            <!-- Total Revenue SMM -->
            <div class="col-xl-3 col-md-6 col-sm-6 box-col-3 des-xl-25 rate-sec">
                <div class="card income-card card-primary">                                 
                    <div class="card-body text-center">                                  
                        <div class="round-box">
                            <i data-feather="shopping-bag" style="width:30px; height:30px;"></i>
                        </div>
                        <h5>${{ number_format($totalRevenue, 2) }}</h5>
                        <p>Total Penjualan SMM</p>
                    </div>
                </div>
            </div>

            <!-- Profit SMM -->
            <div class="col-xl-3 col-md-6 col-sm-6 box-col-3 des-xl-25 rate-sec">
                <div class="card income-card card-success">                                 
                    <div class="card-body text-center">                                  
                        <div class="round-box">
                            <i data-feather="trending-up" style="width:30px; height:30px;"></i>
                        </div>
                        <h5>${{ number_format($totalProfit, 2) }}</h5>
                        <p>Total Keuntungan Bersih</p>
                    </div>
                </div>
            </div>

            <!-- Total Deposit -->
            <div class="col-xl-3 col-md-6 col-sm-6 box-col-3 des-xl-25 rate-sec">
                <div class="card income-card card-secondary">                                 
                    <div class="card-body text-center">                                  
                        <div class="round-box">
                            <i data-feather="dollar-sign" style="width:30px; height:30px;"></i>
                        </div>
                        <h5>${{ number_format($totalDeposit, 2) }}</h5>
                        <p>Total Deposit Masuk</p>
                    </div>
                </div>
            </div>
            
            <!-- Total Users -->
            <div class="col-xl-3 col-md-6 col-sm-6 box-col-3 des-xl-25 rate-sec">
                <div class="card income-card card-info">                                 
                    <div class="card-body text-center">                                  
                        <div class="round-box">
                            <i data-feather="users" style="width:30px; height:30px;"></i>
                        </div>
                        <h5>{{ number_format($totalUsers, 0, ',', '.') }} Member</h5>
                        <p>Total Pengguna Aktif</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <!-- Laporan Komparasi Bulanan (Tabel Sendiri) -->
            <div class="col-xl-5 col-md-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Report</h5>
                        <!-- Filter Inline -->
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
                            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-start">Keterangan</th>
                                        <th><small class="text-muted">({{ date('F Y', mktime(0, 0, 0, $month, 10, $year)) }})</small></th>
                                        <th>Bulan Sebelumnya</th>
                                        <th>Status / Pertumbuhan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start fw-bold"><i data-feather="shopping-bag" class="me-2 text-primary" style="width:16px;"></i> Bruto</td>
                                        <td class="text-primary fw-bold">${{ number_format($totalRevenue, 2) }}</td>
                                        <td class="text-muted">${{ number_format($lastMonthRevenue, 2) }}</td>
                                        <td>
                                            @if($totalRevenue > $lastMonthRevenue)
                                                <span class="badge badge-success"><i class="fa fa-arrow-up"></i> Naik</span>
                                            @elseif($totalRevenue < $lastMonthRevenue)
                                                <span class="badge badge-danger"><i class="fa fa-arrow-down"></i> Turun</span>
                                            @else
                                                <span class="badge badge-light text-dark">- Stabil</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-bold"><i data-feather="trending-up" class="me-2 text-success" style="width:16px;"></i> Profit</td>
                                        <td class="text-success fw-bold">${{ number_format($totalProfit, 2) }}</td>
                                        <td class="text-muted">${{ number_format($lastMonthProfit, 2) }}</td>
                                        <td>
                                            @if($totalProfit > $lastMonthProfit)
                                                <span class="badge badge-success"><i class="fa fa-arrow-up"></i> Naik</span>
                                            @elseif($totalProfit < $lastMonthProfit)
                                                <span class="badge badge-danger"><i class="fa fa-arrow-down"></i> Turun</span>
                                            @else
                                                <span class="badge badge-light text-dark">- Stabil</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-bold"><i data-feather="dollar-sign" class="me-2 text-secondary" style="width:16px;"></i> Deposit</td>
                                        <td class="fw-bold">${{ number_format($totalDeposit, 2) }}</td>
                                        <td class="text-muted">${{ number_format($lastMonthDeposit, 2) }}</td>
                                        <td>
                                            @if($totalDeposit > $lastMonthDeposit)
                                                <span class="badge badge-success"><i class="fa fa-arrow-up"></i> Naik</span>
                                            @elseif($totalDeposit < $lastMonthDeposit)
                                                <span class="badge badge-danger"><i class="fa fa-arrow-down"></i> Turun</span>
                                            @else
                                                <span class="badge badge-light text-dark">- Stabil</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SMM Sales Report (Filtered) -->
            <div class="col-xl-7 col-md-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Riwayat Pesanan SMM Terakhir</h5>
                        <a href="{{ route('admin.smm.order') }}" class="btn btn-outline-primary btn-sm">Lihat Semua Pesanan</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>User</th>
                                        <th>Service</th>
                                        <th>Modal</th>
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
                                            <td><span class="badge badge-light text-dark">{{ $order->invoice }}</span></td>
                                            <td>{{ $order->user->username ?? 'Unknown' }}</td>
                                            <td class="text-start">{{ Str::limit($order->service->name ?? 'Service Removed', 30) }}</td>
                                            <td class="text-danger">${{ number_format($order->price_api, 2) }}</td>
                                            <td class="text-primary">${{ number_format($order->price_sale, 2) }}</td>
                                            <td class="text-success font-weight-bold">
                                                @if($order->status_order != 'Failed' && $order->status_order != 'Error' && $order->status_order != 'Canceled')
                                                    +${{ number_format($profit, 2) }}
                                                @else
                                                    <span class="text-muted">$0 (Canceled)</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->status_order == 'Success')
                                                    <span class="badge badge-success">Success</span>
                                                @elseif(in_array($order->status_order, ['Pending', 'Processing']))
                                                    <span class="badge badge-warning">{{ $order->status_order }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $order->status_order }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Belum ada pesanan SMM pada periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Deposits & Users (Half Width) -->
            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Data Deposit Terbaru</h5>
                        <a href="{{ route('admin.payment.history') }}" class="btn btn-outline-primary btn-sm">Kelola Deposit</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentDeposits as $deposit)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($deposit->create_at)->format('d M Y, H:i') }}</td>
                                            <td>{{ $deposit->user->username ?? 'Unknown' }}</td>
                                            <td>${{ number_format($deposit->amount, 2) }}</td>
                                            <td>
                                                @if($deposit->status_payment == 'Success')
                                                    <span class="badge badge-success">Success</span>
                                                @elseif($deposit->status_payment == 'Pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $deposit->status_payment }}</span>
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

            <div class="col-xl-6 col-md-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5>Register Pengguna Baru</h5>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-outline-primary btn-sm">Kelola Pengguna</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Register Date</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentUsers as $user)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($user->create_at)->format('d M Y, H:i') }}</td>
                                            <td>{{ $user->full_name }}</td>
                                            <td>
                                                @if($user->level == 'Admin')
                                                    <span class="badge badge-danger">Admin</span>
                                                @elseif($user->level == 'Reseller')
                                                    <span class="badge badge-info">Reseller</span>
                                                @else
                                                    <span class="badge badge-primary">Member</span>
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
    <!-- Container-fluid Ends-->
</div>
@endsection