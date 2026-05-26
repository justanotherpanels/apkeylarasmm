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
    <title>Best SMM Panel India | Buy Instagram Follower & Likes</title>
    <meta name="description" content="Register an account on the Best SMM Panel India | Buy Instagram Follower & Likes. Grow your social media platforms with fast, cheap, and premium SMM services.">
    <meta name="keywords" content="smm panel, smm panel india, cheap instagram followers, buy instagram likes, buy followers, social media marketing">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Best SMM Panel India | Bu Instagram Follower & Likes">
    <meta property="og:description" content="Register an account on the Best SMM Panel India | Bu Instagram Follower & Likes. Grow your social media platforms with fast, cheap, and premium SMM services.">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="Best SMM Panel India | Bu Instagram Follower & Likes">
    <meta name="twitter:description" content="Register an account on the Best SMM Panel India | Bu Instagram Follower & Likes. Grow your social media platforms with fast, cheap, and premium SMM services.">

    <link rel="icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('themes/images/favicon.png') }}" type="image/x-icon">
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/fontawesome.css') }}">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/icofont.css') }}">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/themify.css') }}">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/flag-icon.css') }}">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/feather-icon.css') }}">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/bootstrap.css') }}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('themes/css/color-1.css') }}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/css/responsive.css') }}">
    
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
    <!-- page-wrapper Start-->
    <section>         
      <div class="container-fluid p-0">
        <div class="row">
          <div class="col-12">
            <div class="login-card">
              <form class="theme-form login-form" method="POST" action="{{ route('register.post') }}">
                @csrf
                <h4>Create your account</h4>
                <h6>Enter your personal details to create account</h6>

                @if ($errors->any())
                  <div class="alert alert-danger mb-4">
                      <ul class="mb-0">
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                @endif

                <div class="form-group">
                  <label>Your Name</label>
                  <div class="small-group">
                    <div class="input-group"><span class="input-group-text"><i class="icon-user"></i></span>
                      <input class="form-control" type="text" name="full_name" required="" placeholder="Full Name" value="{{ old('full_name') }}">
                    </div>
                    <div class="input-group"><span class="input-group-text"><i class="icon-user"></i></span>
                      <input class="form-control" type="text" name="username" required="" placeholder="Username" value="{{ old('username') }}">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Email Address</label>
                  <div class="input-group"><span class="input-group-text"><i class="icon-email"></i></span>
                    <input class="form-control" type="email" name="email" required="" placeholder="Test@gmail.com" value="{{ old('email') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label>WhatsApp Number</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="icon-mobile"></i></span>
                    <select class="form-control" name="country_code" id="country_code" required style="max-width: 120px;">
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
                    <input class="form-control" type="text" name="phone" required="" placeholder="81234567890" value="{{ old('phone') }}" pattern="[0-9]*" inputmode="numeric" id="phone_input">
                  </div>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <div class="input-group"><span class="input-group-text"><i class="icon-lock"></i></span>
                    <input class="form-control" type="password" name="password" required="" placeholder="*********">
                    <div class="show-hide"><span class="show">                         </span></div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="checkbox">
                    <input id="checkbox1" type="checkbox" required>
                    <label class="text-muted" for="checkbox1">Agree with <span>Privacy Policy</span></label>
                  </div>
                </div>
                <div class="form-group">
                  <button class="btn btn-primary btn-block" type="submit">Create Account</button>
                </div>
                <p>Already have an account?<a class="ms-2" href="{{ route('login') }}">Sign in</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- page-wrapper end-->
    <!-- latest jquery-->
    <script src="{{ asset('themes/js/jquery-3.5.1.min.js') }}"></script>
    <!-- feather icon js-->
    <script src="{{ asset('themes/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('themes/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- Sidebar jquery-->
    <script src="{{ asset('themes/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('themes/js/config.js') }}"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset('themes/js/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('themes/js/bootstrap/bootstrap.min.js') }}"></script>
    <!-- Plugins JS start-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="{{ asset('themes/js/script.js') }}"></script>
    <!-- login js-->
    <!-- Phone number validation - only numbers allowed -->
    <script>
      $(document).ready(function() {
        const phoneInput = $('#phone_input');
        
        phoneInput.on('input', function() {
          // Remove any non-digit characters
          let value = $(this).val().replace(/[^0-9]/g, '');
          $(this).val(value);
        });
        
        // Prevent pasting non-digit characters
        phoneInput.on('paste', function(e) {
          e.preventDefault();
          const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
          const digitsOnly = pastedText.replace(/[^0-9]/g, '');
          $(this).val(digitsOnly);
        });
        
        // Prevent non-digit key presses
        phoneInput.on('keypress', function(e) {
          const charCode = e.which ? e.which : e.keyCode;
          if (charCode < 48 || charCode > 57) {
            e.preventDefault();
            return false;
          }
        });
      });
    </script>
    <!-- Plugin used-->
    {!! $setting->footer_code ?? '' !!}
  </body>
</html>
