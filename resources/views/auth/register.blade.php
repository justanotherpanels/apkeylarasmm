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
    <title>Register - {{ $siteName }}</title>
    <meta name="description" content="Register an account on {{ $siteName }}. Grow your social media platforms with fast, cheap, and premium SMM services.">
    <meta name="keywords" content="smm panel, smm panel india, cheap instagram followers, buy instagram likes, buy followers, social media marketing">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Register - {{ $siteName }}">
    <meta property="og:description" content="Register an account on {{ $siteName }}. Grow your social media platforms with fast, cheap, and premium SMM services.">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="Register - {{ $siteName }}">
    <meta name="twitter:description" content="Register an account on {{ $siteName }}. Grow your social media platforms with fast, cheap, and premium SMM services.">

    <link class="favicon" rel="icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    <link class="favicon" rel="shortcut icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    
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

      <div class="bg-white p-8 sm:p-10 rounded-2xl border border-slate-100 shadow-xl shadow-slate-100/50">
        @if (!isset($step) || $step == 1)
          @if (!isset($showVerification))
            <!-- Step 1: Form Data Input -->
            <form class="space-y-6" method="POST" action="{{ route('register.step1') }}">
              @csrf
              <div class="space-y-2">
                  <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Create your account</h2>
                  <p class="text-sm text-slate-500">Enter your email and WhatsApp number to get started.</p>
              </div>

              @if (isset($success) || session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-xl text-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span class="font-semibold">{{ $success ?? session('success') }}</span>
                    </div>
                </div>
              @endif

              @if (isset($errors))
                @if (is_object($errors))
                  @php
                      $hasError = false;
                      foreach ((array)$errors as $key => $error) {
                          if (is_string($error)) {
                              $hasError = true;
                              break;
                          }
                      }
                  @endphp
                  @if ($hasError)
                    <div class="bg-red-50 border border-red-100 text-red-700 p-4 rounded-xl text-sm" role="alert">
                        <div class="flex items-center gap-2 mb-2 font-semibold">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            <span>Please correct the errors:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach ((array)$errors as $key => $error)
                                @if (is_string($error))
                                    <li>{{ $error }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                  @endif
                @elseif (method_exists($errors, 'any') && $errors->any())
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
              @endif

              <div class="space-y-1.5">
                <label for="email" class="text-xs font-semibold text-slate-700 tracking-wide block">Email Address (Gmail/Yahoo/Outlook only)</label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                  </span>
                  <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="your.email@example.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
                </div>
              </div>

              <div class="space-y-1.5">
                <label for="phone_input" class="text-xs font-semibold text-slate-700 tracking-wide block">WhatsApp Number</label>
                <div class="flex gap-2">
                  <div class="relative w-1/3 min-w-[125px]">
                    <select name="country_code" id="country_code" required class="w-full pl-3 pr-8 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm appearance-none cursor-pointer">
                          <option value="93" {{ old('country_code') == '93' ? 'selected' : '' }}>+93 Afghanistan</option>
                          <option value="355" {{ old('country_code') == '355' ? 'selected' : '' }}>+355 Albania</option>
                          <option value="213" {{ old('country_code') == '213' ? 'selected' : '' }}>+213 Algeria</option>
                          <option value="376" {{ old('country_code') == '376' ? 'selected' : '' }}>+376 Andorra</option>
                          <option value="244" {{ old('country_code') == '244' ? 'selected' : '' }}>+244 Angola</option>
                          <option value="54" {{ old('country_code') == '54' ? 'selected' : '' }}>+54 Argentina</option>
                          <option value="374" {{ old('country_code') == '374' ? 'selected' : '' }}>+374 Armenia</option>
                          <option value="61" {{ old('country_code') == '61' ? 'selected' : '' }}>+61 Australia</option>
                          <option value="43" {{ old('country_code') == '43' ? 'selected' : '' }}>+43 Austria</option>
                          <option value="994" {{ old('country_code') == '994' ? 'selected' : '' }}>+994 Azerbaijan</option>
                          <option value="973" {{ old('country_code') == '973' ? 'selected' : '' }}>+973 Bahrain</option>
                          <option value="880" {{ old('country_code') == '880' ? 'selected' : '' }}>+880 Bangladesh</option>
                          <option value="375" {{ old('country_code') == '375' ? 'selected' : '' }}>+375 Belarus</option>
                          <option value="32" {{ old('country_code') == '32' ? 'selected' : '' }}>+32 Belgium</option>
                          <option value="55" {{ old('country_code') == '55' ? 'selected' : '' }}>+55 Brazil</option>
                          <option value="673" {{ old('country_code') == '673' ? 'selected' : '' }}>+673 Brunei</option>
                          <option value="359" {{ old('country_code') == '359' ? 'selected' : '' }}>+359 Bulgaria</option>
                          <option value="855" {{ old('country_code') == '855' ? 'selected' : '' }}>+855 Cambodia</option>
                          <option value="237" {{ old('country_code') == '237' ? 'selected' : '' }}>+237 Cameroon</option>
                          <option value="1" {{ old('country_code') == '1' ? 'selected' : '' }}>+1 Canada/USA</option>
                          <option value="56" {{ old('country_code') == '56' ? 'selected' : '' }}>+56 Chile</option>
                          <option value="86" {{ old('country_code') == '86' ? 'selected' : '' }}>+86 China</option>
                          <option value="57" {{ old('country_code') == '57' ? 'selected' : '' }}>+57 Colombia</option>
                          <option value="385" {{ old('country_code') == '385' ? 'selected' : '' }}>+385 Croatia</option>
                          <option value="357" {{ old('country_code') == '357' ? 'selected' : '' }}>+357 Cyprus</option>
                          <option value="420" {{ old('country_code') == '420' ? 'selected' : '' }}>+420 Czech Republic</option>
                          <option value="45" {{ old('country_code') == '45' ? 'selected' : '' }}>+45 Denmark</option>
                          <option value="20" {{ old('country_code') == '20' ? 'selected' : '' }}>+20 Egypt</option>
                          <option value="372" {{ old('country_code') == '372' ? 'selected' : '' }}>+372 Estonia</option>
                          <option value="251" {{ old('country_code') == '251' ? 'selected' : '' }}>+251 Ethiopia</option>
                          <option value="358" {{ old('country_code') == '358' ? 'selected' : '' }}>+358 Finland</option>
                          <option value="33" {{ old('country_code') == '33' ? 'selected' : '' }}>+33 France</option>
                          <option value="49" {{ old('country_code') == '49' ? 'selected' : '' }}>+49 Germany</option>
                          <option value="233" {{ old('country_code') == '233' ? 'selected' : '' }}>+233 Ghana</option>
                          <option value="30" {{ old('country_code') == '30' ? 'selected' : '' }}>+30 Greece</option>
                          <option value="852" {{ old('country_code') == '852' ? 'selected' : '' }}>+852 Hong Kong</option>
                          <option value="36" {{ old('country_code') == '36' ? 'selected' : '' }}>+36 Hungary</option>
                          <option value="354" {{ old('country_code') == '354' ? 'selected' : '' }}>+354 Iceland</option>
                          <option value="91" {{ old('country_code') == '91' ? 'selected' : '' }}>+91 India</option>
                          <option value="62" {{ old('country_code', '62') == '62' ? 'selected' : '' }}>+62 Indonesia</option>
                          <option value="98" {{ old('country_code') == '98' ? 'selected' : '' }}>+98 Iran</option>
                          <option value="964" {{ old('country_code') == '964' ? 'selected' : '' }}>+964 Iraq</option>
                          <option value="353" {{ old('country_code') == '353' ? 'selected' : '' }}>+353 Ireland</option>
                          <option value="972" {{ old('country_code') == '972' ? 'selected' : '' }}>+972 Israel</option>
                          <option value="39" {{ old('country_code') == '39' ? 'selected' : '' }}>+39 Italy</option>
                          <option value="81" {{ old('country_code') == '81' ? 'selected' : '' }}>+81 Japan</option>
                          <option value="962" {{ old('country_code') == '962' ? 'selected' : '' }}>+962 Jordan</option>
                          <option value="7" {{ old('country_code') == '7' ? 'selected' : '' }}>+7 Kazakhstan</option>
                          <option value="254" {{ old('country_code') == '254' ? 'selected' : '' }}>+254 Kenya</option>
                          <option value="82" {{ old('country_code') == '82' ? 'selected' : '' }}>+82 South Korea</option>
                          <option value="965" {{ old('country_code') == '965' ? 'selected' : '' }}>+965 Kuwait</option>
                          <option value="856" {{ old('country_code') == '856' ? 'selected' : '' }}>+856 Laos</option>
                          <option value="371" {{ old('country_code') == '371' ? 'selected' : '' }}>+371 Latvia</option>
                          <option value="961" {{ old('country_code') == '961' ? 'selected' : '' }}>+961 Lebanon</option>
                          <option value="218" {{ old('country_code') == '218' ? 'selected' : '' }}>+218 Libya</option>
                          <option value="370" {{ old('country_code') == '370' ? 'selected' : '' }}>+370 Lithuania</option>
                          <option value="352" {{ old('country_code') == '352' ? 'selected' : '' }}>+352 Luxembourg</option>
                          <option value="853" {{ old('country_code') == '853' ? 'selected' : '' }}>+853 Macau</option>
                          <option value="60" {{ old('country_code') == '60' ? 'selected' : '' }}>+60 Malaysia</option>
                          <option value="960" {{ old('country_code') == '960' ? 'selected' : '' }}>+960 Maldives</option>
                          <option value="356" {{ old('country_code') == '356' ? 'selected' : '' }}>+356 Malta</option>
                          <option value="52" {{ old('country_code') == '52' ? 'selected' : '' }}>+52 Mexico</option>
                          <option value="373" {{ old('country_code') == '373' ? 'selected' : '' }}>+373 Moldova</option>
                          <option value="377" {{ old('country_code') == '377' ? 'selected' : '' }}>+377 Monaco</option>
                          <option value="976" {{ old('country_code') == '976' ? 'selected' : '' }}>+976 Mongolia</option>
                          <option value="212" {{ old('country_code') == '212' ? 'selected' : '' }}>+212 Morocco</option>
                          <option value="95" {{ old('country_code') == '95' ? 'selected' : '' }}>+95 Myanmar</option>
                          <option value="977" {{ old('country_code') == '977' ? 'selected' : '' }}>+977 Nepal</option>
                          <option value="31" {{ old('country_code') == '31' ? 'selected' : '' }}>+31 Netherlands</option>
                          <option value="64" {{ old('country_code') == '64' ? 'selected' : '' }}>+64 New Zealand</option>
                          <option value="234" {{ old('country_code') == '234' ? 'selected' : '' }}>+234 Nigeria</option>
                          <option value="47" {{ old('country_code') == '47' ? 'selected' : '' }}>+47 Norway</option>
                          <option value="968" {{ old('country_code') == '968' ? 'selected' : '' }}>+968 Oman</option>
                          <option value="92" {{ old('country_code') == '92' ? 'selected' : '' }}>+92 Pakistan</option>
                          <option value="970" {{ old('country_code') == '970' ? 'selected' : '' }}>+970 Palestine</option>
                          <option value="507" {{ old('country_code') == '507' ? 'selected' : '' }}>+507 Panama</option>
                          <option value="51" {{ old('country_code') == '51' ? 'selected' : '' }}>+51 Peru</option>
                          <option value="63" {{ old('country_code') == '63' ? 'selected' : '' }}>+63 Philippines</option>
                          <option value="48" {{ old('country_code') == '48' ? 'selected' : '' }}>+48 Poland</option>
                          <option value="351" {{ old('country_code') == '351' ? 'selected' : '' }}>+351 Portugal</option>
                          <option value="974" {{ old('country_code') == '974' ? 'selected' : '' }}>+974 Qatar</option>
                          <option value="40" {{ old('country_code') == '40' ? 'selected' : '' }}>+40 Romania</option>
                          <option value="7" {{ old('country_code') == '7' ? 'selected' : '' }}>+7 Russia</option>
                          <option value="966" {{ old('country_code') == '966' ? 'selected' : '' }}>+966 Saudi Arabia</option>
                          <option value="381" {{ old('country_code') == '381' ? 'selected' : '' }}>+381 Serbia</option>
                          <option value="65" {{ old('country_code') == '65' ? 'selected' : '' }}>+65 Singapore</option>
                          <option value="421" {{ old('country_code') == '421' ? 'selected' : '' }}>+421 Slovakia</option>
                          <option value="386" {{ old('country_code') == '386' ? 'selected' : '' }}>+386 Slovenia</option>
                          <option value="27" {{ old('country_code') == '27' ? 'selected' : '' }}>+27 South Africa</option>
                          <option value="34" {{ old('country_code') == '34' ? 'selected' : '' }}>+34 Spain</option>
                          <option value="94" {{ old('country_code') == '94' ? 'selected' : '' }}>+94 Sri Lanka</option>
                          <option value="249" {{ old('country_code') == '249' ? 'selected' : '' }}>+249 Sudan</option>
                          <option value="46" {{ old('country_code') == '46' ? 'selected' : '' }}>+46 Sweden</option>
                          <option value="41" {{ old('country_code') == '41' ? 'selected' : '' }}>+41 Switzerland</option>
                          <option value="963" {{ old('country_code') == '963' ? 'selected' : '' }}>+963 Syria</option>
                          <option value="886" {{ old('country_code') == '886' ? 'selected' : '' }}>+886 Taiwan</option>
                          <option value="66" {{ old('country_code') == '66' ? 'selected' : '' }}>+66 Thailand</option>
                          <option value="670" {{ old('country_code') == '670' ? 'selected' : '' }}>+670 Timor Leste</option>
                          <option value="216" {{ old('country_code') == '216' ? 'selected' : '' }}>+216 Tunisia</option>
                          <option value="90" {{ old('country_code') == '90' ? 'selected' : '' }}>+90 Turkey</option>
                          <option value="256" {{ old('country_code') == '256' ? 'selected' : '' }}>+256 Uganda</option>
                          <option value="380" {{ old('country_code') == '380' ? 'selected' : '' }}>+380 Ukraine</option>
                          <option value="971" {{ old('country_code') == '971' ? 'selected' : '' }}>+971 UAE</option>
                          <option value="44" {{ old('country_code') == '44' ? 'selected' : '' }}>+44 United Kingdom</option>
                          <option value="598" {{ old('country_code') == '598' ? 'selected' : '' }}>+598 Uruguay</option>
                          <option value="998" {{ old('country_code') == '998' ? 'selected' : '' }}>+998 Uzbekistan</option>
                          <option value="58" {{ old('country_code') == '58' ? 'selected' : '' }}>+58 Venezuela</option>
                          <option value="84" {{ old('country_code') == '84' ? 'selected' : '' }}>+84 Vietnam</option>
                          <option value="967" {{ old('country_code') == '967' ? 'selected' : '' }}>+967 Yemen</option>
                          <option value="260" {{ old('country_code') == '260' ? 'selected' : '' }}>+260 Zambia</option>
                          <option value="263" {{ old('country_code') == '263' ? 'selected' : '' }}>+263 Zimbabwe</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-slate-400">
                      <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                  </div>
                  <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                      <i data-lucide="smartphone" class="w-5 h-5"></i>
                    </span>
                    <input id="phone_input" name="phone" type="text" value="{{ old('phone') }}" required placeholder="81234567890" pattern="[0-9]*" inputmode="numeric" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
                  </div>
                </div>
              </div>

              <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 shadow-lg shadow-primary-500/25 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                  <span>Send Verification Codes</span>
                </button>
              </div>

              <div class="text-center pt-2">
                <p class="text-xs text-slate-500 font-medium flex items-center justify-center gap-1.5">
                  <span>Already have an account?</span>
                  <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors">Sign In</a>
                </p>
              </div>
            </form>
          @else
            <!-- Step 1: Verification Codes input validation -->
            <div class="space-y-6">
              <div class="space-y-2">
                  <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Verify Your Account</h2>
                  <p class="text-sm text-slate-500">Enter the verification codes sent to your email and WhatsApp.</p>
              </div>

              @if (isset($success) || session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-xl text-sm" role="alert">
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span class="font-semibold">{{ $success ?? session('success') }}</span>
                    </div>
                </div>
              @endif

              @if (isset($errors))
                @if (is_object($errors))
                  @php
                      $hasError = false;
                      foreach ((array)$errors as $key => $error) {
                          if (is_string($error)) {
                              $hasError = true;
                              break;
                          }
                      }
                  @endphp
                  @if ($hasError)
                    <div class="bg-red-50 border border-red-100 text-red-700 p-4 rounded-xl text-sm" role="alert">
                        <div class="flex items-center gap-2 mb-2 font-semibold">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            <span>Verification Errors:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach ((array)$errors as $key => $error)
                                @if (is_string($error))
                                    <li>{{ $error }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                  @endif
                @elseif (method_exists($errors, 'any') && $errors->any())
                  <div class="bg-red-50 border border-red-100 text-red-700 p-4 rounded-xl text-sm" role="alert">
                      <div class="flex items-center gap-2 mb-2 font-semibold">
                          <i data-lucide="alert-circle" class="w-4 h-4"></i>
                          <span>Verification Errors:</span>
                      </div>
                      <ul class="list-disc list-inside space-y-1 text-xs">
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                @endif
              @endif

              @if (!isset($brevoReady) || !$brevoReady)
                <div class="bg-amber-50 border border-amber-100 text-amber-800 p-4 rounded-xl text-xs flex gap-2">
                  <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0"></i>
                  <span>Email verification requires Brevo API. Please configure Brevo API first in the admin panel.</span>
                </div>
              @endif

              <!-- Email Verification -->
              <div class="bg-slate-50/50 p-5 rounded-xl border border-slate-100 space-y-3">
                  <div class="flex justify-between items-center">
                      <span class="text-xs font-bold text-slate-700 tracking-wide flex items-center gap-1.5">
                          <i data-lucide="mail" class="w-4 h-4 text-slate-500"></i> Email OTP Code
                      </span>
                      @if(isset($emailVerified) && $emailVerified)
                          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                              <i data-lucide="check-circle" class="w-3 h-3"></i> Verified
                          </span>
                      @else
                          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                              Pending
                          </span>
                      @endif
                  </div>
                  
                  <form method="POST" action="{{ route('register.verify-email') }}">
                      @csrf
                      <div class="flex gap-2">
                          <input class="flex-1 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm" type="text" name="email_otp" required placeholder="123456" maxlength="6" {{ isset($emailVerified) && $emailVerified ? 'disabled' : '' }}>
                          <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white font-semibold rounded-xl text-sm transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500" {{ isset($emailVerified) && $emailVerified ? 'disabled' : '' }}>
                              Verify
                          </button>
                      </div>
                  </form>
                  
                  <form method="POST" action="{{ route('register.resend-email') }}">
                      @csrf
                      <button type="submit" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors" {{ isset($emailVerified) && $emailVerified ? 'disabled' : '' }}>
                          Resend Email OTP
                      </button>
                  </form>
              </div>

              <!-- WhatsApp Verification -->
              <div class="bg-slate-50/50 p-5 rounded-xl border border-slate-100 space-y-3">
                  <div class="flex justify-between items-center">
                      <span class="text-xs font-bold text-slate-700 tracking-wide flex items-center gap-1.5">
                          <i data-lucide="smartphone" class="w-4 h-4 text-slate-500"></i> WhatsApp OTP Code
                      </span>
                      @if(isset($whatsappVerified) && $whatsappVerified)
                          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                              <i data-lucide="check-circle" class="w-3 h-3"></i> Verified
                          </span>
                      @else
                          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                              Pending
                          </span>
                      @endif
                  </div>
                  
                  <form method="POST" action="{{ route('register.verify-whatsapp') }}">
                      @csrf
                      <div class="flex gap-2">
                          <input class="flex-1 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm" type="text" name="whatsapp_otp" required placeholder="123456" maxlength="6" {{ isset($whatsappVerified) && $whatsappVerified ? 'disabled' : '' }}>
                          <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white font-semibold rounded-xl text-sm transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500" {{ isset($whatsappVerified) && $whatsappVerified ? 'disabled' : '' }}>
                              Verify
                          </button>
                      </div>
                  </form>
                  
                  <form method="POST" action="{{ route('register.resend-whatsapp') }}">
                      @csrf
                      <button type="submit" class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors" {{ isset($whatsappVerified) && $whatsappVerified ? 'disabled' : '' }}>
                          Resend WhatsApp OTP
                      </button>
                  </form>
              </div>
            </div>
          @endif
        @elseif ($step == 2)
          <!-- Step 2: Final Registration Fields -->
          <form class="space-y-4" method="POST" action="{{ route('register.step2') }}">
            @csrf
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Complete Your Account</h2>
                <p class="text-sm text-slate-500">Enter your personal details to wrap up registration.</p>
            </div>

            @if (session('success'))
              <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-xl text-sm" role="alert">
                  <div class="flex items-center gap-2">
                      <i data-lucide="check-circle" class="w-4 h-4"></i>
                      <span class="font-semibold">{{ session('success') }}</span>
                  </div>
              </div>
            @endif

            @if ($errors->any())
              <div class="bg-red-50 border border-red-100 text-red-700 p-4 rounded-xl text-sm" role="alert">
                  <div class="flex items-center gap-2 mb-2 font-semibold">
                      <i data-lucide="alert-circle" class="w-4 h-4"></i>
                      <span>Registration Errors:</span>
                  </div>
                  <ul class="list-disc list-inside space-y-1 text-xs">
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
            @endif

            <div class="space-y-1.5">
              <label for="full_name" class="text-xs font-semibold text-slate-700 tracking-wide block">Full Name</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                  <i data-lucide="user" class="w-5 h-5"></i>
                </span>
                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required placeholder="John Doe" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="username" class="text-xs font-semibold text-slate-700 tracking-wide block">Username</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                  <i data-lucide="at-sign" class="w-5 h-5"></i>
                </span>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required placeholder="johndoe12" class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
              </div>
            </div>

            <div class="space-y-1.5">
              <label for="password" class="text-xs font-semibold text-slate-700 tracking-wide block">Password</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                  <i data-lucide="lock" class="w-5 h-5"></i>
                </span>
                <input id="password" name="password" type="password" required placeholder="••••••••" minlength="8" class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200 text-sm">
                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                  <i id="passwordToggleIcon" data-lucide="eye" class="w-5 h-5"></i>
                </button>
              </div>
            </div>

            <div class="flex items-start pt-1">
              <input id="checkbox-agree" type="checkbox" required class="h-4 w-4 mt-0.5 text-primary-600 focus:ring-primary-500 border-slate-300 rounded transition-all duration-200 cursor-pointer">
              <label for="checkbox-agree" class="ml-2 block text-xs text-slate-600 font-medium cursor-pointer">
                I agree with the <a href="{{ route('member.pages.privacy') }}" target="_blank" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">Privacy Policy</a>
              </label>
            </div>

            <div class="pt-2">
              <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 active:bg-primary-800 shadow-lg shadow-primary-500/25 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Create Account</span>
              </button>
            </div>
            
            <div class="text-center pt-2">
              <p class="text-xs text-slate-500 font-medium flex items-center justify-center gap-1.5">
                <span>Already have an account?</span>
                <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700 transition-colors">Sign In</a>
              </p>
            </div>
          </form>
        @endif
      </div>
    </div>

    <!-- latest jquery-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
    
    <!-- Phone number validation - only numbers allowed -->
    <script>
      $(document).ready(function() {
        const phoneInput = $('#phone_input');
        if (phoneInput.length) {
          phoneInput.on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(value);
          });
          
          phoneInput.on('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            const digitsOnly = pastedText.replace(/[^0-9]/g, '');
            $(this).val(digitsOnly);
          });
          
          phoneInput.on('keypress', function(e) {
            const charCode = e.which ? e.which : e.keyCode;
            if (charCode < 48 || charCode > 57) {
              e.preventDefault();
              return false;
            }
          });
        }
      });
    </script>
    {!! $setting->footer_code ?? '' !!}
  </body>
</html>
