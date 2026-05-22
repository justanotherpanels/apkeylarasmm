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
<div class="container-fluid dashboard-default-sec">
            <div class="row">
              <div class="col-xl-5 box-col-12 des-xl-100"> 
                <div class="row">
                  <div class="col-xl-12 col-md-6 box-col-6 des-xl-50">
                    <div class="card profile-greeting">
                      <div class="card-header">
                        <div class="header-top">

                        </div>
                      </div>
                      <div class="card-body text-center p-t-0">
                        <h3 class="font-light">Welcome Back, {{ Auth::user()->full_name ?? 'User' }}!!</h3>
                        <p>Welcome to the viho Family! we are glad that you visited this dashboard. we will be happy to help you grow your business.</p>
                        <button class="btn btn-light">Update</button>
                      </div>
                      <div class="confetti">
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="confetti-piece"></div>
                        <div class="code-box-copy">
                          <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#profile-greeting" title="Copy"><i class="icofont icofont-copy-alt"></i></button>
                          <pre><code class="language-html" id="profile-greeting">                                     &lt;div class="card profile-greeting"&gt; 
  &lt;div class="card-header"&gt;
    &lt;div class="header-top"&gt;
      &lt;div class="setting-list bg-primary"&gt;
        &lt;ul class="list-unstyled setting-option"&gt;
          &lt;li&gt;&lt;div class="setting-white"&gt;&lt;i class="icon-settings"&gt;&lt;/i&gt;&lt;/div&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="view-html fa fa-code font-white"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-maximize full-card font-white"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-minus minimize-card font-white"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-refresh reload-card font-white"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-error close-card font-white"&gt; &lt;/i&gt;&lt;/li&gt;
        &lt;/ul&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="card-body text-center"&gt;
    &lt;h3 class="font-light"&gt;Wellcome Back, John!!&lt;/h3&gt;
    &lt;p&gt;Lorem ipsum is simply dummy text of the printing and typesetting industry.Lorem ipsum has been&lt;/p&gt;
    &lt;button class="btn btn-light"&gt;Update &lt;/button&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-6 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <div class="card income-card card-primary">                                 
                      <div class="card-body text-center">                                  
                        <div class="round-box">
                          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 448.057 448.057" style="enable-background:new 0 0 448.057 448.057;" xml:space="preserve">
                            <g>
                              <g>
                                <path d="M404.562,7.468c-0.021-0.017-0.041-0.034-0.062-0.051c-13.577-11.314-33.755-9.479-45.069,4.099                                            c-0.017,0.02-0.034,0.041-0.051,0.062l-135.36,162.56L88.66,11.577C77.35-2.031,57.149-3.894,43.54,7.417                                            c-13.608,11.311-15.471,31.512-4.16,45.12l129.6,155.52h-40.96c-17.673,0-32,14.327-32,32s14.327,32,32,32h64v144                                            c0,17.673,14.327,32,32,32c17.673,0,32-14.327,32-32v-180.48l152.64-183.04C419.974,38.96,418.139,18.782,404.562,7.468z"></path>
                              </g>
                            </g>
                            <g>
                              <g>
                                <path d="M320.02,208.057h-16c-17.673,0-32,14.327-32,32s14.327,32,32,32h16c17.673,0,32-14.327,32-32                                            S337.694,208.057,320.02,208.057z"></path>
                              </g>
                            </g>
                          </svg>
                        </div>
                        <h5>${{ number_format(Auth::user()->balance, 2) }}</h5>
                        <p>Account Balance</p><a class="btn-arrow arrow-primary" href="{{ route('member.payment.add') }}"><i class="fa fa-plus me-2"></i>Top Up </a>
                        <div class="parrten">
                          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 448.057 448.057" style="enable-background:new 0 0 448.057 448.057;" xml:space="preserve">
                            <g>
                              <g>
                                <path d="M404.562,7.468c-0.021-0.017-0.041-0.034-0.062-0.051c-13.577-11.314-33.755-9.479-45.069,4.099                                            c-0.017,0.02-0.034,0.041-0.051,0.062l-135.36,162.56L88.66,11.577C77.35-2.031,57.149-3.894,43.54,7.417                                            c-13.608,11.311-15.471,31.512-4.16,45.12l129.6,155.52h-40.96c-17.673,0-32,14.327-32,32s14.327,32,32,32h64v144                                            c0,17.673,14.327,32,32,32c17.673,0,32-14.327,32-32v-180.48l152.64-183.04C419.974,38.96,418.139,18.782,404.562,7.468z"></path>
                              </g>
                            </g>
                            <g>
                              <g>
                                <path d="M320.02,208.057h-16c-17.673,0-32,14.327-32,32s14.327,32,32,32h16c17.673,0,32-14.327,32-32                                            S337.694,208.057,320.02,208.057z">                                  </path>
                              </g>
                            </g>
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-6 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <div class="card income-card card-secondary">                                    
                      <div class="card-body text-center">
                        <div class="round-box">
                          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                            <g>
                              <g>
                                <path d="M256,0C114.615,0,0,114.615,0,256s114.615,256,256,256s256-114.615,256-256S397.385,0,256,0z M96,100.16                                            c50.315,35.939,80.124,94.008,80,155.84c0.151,61.839-29.664,119.919-80,155.84C11.45,325.148,11.45,186.851,96,100.16z M256,480                                            c-49.143,0.007-96.907-16.252-135.84-46.24C175.636,391.51,208.14,325.732,208,256c0.077-69.709-32.489-135.434-88-177.6                                            c80.1-61.905,191.9-61.905,272,0c-98.174,75.276-116.737,215.885-41.461,314.059c11.944,15.577,25.884,29.517,41.461,41.461                                            C353.003,463.884,305.179,480.088,256,480z M416,412v-0.16c-86.068-61.18-106.244-180.548-45.064-266.616                                            c12.395-17.437,27.627-32.669,45.064-45.064C500.654,186.871,500.654,325.289,416,412z"></path>
                              </g>
                            </g>
                          </svg>
                        </div>
                        <h5>{{ $activeOrdersCount }}</h5>
                        <p>Active Orders</p><a class="btn-arrow arrow-secondary" href="{{ route('member.smm.history') }}"><i class="fa fa-eye me-2"></i>View History </a>
                        <div class="parrten">
                          <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewbox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                            <g>
                              <g>
                                <path d="M256,0C114.615,0,0,114.615,0,256s114.615,256,256,256s256-114.615,256-256S397.385,0,256,0z M96,100.16                                            c50.315,35.939,80.124,94.008,80,155.84c0.151,61.839-29.664,119.919-80,155.84C11.45,325.148,11.45,186.851,96,100.16z M256,480                                            c-49.143,0.007-96.907-16.252-135.84-46.24C175.636,391.51,208.14,325.732,208,256c0.077-69.709-32.489-135.434-88-177.6                                            c80.1-61.905,191.9-61.905,272,0c-98.174,75.276-116.737,215.885-41.461,314.059c11.944,15.577,25.884,29.517,41.461,41.461                                            C353.003,463.884,305.179,480.088,256,480z M416,412v-0.16c-86.068-61.18-106.244-180.548-45.064-266.616                                            c12.395-17.437,27.627-32.669,45.064-45.064C500.654,186.871,500.654,325.289,416,412z"></path>
                              </g>
                            </g>
                          </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-7 box-col-12 des-xl-100 dashboard-sec">
                <div class="card income-card">
                  <div class="card-header">
                    <div class="header-top d-sm-flex align-items-center">
                      <h5>Spending overview</h5>
                      <div class="center-content">
                        <p class="d-sm-flex align-items-center">
                          <span class="font-primary m-r-10 f-w-700">${{ number_format($totalSpent, 2) }}</span>
                          @if($percentageChange >= 0)
                            <i class="toprightarrow-primary fa fa-arrow-up m-r-10 text-success"></i>{{ number_format($percentageChange, 1) }}% more than last month
                          @else
                            <i class="toprightarrow-primary fa fa-arrow-down m-r-10 text-danger" style="transform: rotate(180deg)"></i>{{ number_format(abs($percentageChange), 1) }}% less than last month
                          @endif
                        </p>
                      </div>

                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div id="chart-timeline-dashbord"></div>
                    <div class="code-box-copy">
                      <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#yearly-overview" title="Copy"><i class="icofont icofont-copy-alt"></i></button>
                      <pre><code class="language-html" id="yearly-overview">&lt;div class="card income-card"&gt; 
  &lt;div class="card-header"&gt;
    &lt;div class="header-top d-sm-flex align-items-center"&gt;
      &lt;h5&gt; yearly overview  &lt;/h5&gt;
       &lt;div class="center-content" &gt;
         &lt;p&gt; 
           &lt;span class="font-primary fontbold-600" &gt; $859.25k &lt;/span&gt;
           &lt;i class="toprightarrow-primary fa fa-arrow-up m-l-10 m-r-10" &gt; &lt;/i&gt;
            86% More than last year
         &lt;/p&gt; 
      &lt;/div&gt;
      &lt;div class="setting-list"&gt;
        &lt;ul class="list-unstyled setting-option"&gt;
          &lt;li&gt;&lt;div class="setting-primary"&gt;&lt;i class="icon-settings"&gt;&lt;/i&gt;&lt;/div&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="view-html fa fa-code font-primary"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-maximize full-card font-primary"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-minus minimize-card font-primary"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-refresh reload-card font-primary"&gt;&lt;/i&gt;&lt;/li&gt;
          &lt;li&gt;&lt;i class="icofont icofont-error close-card font-primary"&gt; &lt;/i&gt;&lt;/li&gt;
        &lt;/ul&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="card-body p-0"&gt;
    &lt;div id="chart-timeline-dashbord"&gt;&lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-8 box-col-12 des-xl-100">
                <div class="row">
                  <div class="col-xl-12 recent-order-sec">
                    <div class="card">
                      <div class="card-body">
                        <div class="table-responsive">
                          <h5>Recent Orders</h5>
                          <table class="table table-bordernone">                                         
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
                                    <a href="{{ route('member.smm.history.show', $order->invoice) }}" class="font-primary f-w-600">
                                      #{{ $order->invoice }}
                                    </a>
                                  </td>
                                  <td>
                                    <span>{{ $order->service->name_service ?? 'Unknown Service' }}</span>
                                  </td>
                                  <td>
                                    <p>{{ $order->create_at->format('d M Y H:i') }}</p>
                                  </td>
                                  <td>
                                    <p>{{ number_format($order->amount) }}</p>
                                  </td>
                                  <td>
                                    <p>${{ number_format($order->price_sale, 4) }}</p>
                                  </td>
                                  <td>
                                    @php
                                      $status = strtolower($order->status_order);
                                      $badgeClass = 'badge-primary';
                                      if ($status === 'success' || $status === 'completed' || $status === 'finish') {
                                          $badgeClass = 'badge-success';
                                      } elseif ($status === 'pending') {
                                          $badgeClass = 'badge-warning text-dark';
                                      } elseif ($status === 'cancel' || $status === 'canceled' || $status === 'error') {
                                          $badgeClass = 'badge-danger';
                                      } elseif ($status === 'processing' || $status === 'in progres') {
                                          $badgeClass = 'badge-info';
                                      } elseif ($status === 'partial') {
                                          $badgeClass = 'badge-primary';
                                      }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $order->status_order }}</span>
                                  </td>
                                </tr>
                              @endforeach
                              @if($recentOrders->isEmpty())
                                <tr>
                                  <td colspan="6" class="text-center text-muted p-4">No recent SMM orders found.</td>
                                </tr>
                              @endif
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 box-col-12 des-xl-100">
                <div class="row">
                  <div class="col-xl-12 box-col-12 des-xl-50">
                    <div class="card trasaction-sec">
                      <div class="card-header">
                        <div class="header-top d-sm-flex align-items-center">
                          <h5>Deposit</h5>
                          <div class="center-content">
                            <p>{{ $successDepositsCount }} Successful Deposits</p>
                          </div>

                        </div>
                      </div>
                      <div class="transaction-totalbal">
                        <h2> ${{ number_format($totalDeposit, 2) }} 
                          <span class="ms-3"> 
                            @if($depositPercentageChange >= 0)
                              <a class="btn-arrow arrow-secondary" href="javascript:void(0)"><i class="toprightarrow-secondary fa fa-arrow-up me-2"></i>{{ number_format($depositPercentageChange, 1) }}%</a>
                            @else
                              <a class="btn-arrow arrow-secondary" href="javascript:void(0)"><i class="toprightarrow-secondary fa fa-arrow-down me-2" style="transform: rotate(180deg)"></i>{{ number_format(abs($depositPercentageChange), 1) }}%</a>
                            @endif
                          </span>
                        </h2>
                        <p>Total Deposit</p>
                      </div>
                      <div class="card-body p-0">
                        <div id="chart-deposit-dash"></div>
                        <div class="code-box-copy">
                          <button class="code-box-copy__btn btn-clipboard" data-clipboard-target="#transaction" title="Copy"><i class="icofont icofont-copy-alt"></i></button>
                          <pre><code class="language-html" id="transaction">&lt;div class="card trasaction-sec"&gt;
  &lt;div class="card-header"&gt;
    &lt;div class="header-top d-sm-flex align-items-center"&gt;
      &lt;h5&gt;Deposit&lt;/h5&gt;
    &lt;div class="center-content"&gt;
      &lt;p&gt;{{ $successDepositsCount }} Successful Deposits&lt;/p&gt;
    &lt;/div&gt;
    &lt;div class="setting-list"&gt;
      &lt;ul class="list-unstyled setting-option"&gt;
        &lt;li&gt;
          &lt;div class="setting-secondary"&gt;
            &lt;i class="icon-settings"&gt; &lt;/i&gt;
          &lt;/div&gt;
        &lt;/li&gt;
        &lt;li&gt;
          &lt;i class="view-html fa fa-code font-secondary"&gt;&lt;/i&gt;
        &lt;/li&gt;
        &lt;li&gt;
          &lt;i class="icofont icofont-maximize full-card font-secondary"&gt;&lt;/i&gt;
        &lt;/li&gt;
        &lt;li&gt;
          &lt;i class="icofont icofont-minus minimize-card font-secondary"&gt;&lt;/i&gt;
        &lt;/li&gt;
        &lt;li&gt;
          &lt;i class="icofont icofont-refresh reload-card font-secondary"&gt;&lt;/i&gt;
        &lt;/li&gt;
        &lt;li&gt;
          &lt;i class="icofont icofont-error close-card font-secondary"&gt;&lt;/i&gt;
        &lt;/li&gt;
      &lt;/ul&gt;
    &lt;/div&gt;
  &lt;/div&gt;
 &lt;/div&gt;
 &lt;div class="transaction-totalbal"&gt;
  &lt;h2&gt; ${{ number_format($totalDeposit, 2) }} 
  &lt;/h2&gt;
  &lt;p&gt;Total Deposit&lt;/p&gt;
 &lt;/div&gt;
  &lt;div class="card-body p-0"&gt;
    &lt;div id="chart-deposit-dash"&gt;&lt;/div&gt;
  &lt;/div&gt;
 &lt;/div&gt;</code></pre>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
