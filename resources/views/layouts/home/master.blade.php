@php
    try {
        $setting = \App\Models\Setting::first();
    } catch (\Throwable $e) {
        $setting = null;
    }
    $siteName = $setting && $setting->site_name ? $setting->site_name : config('app.name', 'Apkey SMM');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    
    <title>Best SMM Panel #1 Cheapest SMM Panel For Reseller</title>


    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/assets/img/favicons/favicon.png') }}">
    <link rel="manifest" href="{{ asset('themes/assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">


    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="{{ asset('themes/assets/css/theme.css') }}" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    @stack('styles')

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

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <nav class="navbar navbar-expand-lg navbar-light sticky-top" data-navbar-on-scroll="data-navbar-on-scroll">
@if($setting && $setting->logo_path)
        <div class="container"><a class="navbar-brand" href="{{ url('/') }}"><img src="{{ $setting->logo_path }}" height="40" alt="{{ $siteName }}" /></a>
@else 
@if($setting && $setting->site_name)
                            @php
                                $words = explode(' ', $setting->site_name);
                                $lastName = array_pop($words);
                                $firstName = implode(' ', $words);
                            @endphp
                            @if($firstName)
                                {{ $firstName }}<span class="text-cyan-400"> {{ $lastName }}</span>
                            @else
                                {{ $lastName }}
                            @endif
                        @else
                            
                        @endif
 @endif
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"> </span></button>
          <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item"><a class="nav-link" aria-current="page" href="{{ url('/') }}">Home</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ route('services') }}">Services</a></li>
            </ul>
@if (Route::has('login'))
                    @auth
            <div class="d-flex ms-lg-4"><a class="btn btn-secondary-outline" href="{{ url('/member/') }}">Dashboard</a>
            @else
            <div class="d-flex ms-lg-4"><a class="btn btn-secondary-outline" href="{{ route('login') }}">Sign In</a>
            @if (Route::has('register'))
            <a class="btn btn-warning ms-3" href="{{ route('register') }}">Sign Up</a></div>
 @endif
                    @endauth
                @endif

          </div>
        </div>
      </nav>
      <!-- ============================================-->
        @yield('content')
      
      <!-- <section> begin ============================-->
        <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="pb-2 pb-lg-5">

        <div class="container">
          <div class="row border-top border-top-secondary pt-7">
            <div class="col-lg-3 col-md-6 mb-4 mb-md-6 mb-lg-0 mb-sm-2 order-1 order-md-1 order-lg-1"><img class="mb-4" src="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('themes/assets/img/logo.svg') }}" width="184" alt="" /></div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 order-3 order-md-3 order-lg-2">
              <p class="fs-2 mb-lg-4">Quick Links</p>
              <ul class="list-unstyled mb-0">
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="{{ route('about') }}">About us</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Blog</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="{{ route('contact') }}">Contact</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">FAQ</a></li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 order-4 order-md-4 order-lg-3">
              <p class="fs-2 mb-lg-4">Legal stuff</p>
              <ul class="list-unstyled mb-0">
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Disclaimer</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="#!">Financing</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="{{ route('privacy') }}">Privacy Policy</a></li>
                <li class="mb-1"><a class="link-900 text-secondary text-decoration-none" href="{{ route('terms') }}">Terms of Service</a></li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-4 mb-lg-0 order-2 order-md-2 order-lg-4">
              <p class="fs-2 mb-lg-4">
                knowing you're always on the best energy deal.</p>
              <form class="mb-3">
                <input class="form-control" type="email" placeholder="Enter your phone Number" aria-label="phone" />
              </form>
              <button class="btn btn-warning fw-medium py-1">Sign up Now</button>
            </div>
          </div>
        </div><!-- end of .container-->

      </section>
      <section class="text-center py-0">

        <div class="container">
          <div class="container border-top py-3">
            <div class="row justify-content-between">
              <div class="col-12 col-md-auto mb-1 mb-md-0">
                <p class="mb-0">&copy; <script>document.write(new Date().getFullYear())</script> {{ $siteName }} </p>
              </div>
              <div class="col-12 col-md-auto">
                <p class="mb-0">
                  Made with<span class="fas fa-heart mx-1 text-danger"> </span>by <a class="text-decoration-none ms-1" href="https://sosifast.com" target="_blank">Sosifast</a></p>
              </div>
            </div>
          </div>
        </div><!-- end of .container-->

      </section>
      <!-- <section> close ============================-->
      <!-- ============================================-->


    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->


    <div class="modal fade" id="popupVideo" tabindex="-1" aria-labelledby="popupVideo" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <iframe class="rounded" style="width:100%;height:500px;" src="https://www.youtube.com/embed/_lhdhL4UDIo" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="{{ asset('themes/vendors/@popperjs/popper.min.js') }}"></script>
    <script src="{{ asset('themes/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('themes/vendors/is/is.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/polyfill.min.js?features=window.scroll"></script>
    <script src="{{ asset('themes/vendors/fontawesome/all.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('themes/assets/js/theme.js') }}"></script>
    @stack('scripts')

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;family=Volkhov:wght@700&amp;display=swap" rel="stylesheet">
    {!! $setting->footer_code ?? '' !!}
  </body>

</html>