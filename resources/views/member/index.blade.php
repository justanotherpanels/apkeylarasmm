@extends('layouts.member.master')

@section('content')
<script>
    window.chartTimelineData = @json($chartData);
    @if(!empty($chartData) && (count($chartData) > 1 || $chartData[0][1] !== 0))
        (function() {
            var timestamps = window.chartTimelineData.map(function(pt) { return pt[0]; });
            var minTime = Math.min.apply(null, timestamps);
            var maxTime = Math.max.apply(null, timestamps);
            if (timestamps.length === 1) {
                minTime = minTime - 86400000 * 3;
                maxTime = maxTime + 86400000 * 3;
            } else {
                minTime = minTime - 86400000 * 1;
                maxTime = maxTime + 86400000 * 1;
            }
            window.chartTimelineMinTime = minTime;
            window.chartTimelineMaxTime = maxTime;
        })();
    @endif

    window.chartDepositData = @json($depositChartData);
    @if(!empty($depositChartData) && (count($depositChartData) > 1 || $depositChartData[0][1] !== 0))
        (function() {
            var timestamps = window.chartDepositData.map(function(pt) { return pt[0]; });
            var minTime = Math.min.apply(null, timestamps);
            var maxTime = Math.max.apply(null, timestamps);
            if (timestamps.length === 1) {
                minTime = minTime - 86400000 * 3;
                maxTime = maxTime + 86400000 * 3;
            } else {
                minTime = minTime - 86400000 * 1;
                maxTime = maxTime + 86400000 * 1;
            }
            window.chartDepositMinTime = minTime;
            window.chartDepositMaxTime = maxTime;
        })();
    @endif
