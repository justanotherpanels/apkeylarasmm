@php
  try {
      $setting = \App\Models\Setting::first();
  } catch (\Throwable $e) {
      $setting = null;
  }
@endphp
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <link rel="icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    <title>{{ $setting && $setting->site_name ? $setting->site_name : 'APKEY SMM' }} | Member dashboard</title>
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/fontawesome.css">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/icofont.css">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/themify.css">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/flag-icon.css">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/feather-icon.css">
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/animate.css">
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/chartist.css">
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/date-picker.css">
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/prism.css">
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/vector-map.css">
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/datatables.css">
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/bootstrap.css">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/style.css">
    <link id="color" rel="stylesheet" href="{{ asset("themes") }}/css/color-1.css" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset("themes") }}/css/responsive.css">
    <style>
      .page-wrapper.compact-wrapper .page-body-wrapper header.main-nav .main-navbar .nav-menu {
        height: calc(100vh - 100px) !important;
      }
    </style>
    @yield('css')
    
    <!-- PostHog -->
    <script>
        !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.async=!0,p.src=s.api_host.replace(".i.posthog.com","-assets.i.posthog.com")+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys getNextSurveyStep onSessionId".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
        posthog.init('{{ env("POSTHOG_API_KEY", "<ph_project_api_key>") }}', {
            api_host: '{{ env("POSTHOG_HOST", "https://us.i.posthog.com") }}',
            person_profiles: 'identified_only'
        });
    </script>
    <!-- End PostHog -->
    {!! $setting->head_code ?? '' !!}
  </head>
  <body>
    <!-- Loader starts-->
    <div class="loader-wrapper">
      <div class="theme-loader">    
        <div class="loader-p"></div>
      </div>
    </div>
    <!-- Loader ends-->
    <!-- page-wrapper Start       -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
      <!-- Page Header Start-->
      <div class="page-main-header">
        <div class="main-header-right row m-0">
          <div class="main-header-left">
            <div class="logo-wrapper"><a href="{{ route('member.index') }}"><img class="img-fluid" src="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('themes/images/logo/logo.png') }}" alt="" style="max-height: 35px;"></a></div>
            <div class="dark-logo-wrapper"><a href="{{ route('member.index') }}"><img class="img-fluid" src="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('themes/images/logo/dark-logo.png') }}" alt="" style="max-height: 35px;"></a></div>
            <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i></div>
          </div>
          <div class="left-menu-header col">
            <ul>
              <li>
                <form class="form-inline search-form">
                  <div class="search-bg"><i class="fa fa-search"></i>
                    <input class="form-control-plaintext" placeholder="Search here.....">
                  </div>
                </form><span class="d-sm-none mobile-search search-bg"><i class="fa fa-search"></i></span>
              </li>
            </ul>
          </div>
          <div class="nav-right col pull-right right-menu p-0 box-col-6">
            <ul class="nav-menus">
              <li><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i data-feather="maximize"></i></a></li>
              
              
              <li>
                <div class="mode"><i class="fa fa-moon-o"></i></div>
              </li>
              
              <li class="onhover-dropdown p-0">
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button class="btn btn-primary-light" type="submit"><i data-feather="log-out"></i>Log out</button>
                </form>
              </li>
            </ul>
          </div>
          <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
        </div>
      </div>
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper sidebar-icon">
        <!-- Page Sidebar Start-->
        <header class="main-nav">
          
          <nav>
            <div class="main-navbar">
              <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
              <div id="mainnav">           
                <ul class="nav-menu custom-scrollbar">
                  <li class="back-btn">
                    <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                  </li>
                  
                  <div>
                    <br>
                  </div>
                  
                  <li class="dropdown"><a class="nav-link menu-title link-nav" href="{{ route('member.index') }}"><i data-feather="git-pull-request"></i><span>Dashboard</span></a></li>
                  <li class="dropdown"><a class="nav-link menu-title link-nav" href="{{ route('member.smm.order') }}"><i data-feather="monitor"></i><span>New Order</span></a></li>
                  <li class="dropdown"><a class="nav-link menu-title link-nav" href="{{ route('member.payment.add') }}"><i data-feather="plus-circle"></i><span>Deposit</span></a></li>

                  <li class="dropdown"><a class="nav-link menu-title" href="javascript:void(0)"><i data-feather="shopping-bag"></i><span>History</span></a>
                    <ul class="nav-submenu menu-content">
                      <li><a href="{{ route('member.smm.history') }}">Order</a></li>
                      <li><a href="{{ route('member.payment.history') }}">Deposit</a></li><!-- 
                      <li><a href="{{ route('member.smm.refill') }}">Refill</a></li> -->
                      
                    </ul>
                  </li>
                  <li class="dropdown"><a class="nav-link menu-title" href="javascript:void(0)"><i data-feather="mail"></i><span>Pages</span></a>
                    <ul class="nav-submenu menu-content">
                      <li><a href="{{ route('member.pages.api') }}">API</a></li>
                      <li><a href="{{ route('member.pages.faq') }}">F.A.Q</a></li>
                      <li><a href="{{ route('member.pages.privacy') }}">Privacy</a></li>
                      <li><a href="{{ route('member.smm.service') }}">Service</a></li>
                    </ul>
                  </li>
                  
                  <li><a class="nav-link menu-title link-nav" href="{{ route('member.tickets.index') }}"><i data-feather="headphones"></i><span>Support Ticket</span></a></li>
                </ul>
              </div>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
          </nav>
        </header>
        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <!-- Container-fluid starts-->



        @yield('content')
          
          
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 footer-copyright">
                <p class="mb-0">Copyright 2023-24 © viho All rights reserved.</p>
              </div>
              <div class="col-md-6">
                <p class="pull-right mb-0">Hand crafted & made with <i class="fa fa-heart font-secondary"></i></p>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="{{ asset("themes") }}/js/jquery-3.5.1.min.js"></script>
    <!-- feather icon js-->
    <script src="{{ asset("themes") }}/js/icons/feather-icon/feather.min.js"></script>
    <script src="{{ asset("themes") }}/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="{{ asset("themes") }}/js/sidebar-menu.js"></script>
    <script src="{{ asset("themes") }}/js/config.js"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset("themes") }}/js/bootstrap/popper.min.js"></script>
    <script src="{{ asset("themes") }}/js/bootstrap/bootstrap.min.js"></script>
    <!-- Plugins JS start-->
    <script src="{{ asset("themes") }}/js/chart/chartist/chartist.js"></script>
    <script src="{{ asset("themes") }}/js/chart/chartist/chartist-plugin-tooltip.js"></script>
    <script src="{{ asset("themes") }}/js/chart/knob/knob.min.js"></script>
    <script src="{{ asset("themes") }}/js/chart/knob/knob-chart.js"></script>
    <script src="{{ asset("themes") }}/js/chart/apex-chart/apex-chart.js"></script>
    <script src="{{ asset("themes") }}/js/chart/apex-chart/stock-prices.js"></script>
    <script src="{{ asset("themes") }}/js/prism/prism.min.js"></script>
    <script src="{{ asset("themes") }}/js/clipboard/clipboard.min.js"></script>
    <script src="{{ asset("themes") }}/js/counter/jquery.waypoints.min.js"></script>
    <script src="{{ asset("themes") }}/js/counter/jquery.counterup.min.js"></script>
    <script src="{{ asset("themes") }}/js/counter/counter-custom.js"></script>
    <script src="{{ asset("themes") }}/js/custom-card/custom-card.js"></script>
    <script src="{{ asset("themes") }}/js/notify/bootstrap-notify.min.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-world-mill-en.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-us-aea-en.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-uk-mill-en.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-au-mill.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-chicago-mill-en.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-in-mill.js"></script>
    <script src="{{ asset("themes") }}/js/vector-map/map/jquery-jvectormap-asia-mill.js"></script>
    <script src="{{ asset("themes") }}/js/dashboard/default.js"></script>
    <script src="{{ asset("themes") }}/js/notify/index.js"></script>
    <script src="{{ asset("themes") }}/js/datepicker/date-picker/datepicker.js"></script>
    <script src="{{ asset("themes") }}/js/datepicker/date-picker/datepicker.en.js"></script>
    <script src="{{ asset("themes") }}/js/datepicker/date-picker/datepicker.custom.js"></script>
    <script src="{{ asset("themes") }}/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset("themes") }}/js/datatable/datatables/datatable.custom.js"></script>
    <script src="{{ asset("themes") }}/js/tooltip-init.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="{{ asset("themes") }}/js/script.js"></script>
   
    <!-- login js-->
    <!-- Plugin used-->
    @yield('js')
    {!! $setting->footer_code ?? '' !!}
  </body>
</html>