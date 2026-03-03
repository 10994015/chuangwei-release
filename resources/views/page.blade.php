{{-- resources/views/page.blade.php --}}
@extends('layouts.site')

@section('content')

  @php
    $componentMap = [
    'HEADER'           => 'components.navbar',
    'FOOTER'           => 'components.footer',
    'CAROUSEL_WALL'    => 'components.hero',
    'FIRST_PICTURE'    => 'elements.first_picture',
    'INDEX_NEWS'       => 'components.news-section',
    'INDEX_EVENT'      => 'components.events-section',
    'INDEX_PRODUCT'    => 'components.products-section',
    'INDEX_DONATION'   => 'components.donation-section',
    'BRIGHT_LAMP'      => 'components.about-section',  // ← 補上
    'NEWS_LIST'        => 'components.news-list-section',      // ← 補上
    'PRODUCT_LIST'     => 'components.product-list-section',   // ← 補上
    'ALBUM_LIST'       => 'components.album-list-section',     // ← 補上
    'EVENT_LIST'       => 'components.event-list-section',     // ← 補上
    'DONATION_PRODUCT' => 'components.donation-product',       // ← 補上
    ];
    $systemFrameTypes = array_keys($componentMap);
  @endphp

  @foreach($basemaps as $basemap)
    @php
      $bgType = $basemap['bgType'] ?? 'CONTENT';
      $pcImg  = $basemap['bgPcImgSrc']     ?? null;
      $tabImg = $basemap['bgTabletImgSrc'] ?? null;
      $mobImg = $basemap['bgPhoneImgSrc']  ?? null;

      $bgStyle = $pcImg
        ? "background-image: url('{$pcImg}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
        : '';
    @endphp

    <div
      class="basemap-section basemap-{{ strtolower($bgType) }}"
      @if($bgStyle) style="{{ $bgStyle }}" @endif
      @if($tabImg)  data-bg-tablet="{{ $tabImg }}" @endif
      @if($mobImg)  data-bg-mobile="{{ $mobImg }}" @endif
    >
      @foreach($basemap['frames'] ?? [] as $frame)
        @php
          $frameType = $frame['type'] ?? '';
          $isSystem  = in_array($frameType, $systemFrameTypes);
          $isCustom  = !$isSystem && str_starts_with($frameType, 'FRAME');
        @endphp

        @if($isCustom)
          {{-- 自訂框架（FRAME1_1 / FRAMEA / FRAME_1_1 / FRAME_A …） --}}
          @include('frames.custom_frame', ['frame' => $frame])

        @elseif($isSystem)
          {{-- 系統框架：透過 componentMap 對應到實際 view 路徑 --}}
          @php $systemView = $componentMap[$frameType] ?? null; @endphp
          @if($systemView && View::exists($systemView))
            @include($systemView, ['frame' => $frame])
          @endif

        @endif
      @endforeach
    </div>
  @endforeach

@endsection

@push('scripts')
<script>
  (function () {
    function applyResponsiveBackground() {
      var width    = window.innerWidth;
      var sections = document.querySelectorAll(
        '.basemap-section[data-bg-tablet], .basemap-section[data-bg-mobile]'
      );

      sections.forEach(function (el) {
        var tablet = el.dataset.bgTablet;
        var mobile = el.dataset.bgMobile;

        if (width <= 768 && mobile) {
          el.style.backgroundImage = "url('" + mobile + "')";
        } else if (width <= 1024 && tablet) {
          el.style.backgroundImage = "url('" + tablet + "')";
        }
      });
    }

    applyResponsiveBackground();
    window.addEventListener('resize', applyResponsiveBackground);
  })();
</script>
@endpush
