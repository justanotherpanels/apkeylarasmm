@php
    try {
        $setting = \App\Models\Setting::first();
    } catch (\Throwable $e) {
        $setting = null;
    }
    $siteName = $setting && $setting->site_name ? $setting->site_name : config('app.name', 'Apkey SMM');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>#1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower</title>
    <script>
        !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.async=!0,p.src=s.api_host.replace(".i.posthog.com","-assets.i.posthog.com")+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys getNextSurveyStep onSessionId".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
        posthog.init('{{ env("POSTHOG_API_KEY", "<ph_project_api_key>") }}', {
            api_host: '{{ env("POSTHOG_HOST", "https://us.i.posthog.com") }}',
            person_profiles: 'identified_only'
        });
    </script>

    <!-- Search Engine Optimization (SEO) -->
    <meta name="description" content="Tingkatkan follower Instagram, TikTok, & YouTube dengan #1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower. Layanan cepat, murah, & aman 24/7.">
    <meta name="keywords" content="smm panel, cheap smm panel, instagram followers, buy tiktok followers, youtube views, social media marketing, apkey smm, boost social media, instant smm services, smm panel malaysia">
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="#1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower">
    <meta property="og:description" content="Tingkatkan follower Instagram, TikTok, & YouTube dengan #1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower. Layanan cepat, murah, & aman 24/7.">
    <meta property="og:image" content="{{ asset('assets/images/smm_social_preview.png') }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="#1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower">
    <meta name="twitter:description" content="Tingkatkan follower Instagram, TikTok, & YouTube dengan #1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower. Layanan cepat, murah, & aman 24/7.">
    <meta name="twitter:image" content="{{ asset('assets/images/smm_social_preview.png') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ $setting && $setting->favicon_path ? $setting->favicon_path : asset('assets/images/favicon-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ $setting && $setting->logo_path ? $setting->logo_path : asset('assets/images/logo-icon.png') }}">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@graph": [
        {
          "@@type": "WebSite",
          "@@id": "{{ url('/') }}/#website",
          "url": "{{ url('/') }}",
          "name": "{{ $siteName }}",
          "description": "Tingkatkan follower Instagram, TikTok, & YouTube dengan #1 SMM Panel Malaysia Terbaik & Terpercaya | Buy Social Media Follower. Layanan cepat, murah, & aman 24/7.",
          "publisher": {
            "@@id": "{{ url('/') }}/#organization"
          },
          "inLanguage": "{{ str_replace('_', '-', app()->getLocale()) }}"
        },
        {
          "@@type": "Organization",
          "@@id": "{{ url('/') }}/#organization",
          "name": "{{ $siteName }}",
          "url": "{{ url('/') }}",
          "logo": {
            "@@type": "ImageObject",
            "@@id": "{{ url('/') }}/#logo",
            "url": "{{ $setting && $setting->logo_path ? $setting->logo_path : asset('assets/images/logo-icon.png') }}",
            "caption": "{{ $siteName }} Logo"
          },
          "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "{{ $setting && $setting->whatsapp_url ? (str_contains($setting->whatsapp_url, 'wa.me/') ? '+' . substr($setting->whatsapp_url, strpos($setting->whatsapp_url, 'wa.me/') + 6) : '+6289660081616') : '+6289660081616' }}",
            "contactType": "customer service",
            "url": "{{ $setting && $setting->whatsapp_url ? $setting->whatsapp_url : 'https://wa.me/6289660081616' }}"
          }
        }
      ]
    }
    </script>
    
    <!-- Load TailwindCSS via CDN for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .text-gradient {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    {!! $setting->head_code ?? '' !!}
</head>
<body class="h-screen w-full overflow-hidden bg-[#0A0F1C] text-white flex items-center justify-center relative">
    
    <!-- Background glowing orbs -->
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-600 rounded-full mix-blend-screen filter blur-[150px] opacity-40 animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-cyan-500 rounded-full mix-blend-screen filter blur-[150px] opacity-40" style="animation: pulse 4s infinite alternate;"></div>

    <!-- Navigation Bar -->
    <nav class="absolute top-0 left-0 w-full z-50 py-6">
        <div class="container mx-auto px-6 md:px-12 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                @if($setting && $setting->logo_path)
                    <img class="h-10 w-auto object-contain" src="{{ $setting->logo_path }}" alt="{{ $siteName }}">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-cyan-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-xl font-bold tracking-wider">
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
                            APKEY<span class="text-cyan-400">SMM</span>
                        @endif
                    </span>
                @endif
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/member/') }}" class="text-sm font-semibold hover:text-cyan-400 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold hover:text-cyan-400 transition-colors">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-semibold px-5 py-2.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/10 transition-all shadow-[0_0_15px_rgba(255,255,255,0.05)] hover:shadow-[0_0_20px_rgba(255,255,255,0.2)]">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <div class="glass rounded-3xl p-10 md:p-16 max-w-4xl w-full mx-6 text-center relative z-10 flex flex-col items-center justify-center shadow-2xl">
        
        <!-- Icon -->
        <div class="mb-8 inline-flex items-center justify-center p-4 bg-white/5 rounded-full shadow-inner ring-1 ring-white/10">
            <svg class="w-10 h-10 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>

        <!-- Headings -->
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
            The <span class="text-gradient">Best</span> SMM Services
        </h1>
        
        <p class="text-lg md:text-2xl text-slate-300 mb-12 max-w-2xl font-light">
            Easy to Use. Highly Trusted. Elevate your social media presence instantly with our premium platform.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-5 items-center justify-center w-full">
            
            <!-- Google Play Button -->
            <a href="#" class="group w-full sm:w-auto flex items-center justify-center gap-3 bg-white text-[#0A0F1C] px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-slate-200 transition-all duration-300 hover:scale-105 shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                <svg viewBox="0 0 512 512" class="w-7 h-7" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#000" d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z"/>
                </svg>
                <span>Google Play</span>
            </a>

            <!-- WhatsApp Button 
            <a href="{{ $setting && $setting->whatsapp_url ? $setting->whatsapp_url : 'https://wa.me/6289660081616' }}" class="group w-full sm:w-auto flex items-center justify-center gap-3 bg-[#25D366] text-white px-8 py-4 rounded-2xl font-semibold text-lg hover:bg-[#20bd5a] transition-all duration-300 hover:scale-105 shadow-[0_0_20px_rgba(37,211,102,0.3)]">
                <svg viewBox="0 0 24 24" class="w-7 h-7" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.82 9.82 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                </svg>
                <span>WhatsApp</span>
            </a>
            -->
        </div>
        
    </div>

    {!! $setting->footer_code ?? '' !!}
</body>
</html>
