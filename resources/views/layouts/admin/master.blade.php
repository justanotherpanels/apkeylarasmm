@php
  $setting = \App\Models\Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>{{ $setting && $setting->site_name ? $setting->site_name : 'Admin Dashboard' }} | Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="{{ asset('website/css/styles.css') }}"  rel="stylesheet" />
        <link href="{{ asset('themes/css/feather-icon.css') }}"  rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="{{ route('admin.index') }}">AdminPanel</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Main Menu</div>
                            <a class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}" href="{{ route('admin.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}" href="{{ route('admin.user.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                User
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.smm.*') ? 'active collapsed' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSMM" aria-expanded="{{ request()->routeIs('admin.smm.*') ? 'true' : 'false' }}" aria-controls="collapseSMM">
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                                SMM Service
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.smm.*') ? 'show' : '' }}" id="collapseSMM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link {{ request()->routeIs('admin.smm.order') ? 'active' : '' }}" href="{{ route('admin.smm.order') }}">Order</a>
                                    <a class="nav-link {{ request()->routeIs('admin.smm.category*') ? 'active' : '' }}" href="{{ route('admin.smm.category') }}">Category</a>
                                    <a class="nav-link {{ request()->routeIs('admin.smm.service*') ? 'active' : '' }}" href="{{ route('admin.smm.service') }}">Service</a>
                                    <a class="nav-link {{ request()->routeIs('admin.smm.api*') ? 'active' : '' }}" href="{{ route('admin.smm.api') }}">API</a>
                                    <a class="nav-link {{ request()->routeIs('admin.smm.import') ? 'active' : '' }}" href="{{ route('admin.smm.import') }}">Import</a>
                                </nav>
                            </div>
                            <a class="nav-link {{ request()->routeIs('admin.payment.*') ? 'active collapsed' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePayment" aria-expanded="{{ request()->routeIs('admin.payment.*') ? 'true' : 'false' }}" aria-controls="collapsePayment">
                                <div class="sb-nav-link-icon"><i class="fas fa-money-bill-wave"></i></div>
                                Payment
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse {{ request()->routeIs('admin.payment.*') ? 'show' : '' }}" id="collapsePayment" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link {{ request()->routeIs('admin.payment.history') ? 'active' : '' }}" href="{{ route('admin.payment.history') }}">History</a>
                                    <a class="nav-link {{ request()->routeIs('admin.payment.settings') ? 'active' : '' }}" href="{{ route('admin.payment.settings') }}">Payment Gateway</a>
                                </nav>
                            </div>
                            <a class="nav-link {{ request()->routeIs('admin.ticket.*') ? 'active' : '' }}" href="{{ route('admin.ticket.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-ticket-alt"></i></div>
                                Support Ticket
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}" href="{{ route('admin.whatsapp.index') }}">
                                <div class="sb-nav-link-icon"><i class="fab fa-whatsapp"></i></div>
                                WhatsApp
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.brevo-api.*') ? 'active' : '' }}" href="{{ route('admin.brevo-api.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                                Brevo API
                            </a>
                            <div class="sb-sidenav-menu-heading">System</div>
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                                Setting
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.system-update.*') ? 'active' : '' }}" href="{{ route('admin.system-update.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-sync"></i></div>
                                System Update
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    @if(isset($hasAdminUpdate) && $hasAdminUpdate)
                        <div class="alert alert-warning m-3 text-center">
                            <i class="fas fa-sync-alt me-2"></i>
                            Update baru tersedia dari GitHub!
                            <a href="{{ route('admin.system-update.index') }}" class="btn btn-sm btn-primary ms-2">
                                <i class="fas fa-download me-1"></i> Update Sekarang
                            </a>
                        </div>
                    @endif
                    @yield('content')
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('website/js/scripts.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('website/assets/demo/chart-area-demo.js') }}"></script>
        <script src="{{ asset('website/assets/demo/chart-bar-demo.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('website/js/datatables-simple-demo.js') }}"></script>
        <script src="{{ asset('themes/js/feather.min.js') }}"></script>
        <script>
            feather.replace();
        </script>
    </body>
</html>