</script>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Welcome Greeting -->
    <div class="card bg-light mb-4 border-0 shadow-sm">
        <div class="card-body">
            <h4 class="mb-1 text-primary">Welcome Back, {{ Auth::user()->full_name ?? 'User' }}!</h4>
            <p class="text-muted mb-0">We are glad to see you again. Here is your business growth summary for today.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Account Balance Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Account Balance</div>
                            <div class="fs-3 fw-bold">${{ number_format(Auth::user()->balance, 2) }}</div>
                        </div>
                        <div class="text-white-50"><i class="fas fa-wallet fa-2x"></i></div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link text-decoration-none" href="{{ route('member.payment.add') }}">Top Up Balance</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Active Orders Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-dark-50 small text-uppercase fw-bold">Active SMM Orders</div>
                            <div class="fs-3 fw-bold">{{ $activeOrdersCount }}</div>
                        </div>
                        <div class="text-dark-50"><i class="fas fa-shopping-cart fa-2x"></i></div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link text-decoration-none" href="{{ route('member.smm.history') }}">View History</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Total Spent Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Total Spent</div>
                            <div class="fs-3 fw-bold">${{ number_format($totalSpent, 2) }}</div>
                        </div>
                        <div class="text-white-50"><i class="fas fa-chart-line fa-2x"></i></div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <span class="small text-white">
                        @if($percentageChange >= 0)
                            <i class="fas fa-arrow-up me-1 text-white"></i>{{ number_format($percentageChange, 1) }}% vs last month
                        @else
                            <i class="fas fa-arrow-down me-1 text-white"></i>{{ number_format(abs($percentageChange), 1) }}% vs last month
                        @endif
                    </span>
                    <div class="small text-white"><i class="fas fa-ellipsis-h"></i></div>
                </div>
            </div>
        </div>

        <!-- Total Deposit Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Total Deposit</div>
                            <div class="fs-3 fw-bold">${{ number_format($totalDeposit, 2) }}</div>
                        </div>
                        <div class="text-white-50"><i class="fas fa-plus-circle fa-2x"></i></div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex align-items-center justify-content-between">
                    <span class="small text-white">
                        @if($depositPercentageChange >= 0)
                            <i class="fas fa-arrow-up me-1 text-white"></i>{{ number_format($depositPercentageChange, 1) }}% vs last month
                        @else
                            <i class="fas fa-arrow-down me-1 text-white"></i>{{ number_format(abs($depositPercentageChange), 1) }}% vs last month
                        @endif
                    </span>
                    <div class="small text-white">({{ $successDepositsCount }} deposits)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Spending Area Chart -->
        <div class="col-xl-6">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <i class="fas fa-chart-area me-1 text-primary"></i>
                    <strong class="text-dark">Spending Overview (Daily SMM)</strong>
                </div>
                <div class="card-body">
                    <canvas id="mySpendingAreaChart" width="100%" height="45"></canvas>
                </div>
            </div>
        </div>

        <!-- Deposit Bar Chart -->
        <div class="col-xl-6">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-0">
                    <i class="fas fa-chart-bar me-1 text-danger"></i>
                    <strong class="text-dark">Deposit Overview (Successful)</strong>
                </div>
                <div class="card-body">
                    <canvas id="myDepositBarChart" width="100%" height="45"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table Card -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1 text-success"></i>
                <strong class="text-dark">Recent SMM Orders</strong>
            </div>
            <a href="{{ route('member.smm.history') }}" class="btn btn-outline-success btn-sm">View All Orders</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('member.smm.history.show', $order->invoice) }}" class="fw-bold text-decoration-none text-primary">
                                        #{{ $order->invoice }}
                                    </a>
                                </td>
                                <td>{{ $order->service->name_service ?? 'Unknown Service' }}</td>
                                <td>{{ $order->create_at->format('d M Y H:i') }}</td>
                                <td>{{ number_format($order->amount) }}</td>
                                <td>${{ number_format($order->price_sale, 4) }}</td>
                                <td>
                                    @php
                                        $status = strtolower($order->status_order);
                                        $badgeClass = 'bg-primary';
                                        if ($status === 'success' || $status === 'completed' || $status === 'finish') {
                                            $badgeClass = 'bg-success';
                                        } elseif ($status === 'pending') {
                                            $badgeClass = 'bg-warning text-dark';
                                        } elseif ($status === 'cancel' || $status === 'canceled' || $status === 'error') {
                                            $badgeClass = 'bg-danger';
                                        } elseif ($status === 'processing' || $status === 'in progres') {
                                            $badgeClass = 'bg-info text-dark';
                                        } elseif ($status === 'partial') {
                                            $badgeClass = 'bg-primary';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $order->status_order }}</span>
                                </td>
                            </tr>
                        @endforeach
                        @if($recentOrders->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No recent SMM orders found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Spending Area Chart
    var ctxArea = document.getElementById("mySpendingAreaChart");
    if (ctxArea) {
        var chartTimelineData = window.chartTimelineData || [];
        var labels = chartTimelineData.map(function(item) {
            var d = new Date(item[0]);
            return d.getDate() + ' ' + d.toLocaleString('en-US', { month: 'short' });
        });
        var data = chartTimelineData.map(function(item) { return item[1]; });

        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Spending ($)",
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: data,
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, .125)",
                        }
                    }],
                },
                legend: {
                    display: false
                }
            }
        });
    }

    // Deposit Bar Chart
    var ctxBar = document.getElementById("myDepositBarChart");
    if (ctxBar) {
        var chartDepositData = window.chartDepositData || [];
        var labels = chartDepositData.map(function(item) {
            var d = new Date(item[0]);
            return d.getDate() + ' ' + d.toLocaleString('en-US', { month: 'short' });
        });
        var data = chartDepositData.map(function(item) { return item[1]; });

        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "Deposit ($)",
                    backgroundColor: "rgba(220,53,69,1)",
                    borderColor: "rgba(220,53,69,1)",
                    data: data,
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5
                        },
                        gridLines: {
                            display: true
                        }
                    }],
                },
                legend: {
                    display: false
                }
            }
        });
    }
});
</script>
@endsection