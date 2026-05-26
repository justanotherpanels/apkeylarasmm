@php
  $setting = \App\Models\Setting::first();
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
    <link rel="icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/logo/favicon-icon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/logo/favicon-icon.png') }}" type="image/x-icon">
    <title>Verifikasi OTP - {{ $setting && $setting->site_name ? $setting->site_name : config('app.name') }}</title>
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
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">
    @if($setting && $setting->head_code)
        {!! $setting->head_code !!}
    @endif
    <style>
      .otp-input {
        width: 50px;
        height: 55px;
        text-align: center;
        font-size: 24px;
        font-weight: 600;
        border: 2px solid #ced4da;
        border-radius: 8px;
        margin: 0 5px;
      }
      .otp-input:focus {
        border-color: #7366ff;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(115, 102, 255, 0.25);
      }
      .otp-wrapper {
        display: flex;
        justify-content: center;
        margin: 20px 0;
      }
      .phone-masked {
        font-weight: 600;
        color: #7366ff;
      }
    </style>
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
              <form class="theme-form login-form" method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                
                <h4>Verifikasi OTP</h4>
                <h6>Masukkan kode 6 digit yang dikirim ke WhatsApp</h6>
                
                <div class="text-center my-3">
                  <p class="text-muted mb-1">Kode dikirim ke nomor:</p>
                  <p class="phone-masked">+{{ substr($user->phone, 0, 4) }}****{{ substr($user->phone, -4) }}</p>
                </div>

                @if (session('success'))
                  <div class="alert alert-success mb-4">
                    {{ session('success') }}
                  </div>
                @endif

                @if (session('info'))
                  <div class="alert alert-info mb-4">
                    {{ session('info') }}
                  </div>
                @endif

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
                  <div class="otp-wrapper">
                    <input type="text" class="otp-input" maxlength="1" data-index="0" autofocus>
                    <input type="text" class="otp-input" maxlength="1" data-index="1">
                    <input type="text" class="otp-input" maxlength="1" data-index="2">
                    <input type="text" class="otp-input" maxlength="1" data-index="3">
                    <input type="text" class="otp-input" maxlength="1" data-index="4">
                    <input type="text" class="otp-input" maxlength="1" data-index="5">
                  </div>
                  <input type="hidden" name="otp_code" id="otp_code">
                </div>

                <div class="form-group">
                  <button class="btn btn-primary btn-block" type="submit">Verifikasi</button>
                </div>

                <p class="mt-4 text-center"><a href="{{ route('login') }}">Kembali ke Login</a></p>
              </form>

              <div class="text-center mt-3">
                <p class="text-muted mb-2">Tidak menerima kode?</p>
                <form method="POST" action="{{ route('otp.resend') }}">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <button type="submit" class="btn btn-link p-0">Kirim Ulang OTP</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- page-wrapper end-->
    <!-- latest jquery-->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <!-- feather icon js-->
    <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- Sidebar jquery-->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- Plugins JS start-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="{{ asset('assets/js/script.js') }}"></script>
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
            otpInputs.eq(Math.min(digits.length, 5)).focus();
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
    @if($setting && $setting->footer_code)
        {!! $setting->footer_code !!}
    @endif
  </body>
</html>
