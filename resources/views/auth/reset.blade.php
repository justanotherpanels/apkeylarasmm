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
    <title>Reset Password - {{ $siteName }}</title>
    <meta name="description" content="Reset your password at {{ $siteName }}. Access high-quality, fast, and cheap social media services instantly.">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="noindex, nofollow">
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
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Reset Password</h2>
            <p class="text-sm text-slate-500">Enter the OTP code and your new password.</p>
        </div>

        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-xl text-sm" role="alert">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span class="font-semibold">{{ session('status') }}</span>
                </div>
            </div>
        @endif

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

        <form class="space-y-4" method="POST" action="{{ route('password.update') }}" id="passwordResetForm">
          @csrf
          <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

          <div class="space-y-1.5">
            <label for="email_display" class="text-xs font-semibold text-slate-700 tracking-wide block">Email/Username/Phone</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i data-lucide="user" class="w-5 h-5"></i>
              </span>
              <input id="email_display" name="email" type="text" value="{{ $email ?? old('email') }}" required placeholder="Email, Username, or Phone" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm" autofocus>
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-700 tracking-wide block text-center">OTP Verification Code</label>
            <div class="flex justify-center gap-2 my-2">
              <input type="text" class="otp-input w-12 h-12 text-center text-xl font-semibold border-2 border-slate-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 focus:outline-none transition-all duration-200" maxlength="1" data-index="0" autofocus>
              <input type="text" class="otp-input w-12 h-12 text-center text-xl font-semibold border-2 border-slate-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 focus:outline-none transition-all duration-200" maxlength="1" data-index="1">
              <input type="text" class="otp-input w-12 h-12 text-center text-xl font-semibold border-2 border-slate-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 focus:outline-none transition-all duration-200" maxlength="1" data-index="2">
              <input type="text" class="otp-input w-12 h-12 text-center text-xl font-semibold border-2 border-slate-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 focus:outline-none transition-all duration-200" maxlength="1" data-index="3">
              <input type="text" class="otp-input w-12 h-12 text-center text-xl font-semibold border-2 border-slate-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 focus:outline-none transition-all duration-200" maxlength="1" data-index="4">
              <input type="text" class="otp-input w-12 h-12 text-center text-xl font-semibold border-2 border-slate-200 rounded-xl focus:border-primary-500 focus:ring-2 focus:ring-primary-500/25 focus:outline-none transition-all duration-200" maxlength="1" data-index="5">
            </div>
            <input type="hidden" name="otp_code" id="otp_code">
          </div>

          <div class="space-y-1.5">
            <label for="password" class="text-xs font-semibold text-slate-700 tracking-wide block">New Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i data-lucide="lock" class="w-5 h-5"></i>
              </span>
              <input id="password" name="password" type="password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
              <button type="button" onclick="togglePasswordVisibility('password', 'passwordToggleIcon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                <i id="passwordToggleIcon" data-lucide="eye" class="w-5 h-5"></i>
              </button>
            </div>
          </div>

          <div class="space-y-1.5">
            <label for="password_confirmation" class="text-xs font-semibold text-slate-700 tracking-wide block">Confirm New Password</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <i data-lucide="lock" class="w-5 h-5"></i>
              </span>
              <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
              <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'passwordConfirmToggleIcon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                <i id="passwordConfirmToggleIcon" data-lucide="eye" class="w-5 h-5"></i>
              </button>
            </div>
          </div>

          <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 shadow-lg shadow-primary-500/25 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
              <i data-lucide="key-round" class="w-4 h-4"></i>
              <span>Reset Password</span>
            </button>
          </div>
        </form>

        <div class="flex flex-col gap-3 text-center pt-2">
          <p class="text-xs text-slate-500 font-medium flex items-center justify-center gap-1.5">
            <span>Remembered your password?</span>
            <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors">Sign In</a>
          </p>

          <div class="border-t border-slate-100 pt-3">
            <p class="text-xs text-slate-500 font-medium">Didn't receive the code?</p>
            <form method="POST" action="{{ route('password.email') }}" class="mt-1">
              @csrf
              <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
              <button type="submit" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors">Resend OTP</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- latest jquery-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();

      function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
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
    
    <!-- OTP Input Script -->
    <script>
      $(document).ready(function() {
        const otpInputs = $('.otp-input');
        const otpHidden = $('#otp_code');

        // Auto focus next input
        otpInputs.on('input', function() {
          const index = $(this).data('index');
          const value = $(this).val();

          // Only allow numbers
          $(this).val(value.replace(/[^0-9]/g, ''));

          if (value && index < 5) {
            otpInputs.eq(index + 1).focus();
          }

          // Update hidden field
          updateOtpValue();
        });

        // Handle backspace
        otpInputs.on('keydown', function(e) {
          const index = $(this).data('index');
          
          if (e.key === 'Backspace' && !$(this).val() && index > 0) {
            otpInputs.eq(index - 1).focus();
          }
        });

        // Handle paste
        otpInputs.first().on('paste', function(e) {
          e.preventDefault();
          const pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
          const digits = pastedData.replace(/[^0-9]/g, '').substring(0, 6);
          
          digits.split('').forEach((digit, i) => {
            otpInputs.eq(i).val(digit);
          });

          if (digits.length > 0) {
            otpInputs.eq(Math.min(digits.length - 1, 5)).focus();
          }

          updateOtpValue();
        });

        function updateOtpValue() {
          let otp = '';
          otpInputs.each(function() {
            otp += $(this).val();
          });
          otpHidden.val(otp);
        }
      });
    </script>
    {!! $setting->footer_code ?? '' !!}
  </body>
</html>
