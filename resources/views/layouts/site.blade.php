<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  {{-- ==================== SEO ==================== --}}
  <title>{{ $settings['seoTitle'] ?? config('app.name') }}</title>
  <meta name="description" content="{{ $settings['seoDescription'] ?? '' }}" />
  @if(!empty($settings['seoKeywords']))
    <meta name="keywords" content="{{ $settings['seoKeywords'] }}" />
  @endif

  {{-- Open Graph --}}
  <meta property="og:title"       content="{{ $settings['seoTitle'] ?? config('app.name') }}" />
  <meta property="og:description" content="{{ $settings['seoDescription'] ?? '' }}" />
  <meta property="og:type"        content="website" />

  {{-- ==================== 字型（從 API 取得 frontFamily）==================== --}}
    @php
        $fontMap = [
            'bona-nova'           => 'Bona Nova',
            'cormorant-garamond'  => 'Cormorant Garamond',
            'inter'               => 'Inter',
            'montserrat'          => 'Montserrat',
            'playfair-display'    => 'Playfair Display',
            'ibm-plex-sans-tc'    => 'IBM Plex Sans TC',
            'lxgw-wenkai-mono-tc' => 'LXGW WenKai Mono TC',
            'noto-sans-tc'        => 'Noto Sans TC',
            'noto-serif-tc'       => 'Noto Serif TC',
            'noto-sans-sc'        => 'Noto Sans SC',
            'noto-serif-sc'       => 'Noto Serif SC',
            'ibm-plex-sans-sc'    => 'IBM Plex Sans SC',
        ];

        $locale   = request()->query('locale', 'ZH-TW');
        $fontId   = match(strtoupper($locale)) {
            'ZH-CN'       => $settings['frontFamilyZhCn'] ?? null,
            'EN-US', 'EN' => $settings['frontFamilyEnUs'] ?? null,
            default       => $settings['frontFamilyZhTw'] ?? null,
        };
        $fontName = $fontId ? ($fontMap[$fontId] ?? null) : null;
    @endphp

    @if(!empty($fontName))
        <style>
            body { font-family: '{{ $fontName }}', sans-serif; }
        </style>
    @endif

  {{-- ==================== CSS ==================== --}}
  @vite(['resources/css/app.css', 'resources/css/components.css', 'resources/js/app.js'])

  {{-- ==================== Meta Pixel ==================== --}}
  @if(!empty($settings['metaPixel']))
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '{{ $settings['metaPixel'] }}');
      fbq('track', 'PageView');
    </script>
    <noscript>
      <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $settings['metaPixel'] }}&ev=PageView&noscript=1"
      />
    </noscript>
  @endif
<style>
    #app{
        max-width: 1920px;
        margin: 0 auto;
    }
</style>
</head>
<body>

  {{-- ==================== 頁面主體內容 ==================== --}}
  <div id="app">
    @yield('content')
  </div>

  {{-- ==================== JS ==================== --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="{{ asset('js/carousel-element.js') }}"></script>
  <script src="{{ asset('js/map-element.js') }}" defer></script>
  {{-- 其他元件 JS 陸續補在這裡 --}}
  @stack('scripts')
</body>
</html>
