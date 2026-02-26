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
  @if(!empty($settings['frontFamily']))
    @php
      // Google Fonts 字型名稱轉換為 URL 格式（空格換成 +）
      $fontName    = $settings['frontFamily'];
      $fontEncoded = urlencode($fontName);
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family={{ $fontEncoded }}:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      body { font-family: '{{ $fontName }}', sans-serif; }
    </style>
  @endif

  {{-- ==================== CSS ==================== --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

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

</head>
<body>

  {{-- ==================== 頁面主體內容 ==================== --}}
  @yield('content')

  {{-- ==================== JS ==================== --}}
  <script src="{{ asset('js/carousel-element.js') }}"></script>

  {{-- 其他元件 JS 陸續補在這裡 --}}
  @stack('scripts')

</body>
</html>
