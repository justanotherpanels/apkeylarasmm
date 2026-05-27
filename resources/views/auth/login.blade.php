@php
    try {
        $setting = \App\Models\Setting::first();
    } catch (\Throwable $e) {
        $setting = null;
    }
    $siteName = $setting && $setting->site_name ? $setting->site_name : config('app.name', 'Apkey SMM');
@endphp
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $siteName }}</title>
    <meta name="description" content="Log in to your account at {{ $siteName }}. Access high-quality, fast, and cheap social media services instantly.">
    <meta name="keywords" content="smm panel, cheap followers, buy followers, buy subscribers, social media marketing, cheapest smm panel">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Login - {{ $siteName }}">
    <meta property="og:description" content="Log in to your account at {{ $siteName }}. Access high-quality, fast, and cheap social media services instantly.">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="Login - {{ $siteName }}">
    <meta name="twitter:description" content="Log in to your account at {{ $siteName }}. Access high-quality, fast, and cheap social media services instantly.">

    <link rel="icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    
    <!-- Google Font (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
            },
            colors: {
              primary: {
                50: '#f5f3ff',
                100: '#ede9fe',
                200: '#ddd6fe',
                300: '#c4b5fd',
                400: '#a78bfa',
                500: '#8b5cf6',
                600: '#7c3aed',
                700: '#6d28d9',
                800: '#5b21b6',
                900: '#4c1d95',
              }
            }
          }
        }
      }
    </script>
    
    <!-- PostHog -->
    @if(env("POSTHOG_API_KEY"))
    <script>
        !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.async=!0,p.src=s.api_host.replace(".i.posthog.com","-assets.i.posthog.com")+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys getNextSurveyStep onSessionId".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
        posthog.init('{{ env("POSTHOG_API_KEY") }}', {
            api_host: '{{ env("POSTHOG_HOST", "https://us.i.posthog.com") }}',
            person_profiles: 'identified_only'
        });
    </script>
    @endif
    
    {!! $setting->head_code ?? '' !!}
  </head>
  <body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-primary-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/2 translate-y-1/2"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
      <div class="flex flex-col items-center">
        <!-- Logo -->
        @if($setting && $setting->logo_path)
            <img class="h-12 w-auto" src="{{ $setting->logo_path }}" alt="{{ $siteName }}">
        @else
            <div class="flex items-center gap-2 text-primary-600 font-bold text-2xl tracking-tight">
                <i data-lucide="zap" class="w-8 h-8 fill-primary-500"></i>
                <span>{{ $siteName }}</span>
            </div>
        @endif
      </div>

      <div class="bg-white p-8 sm:p-10 rounded-2xl border border-slate-100 shadow-xl shadow-slate-100/50 space-y-6">
        <div class="space-y-2">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Sign in to your account</h2>
            <p class="text-sm text-slate-500">Access high-quality social media marketing services instantly.</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-700 p-4 rounded-xl text-sm" role="alert">
                <div class="flex items-center gap-2 mb-2 font-semibold">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>Please correct the errors:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-4" method="POST" action="{{ route('login') }}">
          @csrf
          
          <div class="space-y-1.5">
            <label for="email" class="text-xs font-semibold text-slate-700 tracking-wide block">Email Address</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i data-lucide="mail" class="w-5 h-5"></i>
              </span>
              <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="your.email@example.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm" autofocus>
            </div>
          </div>

          <div class="space-y-1.5">
            <label for="password" class="text-xs font-semibold text-slate-700 tracking-wide block">Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i data-lucide="lock" class="w-5 h-5"></i>
              </span>
              <input id="password" name="password" type="password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
              <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                <i id="passwordToggleIcon" data-lucide="eye" class="w-5 h-5"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between text-sm pt-1">
            <div class="flex items-center">
              <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded transition-all duration-200">
              <label for="remember" class="ml-2 block text-xs text-slate-600 font-medium cursor-pointer">Remember me</label>
            </div>
            
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">Forgot password?</a>
            @endif
          </div>

          <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 shadow-lg shadow-primary-500/25 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
              <i data-lucide="log-in" class="w-4 h-4"></i>
              <span>Sign In</span>
            </button>
          </div>
        </form>

        @if (Route::has('register'))
          <div class="text-center pt-2">
            <p class="text-xs text-slate-500 font-medium">
              Don't have an account? 
              <a href="{{ route('register') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors ml-1">Create Account</a>
            </p>
          </div>
        @endif
      </div>
    </div>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();

      function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggleIcon');
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          toggleIcon.setAttribute('data-lucide', 'eye-off');
        } else {
          passwordInput.type = 'password';
          toggleIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
      }
    </script>
    {!! $setting->footer_code ?? '' !!}
  </body>
</html>
