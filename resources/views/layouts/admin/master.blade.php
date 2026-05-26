@php
  $setting = \App\Models\Setting::first();
@endphp
<!DOCTYPE html>
<html lang="en"> 
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/logo/favicon-icon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/logo/favicon-icon.png') }}" type="image/x-icon">
    <title>{{ $setting && $setting->site_name ? $setting->site_name : 'Admin Dashboard' }} | Admin Dashboard</title>
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/font-awesome.css') }}">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/date-picker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/photoswipe.css') }}">
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">
    @yield('css')
  </head>
  <body>     
    <!-- Loader starts-->
    <div class="loader-wrapper">
      <div class="loader">
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-bar"></div>
        <div class="loader-ball"></div>
      </div>
    </div>
    <!-- Loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
      <!-- Page Header Start-->
      <div class="page-header">
        <div class="header-wrapper row m-0"> 
          <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"><a href="{{ route('admin.index') }}"><img class="img-fluid" src="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('assets/images/logo/logo.png') }}" alt="" style="max-height: 35px;"></a></div>
            <div class="toggle-sidebar">
              <div class="status_toggle sidebar-toggle d-flex">        
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g> 
                    <g> 
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M21.0003 6.6738C21.0003 8.7024 19.3551 10.3476 17.3265 10.3476C15.2979 10.3476 13.6536 8.7024 13.6536 6.6738C13.6536 4.6452 15.2979 3 17.3265 3C19.3551 3 21.0003 4.6452 21.0003 6.6738Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3467 6.6738C10.3467 8.7024 8.7024 10.3476 6.6729 10.3476C4.6452 10.3476 3 8.7024 3 6.6738C3 4.6452 4.6452 3 6.6729 3C8.7024 3 10.3467 4.6452 10.3467 6.6738Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M21.0003 17.2619C21.0003 19.2905 19.3551 20.9348 17.3265 20.9348C15.2979 20.9348 13.6536 19.2905 13.6536 17.2619C13.6536 15.2333 15.2979 13.5881 17.3265 13.5881C19.3551 13.5881 21.0003 15.2333 21.0003 17.2619Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M10.3467 17.2619C10.3467 19.2905 8.7024 20.9348 6.6729 20.9348C4.6452 20.9348 3 19.2905 3 17.2619C3 15.2333 4.6452 13.5881 6.6729 13.5881C8.7024 13.5881 10.3467 15.2333 10.3467 17.2619Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg>
              </div>
            </div>
          </div>
          <div class="left-side-header col ps-0 d-none d-md-block">
            <div class="input-group"><span class="input-group-text" id="basic-addon1">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g> 
                    <g> 
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2753 2.71436C16.0029 2.71436 19.8363 6.54674 19.8363 11.2753C19.8363 16.0039 16.0029 19.8363 11.2753 19.8363C6.54674 19.8363 2.71436 16.0039 2.71436 11.2753C2.71436 6.54674 6.54674 2.71436 11.2753 2.71436Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M19.8987 18.4878C20.6778 18.4878 21.3092 19.1202 21.3092 19.8983C21.3092 20.6783 20.6778 21.3097 19.8987 21.3097C19.1197 21.3097 18.4873 20.6783 18.4873 19.8983C18.4873 19.1202 19.1197 18.4878 19.8987 18.4878Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg></span>
              <input class="form-control" type="text" placeholder="Search here.." aria-label="search" aria-describedby="basic-addon1">
            </div>
          </div>
          <div class="nav-right col-10 col-sm-6 pull-right right-header p-0">
            <ul class="nav-menus">
              <li>
                <div class="mode animated backOutRight">
                  <svg class="lighticon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g>
                      <g>                 
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.1377 13.7902C19.2217 14.8742 16.3477 21.0542 10.6517 21.0542C6.39771 21.0542 2.94971 17.6062 2.94971 13.3532C2.94971 8.05317 8.17871 4.66317 9.67771 6.16217C10.5407 7.02517 9.56871 11.0862 11.1167 12.6352C12.6647 14.1842 17.0537 12.7062 18.1377 13.7902Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      </g>
                    </g>
                  </svg>
                  <svg class="darkicon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 12C17 14.7614 14.7614 17 12 17C9.23858 17 7 14.7614 7 12C7 9.23858 9.23858 7 12 7C14.7614 7 17 9.23858 17 12Z"></path>
                    <path d="M18.3117 5.68834L18.4286 5.57143M5.57144 18.4286L5.68832 18.3117M12 3.07394V3M12 21V20.9261M3.07394 12H3M21 12H20.9261M5.68831 5.68834L5.5714 5.57143M18.4286 18.4286L18.3117 18.3117" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </div>
              </li>
              <li class="d-md-none resp-serch-input">
                <div class="resp-serch-box"><i data-feather="search"></i></div>
                <div class="form-group search-form">
                  <input type="text" placeholder="Search here...">
                </div>
              </li>

              <li class="maximize"><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g> 
                      <g>    
                        <path d="M2.99609 8.71995C3.56609 5.23995 5.28609 3.51995 8.76609 2.94995" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M8.76616 20.99C5.28616 20.41 3.56616 18.7 2.99616 15.22L2.99516 15.224C2.87416 14.504 2.80516 13.694 2.78516 12.804" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M21.2446 12.804C21.2246 13.694 21.1546 14.504 21.0346 15.224L21.0366 15.22C20.4656 18.7 18.7456 20.41 15.2656 20.99" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M15.2661 2.94995C18.7461 3.51995 20.4661 5.23995 21.0361 8.71995" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      </g>
                    </g>
                  </svg></a></li>

            </ul>
          </div>
          <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">                        
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details">
            <div class="ProfileCard-realName">@{{name}}</div>
            </div>
            </div>
          </script>
          <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
        </div>
      </div>
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <div class="sidebar-wrapper"> 
          <div>
            <div class="logo-wrapper"><a href="{{ route('admin.index') }}"><img class="img-fluid for-light" src="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('assets/images/logo/small-logo.png') }}" alt="" style="max-height: 35px;"><img class="img-fluid for-dark" src="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('assets/images/logo/small-white-logo.png') }}" alt="" style="max-height: 35px;"></a>
              <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            </div>
            <div class="logo-icon-wrapper"><a href="{{ route('admin.index') }}"><img class="img-fluid" src="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/logo-icon.png') }}" alt="" style="max-height: 30px;"></a></div>
            <nav class="sidebar-main">
              <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
              <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                  <li class="back-btn"><a href="{{ route('admin.index') }}"><img class="img-fluid" src="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/logo-icon.png') }}" alt="" style="max-height: 30px;"></a>
                    <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true">        </i></div>
                  </li>
                 

                  <li class="sidebar-list {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                   <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.index') }}">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g> 
                          <g> 
                            <path d="M9.07861 16.1355H14.8936" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.3999 13.713C2.3999 8.082 3.0139 8.475 6.3189 5.41C7.7649 4.246 10.0149 2 11.9579 2C13.8999 2 16.1949 4.235 17.6539 5.41C20.9589 8.475 21.5719 8.082 21.5719 13.713C21.5719 22 19.6129 22 11.9859 22C4.3589 22 2.3999 22 2.3999 13.713Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                          </g>
                        </g>
                      </svg><span>Dashboard</span></a></li>
                  <li class="sidebar-list {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">  
                   <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.user.index') }}">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g> 
                          <g> 
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.92234 21.8083C6.10834 21.8083 2.85034 21.2313 2.85034 18.9213C2.85034 16.6113 6.08734 14.5103 9.92234 14.5103C13.7363 14.5103 16.9943 16.5913 16.9943 18.9003C16.9943 21.2093 13.7573 21.8083 9.92234 21.8083Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.92231 11.2159C12.4253 11.2159 14.4553 9.1859 14.4553 6.6829C14.4553 4.1789 12.4253 2.1499 9.92231 2.1499C7.41931 2.1499 5.38931 4.1789 5.38931 6.6829C5.38031 9.1769 7.39631 11.2069 9.89031 11.2159H9.92231Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M19.1313 8.12891V12.1389" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M21.1776 10.1338H17.0876" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                          </g>
                        </g>
                      </svg><span>User</span></a>
                  </li>
                  <li class="sidebar-list {{ request()->routeIs('admin.smm.*') ? 'active' : '' }}">
                    <a class="sidebar-link sidebar-title {{ request()->routeIs('admin.smm.*') ? 'active' : '' }}" href="#">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g> 
                          <g> 
                            <path d="M15.7499 9.47167V6.43967C15.7549 4.35167 14.0659 2.65467 11.9779 2.64967C9.88887 2.64567 8.19287 4.33467 8.18787 6.42267V9.47167" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.94995 14.2074C2.94995 8.91344 5.20495 7.14844 11.969 7.14844C18.733 7.14844 20.988 8.91344 20.988 14.2074C20.988 19.5004 18.733 21.2654 11.969 21.2654C5.20495 21.2654 2.94995 19.5004 2.94995 14.2074Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                          </g>
                        </g>
                      </svg><span>SMM Service</span></a>
                    <ul class="sidebar-submenu {{ request()->routeIs('admin.smm.*') ? 'd-block' : '' }}">
                      <li class="{{ request()->routeIs('admin.smm.order') ? 'active' : '' }}">
                        <a href="{{ route('admin.smm.order') }}">Order</a>
                      </li>
                      <li class="{{ request()->routeIs('admin.smm.category*') ? 'active' : '' }}">
                        <a href="{{ route('admin.smm.category') }}">Category</a>
                      </li>
                      <li class="{{ request()->routeIs('admin.smm.service*') ? 'active' : '' }}">
                        <a href="{{ route('admin.smm.service') }}">Service</a>
                      </li>
                      <li class="{{ request()->routeIs('admin.smm.api*') ? 'active' : '' }}">
                        <a href="{{ route('admin.smm.api') }}">API</a>
                      </li>
                      <li class="{{ request()->routeIs('admin.smm.import') ? 'active' : '' }}">
                        <a href="{{ route('admin.smm.import') }}">Import</a>
                      </li>
                    </ul>
                  </li>
                 
                  
                  <li class="sidebar-list {{ request()->routeIs('admin.payment.*') ? 'active' : '' }}">
                    <a class="sidebar-link sidebar-title {{ request()->routeIs('admin.payment.*') ? 'active' : '' }}" href="#">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g> 
                          <g> 
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.92178 12.4462C1.84878 9.09619 3.10378 4.93119 6.62078 3.79919C8.47078 3.20219 10.7538 3.70019 12.0508 5.48919C13.2738 3.63419 15.6228 3.20619 17.4708 3.79919C20.9868 4.93119 22.2488 9.09619 21.1768 12.4462C19.5068 17.7562 13.6798 20.5222 12.0508 20.5222C10.4228 20.5222 4.64778 17.8182 2.92178 12.4462Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M15.7885 7.56396C16.9955 7.68796 17.7505 8.64496 17.7055 9.98596" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                          </g>
                        </g>
                      </svg><span>Payment</span></a>
                    <ul class="sidebar-submenu {{ request()->routeIs('admin.payment.*') ? 'd-block' : '' }}">
                      <li class="{{ request()->routeIs('admin.payment.history') ? 'active' : '' }}">
                        <a href="{{ route('admin.payment.history') }}">History</a>
                      </li>
                      <li class="{{ request()->routeIs('admin.payment.settings') ? 'active' : '' }}">
                        <a href="{{ route('admin.payment.settings') }}">Payment Gateway</a>
                      </li>
                    </ul>
                  </li>
                  
                  <li class="sidebar-list {{ request()->routeIs('admin.ticket.*') ? 'active' : '' }}">
                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.ticket.index') }}">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g> 
                          <g>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.4399 13.9939C18.7789 13.9939 18.7789 9.87952 21.4399 9.87952C21.4399 5.11236 21.4399 3.41089 12.0449 3.41089C2.6499 3.41089 2.6499 5.11236 2.6499 9.87952C5.3109 9.87952 5.3109 13.9939 2.6499 13.9939C2.6499 18.762 2.6499 20.4635 12.0449 20.4635C21.4399 20.4635 21.4399 18.762 21.4399 13.9939Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.0449 9.17114C11.3619 9.17114 11.2969 10.2606 10.8909 10.6462C10.4839 11.0308 9.22087 10.5912 9.04487 11.2743C8.86987 11.9583 10.0069 12.1904 10.1479 12.7768C10.2879 13.3632 9.59387 14.1875 10.1869 14.5986C10.7809 15.0079 11.4199 14.0804 12.0449 14.0804C12.6699 14.0804 13.3089 15.0079 13.9029 14.5986C14.4969 14.1875 13.8019 13.3632 13.9419 12.7768C14.0829 12.1904 15.2199 11.9583 15.0449 11.2743C14.8689 10.5912 13.6059 11.0308 13.1989 10.6462C12.7929 10.2606 12.7279 9.17114 12.0449 9.17114Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                          </g>
                        </g>
                      </svg><span>Support Ticket</span></a></li>

                  <li class="sidebar-list {{ request()->routeIs('admin.whatsapp.*') ? 'active' : '' }}">
                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.whatsapp.index') }}">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                          <path d="M12 2C6.477 2 2 6.477 2 12c0 1.764.457 3.42 1.258 4.88L2 22l5.242-1.218C8.618 21.554 10.264 22 12 22c5.523 22 10-4.477 10-10S17.523 2 12 2zm5.087 14.536c-.214.604-1.246 1.156-1.748 1.206-.45.044-.997.228-3.155-.668-2.607-1.082-4.269-3.72-4.397-3.89-.128-.17-1.047-1.393-1.047-2.656 0-1.263.655-1.884.89-2.14.234-.256.51-.32.68-.32.17 0 .34.004.488.012.16.01.378-.063.593.456.223.543.723 1.764.787 1.892.064.128.106.277.021.447-.085.17-.128.277-.255.426-.128.149-.27.32-.383.447-.128.138-.266.29-.117.543.15.255.666 1.1 1.432 1.782.986.877 1.815 1.147 2.07 1.275.255.128.404.106.553-.064.15-.17.638-.745.81-1.002.17-.255.34-.213.574-.128.234.085 1.488.702 1.744.83.255.128.425.192.488.298.064.106.064.617-.15 1.221z" fill="#130F26" stroke="none"></path>
                        </g>
                      </svg><span>WhatsApp</span></a></li>

                  <li class="sidebar-list {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.settings.index') }}">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g>
                          <g>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.8064 7.62325L20.184 6.54325C19.6574 5.62675 18.4911 5.31193 17.5746 5.83849V5.83849C17.1387 6.09765 16.6189 6.17152 16.1271 6.04373C15.6353 5.91594 15.2126 5.59694 14.9534 5.16097C14.7837 4.87985 14.6929 4.55765 14.6898 4.22841V4.22841C14.7053 3.71085 14.5088 3.20921 14.1463 2.83633C13.7839 2.46345 13.2869 2.25289 12.7692 2.25H11.5149C11.0075 2.25 10.5211 2.45209 10.1622 2.81126C9.80334 3.17044 9.60155 3.65701 9.60205 4.16451V4.16451C9.58756 5.21104 8.73547 6.0531 7.68892 6.05286C7.35968 6.04974 7.03748 5.95896 6.75636 5.78929V5.78929C5.83988 5.26273 4.67353 5.57755 4.14695 6.49405L3.47819 7.62325C2.9522 8.53898 3.26382 9.70502 4.17895 10.2323V10.2323C4.78135 10.5786 5.1502 11.2207 5.1502 11.9166C5.1502 12.6126 4.78135 13.2547 4.17895 13.601V13.601C3.26489 14.1248 2.95257 15.2882 3.47819 16.2015V16.2015L4.10989 17.2905C4.3617 17.7368 4.77906 18.0695 5.27232 18.2138C5.76559 18.3581 6.29638 18.3022 6.74842 18.0579V18.0579C7.18545 17.8006 7.70546 17.7294 8.19499 17.8598C8.68452 17.9902 9.10379 18.3118 9.35874 18.7495C9.5284 19.0306 9.61917 19.3528 9.62229 19.682V19.682C9.62229 20.7385 10.4785 21.5948 11.535 21.5948H12.7692C13.8222 21.5948 14.677 20.7445 14.6898 19.6915V19.6915C14.6893 19.1824 14.8919 18.6944 15.2523 18.3348C15.6127 17.9751 16.1011 17.7733 16.6102 17.7748C16.9387 17.7832 17.2596 17.8739 17.5443 18.0385V18.0385C18.4602 18.5651 19.6265 18.2535 20.1536 17.3375V17.3375L20.8064 16.2015C21.0641 15.7626 21.1356 15.2414 21.0055 14.7507C20.8753 14.26 20.5537 13.8388 20.1133 13.5815V13.5815C19.6729 13.3242 19.3514 12.903 19.2212 12.4123C19.0911 11.9216 19.1625 11.4005 19.4203 10.9615C19.5905 10.6687 19.8367 10.4268 20.1334 10.2625V10.2625C21.0426 9.73519 21.3531 8.57557 20.8267 7.66325L20.8064 7.62325Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <circle cx="12.1" cy="11.9999" r="2.46173" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></circle>
                          </g>
                        </g>
                      </svg><span>Setting</span></a></li>

                  <li class="sidebar-list {{ request()->routeIs('admin.system-update.*') ? 'active' : '' }}">
                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.system-update.index') }}">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3V7" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 17V21" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M4.93 4.93L7.76 7.76" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M16.24 16.24L19.07 19.07" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M3 12H7" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M17 12H21" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M4.93 19.07L7.76 16.24" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M16.24 7.76L19.07 4.93" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <circle cx="12" cy="12" r="3" stroke="#130F26" stroke-width="1.5"></circle>
                      </svg><span>System Update</span></a></li>
                </ul>
                <div class="sidebar-img-section">
                  <div class="sidebar-img-content"><img class="img-fluid" src="{{ asset('assets/images/side-bar.png') }}" alt="">
                    <h4>Need Help ?</h4><a class="txt" href="https://pixelstrap.freshdesk.com/support/home">Raise ticket at "support@pixelstrap.com"</a>
                    <a class="btn btn-secondary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      @csrf
                    </form>
                  </div>
                </div>
              </div>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </nav>
          </div>
        </div>
        <!-- Page Sidebar Ends-->
        
        @if(isset($hasAdminUpdate) && $hasAdminUpdate)
            <div class="alert alert-warning m-3 text-center">
                <i data-feather="refresh-cw" class="me-2"></i>
                Update baru tersedia dari GitHub!
                <a href="{{ route('admin.system-update.index') }}" class="btn btn-sm btn-primary ms-2">
                    <i data-feather="download" class="me-1"></i> Update Sekarang
                </a>
            </div>
        @endif
        
        @yield('content')


        <!-- footer start-->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12 footer-copyright text-center">
                <p class="mb-0">Copyright 2022 © Zeta theme by pixelstrap  </p>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- feather icon js-->
    <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- scrollbar js-->
    <script src="{{ asset('assets/js/scrollbar/simplebar.js') }}"></script>
    <script src="{{ asset('assets/js/scrollbar/custom.js') }}"></script>
    <!-- Sidebar jquery-->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <!-- Plugins JS start-->
    <script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/chart/knob/knob.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart/knob/knob-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/default.js') }}"></script>
    <script src="{{ asset('assets/js/notify/index.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
    <script src="{{ asset('assets/js/photoswipe/photoswipe.min.js') }}"></script>
    <script src="{{ asset('assets/js/photoswipe/photoswipe-ui-default.min.js') }}"></script>
    <script src="{{ asset('assets/js/photoswipe/photoswipe.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead/handlebars.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead/typeahead.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead/typeahead.custom.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead-search/handlebars.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead-search/typeahead-custom.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/theme-customizer/customizer.js') }}"></script>
    @yield('scripts')
    <!-- login js-->
    <!-- Plugin used-->
  </body>
</html>