@endsection

@section('js')
<script>
  (function() {
    var optionsDeposit = {
      series: [{
          name: 'Deposit ($)',
          data: (typeof window.chartDepositData !== 'undefined') ? window.chartDepositData : []
      }],
      chart: {
          height: 405,
          type: 'area',
          toolbar: {
              show: false
          }
      },
      dataLabels: {
          enabled: false
      },
      stroke: {
          curve: 'smooth'
      },
      xaxis: {
          type: 'datetime',
          min: (typeof window.chartDepositMinTime !== 'undefined') ? window.chartDepositMinTime : undefined,
          max: (typeof window.chartDepositMaxTime !== 'undefined') ? window.chartDepositMaxTime : undefined,
          show: false,
          labels: {
              show: false,
          },
          axisTicks: {
              show: false,
          },
          axisBorder: {
              show: false
          }
      },
      yaxis: {
          show: false,
      },
      tooltip: {
          x: {
              format: 'dd MMM yyyy'
          },
      },
      colors: [vihoAdminConfig.secondary],
      fill: {
          type: 'gradient',
          gradient: {
              shadeIntensity: 1,
              opacityFrom: 0.7,
              opacityTo: 0.9,
              stops: [0, 100]
          }
      },
      responsive: [
        {
          breakpoint: 1365,
          options: {
              chart: {
                  height: 220
              }
          },
        },
        {
          breakpoint: 575,
          options: {
              chart: {
                  height: 180
              }
          },
        },
        {
          breakpoint: 992,
          options: {
              chart: {
                  height: 250
              }
          },
        }
      ],
    };
    if (document.querySelector("#chart-deposit-dash")) {
        var chartDeposit = new ApexCharts(document.querySelector("#chart-deposit-dash"), optionsDeposit);
        chartDeposit.render();
    }
  })();
</script>
@endsection
