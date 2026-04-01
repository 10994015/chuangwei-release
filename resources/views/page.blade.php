{{-- resources/views/page.blade.php --}}
@extends('layouts.site')

@section('content')

  @php
    use App\Helpers\FrameHelper;

    $componentMap = [
      // ── 原有框架 ──
      'HEADER'           => 'components.navbar',
      'FOOTER'           => 'components.footer',
      'CAROUSEL_WALL'    => 'components.hero',
      'FIRST_PICTURE'    => 'elements.first_picture',
      'INDEX_NEWS'       => 'components.news-section',
      'INDEX_EVENT'      => 'components.events-section',
      'INDEX_PRODUCT'    => 'components.products-section',
      'INDEX_DONATION'   => 'components.donation-section',
      'BRIGHT_LAMP'      => 'components.bright-lamp',
      'NEWS_LIST'        => 'components.news-list-section',
      'PRODUCT_LIST'     => 'components.product-list-section',
      'ALBUM_LIST'       => 'components.album-list-section',
      'EVENT_LIST'       => 'components.event-list-section',
      'DONATION_PRODUCT' => 'components.donation-product',
      // ── PV 系列框架 ──
      'PV_HEADER'        => 'components.pv-navbar',
      'PV_FOOTER'        => 'components.pv-footer',
      'PV_CAROUSEL_WALL' => 'components.pv-hero',
      'PV_FIRST_PICTURE' => 'components.pv-first-picture',
      'PV_INDEX_NEWS'    => 'components.pv-news-section',
      'PV_NEWS_LIST'     => 'components.pv-news-list-section',
      'PV_INDEX_PRODUCT' => 'components.pv-products-section',
      'PV_PRODUCT_LIST'  => 'components.pv-product-list-section',
      'PV_INDEX_SERVICE' => 'components.pv-services-section',
    ];

    $systemFrameTypes = array_keys($componentMap);

    // 這幾種框架有自己的背景色邏輯，不套文字色主題變數
    $noThemeTypes = [
      'HEADER', 'FOOTER', 'INDEX_DONATION', 'CAROUSEL_WALL', 'FIRST_PICTURE',
      'PV_HEADER', 'PV_FOOTER', 'PV_CAROUSEL_WALL', 'PV_FIRST_PICTURE',
    ];

    // FOOTER 類型（用來傳 footerData）
    $footerTypes = ['FOOTER', 'PV_FOOTER'];

    // HEADER 類型（用來判斷 headerFrame，PageController 已處理，這裡備用）
    $headerTypes = ['HEADER', 'PV_HEADER'];
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
          $frameData = $frame['data'] ?? [];
          $isSystem  = in_array($frameType, $systemFrameTypes);
          $isCustom  = !$isSystem && str_starts_with($frameType, 'FRAME');

          // ── 計算 wrapper inline style ──────────────────────────────────────
          $wrapperStyle = '';
          if ($isSystem) {
            if (in_array($frameType, $footerTypes)) {
              $wrapperStyle = FrameHelper::resolveFooterStyle($frameData);
            } elseif ($frameType === 'INDEX_DONATION') {
              $wrapperStyle = FrameHelper::resolveDonationStyle($frameData);
            } elseif (!in_array($frameType, $noThemeTypes)) {
              $wrapperStyle = FrameHelper::resolveTextThemeCssVars($frameData);
            }
          } elseif ($isCustom) {
            $wrapperStyle = FrameHelper::resolveTextThemeCssVars($frameData);
          }
        @endphp

        @if($isCustom)
          <div class="frame-wrapper" @if($wrapperStyle) style="{{ $wrapperStyle }}" @endif>
            @include('frames.custom_frame', ['frame' => $frame])
          </div>

        @elseif($isSystem)
          @php $systemView = $componentMap[$frameType] ?? null; @endphp
          @if($systemView && View::exists($systemView))

            <div class="system-frame-wrapper" @if($wrapperStyle) style="{{ $wrapperStyle }}" @endif>

              @if(in_array($frameType, $footerTypes))
                @include($systemView, [
                  'frame'         => $frame,
                  'footerData'    => $footerData ?? [],
                  'tenantName'    => $footerData['tenantName']    ?? null,
                  'tenantPhone'   => $footerData['tenantPhone']   ?? null,
                  'tenantAddress' => $footerData['tenantAddress'] ?? null,
                  'tenantEmail'   => $footerData['tenantEmail']   ?? null,
                  'columns'       => $footerData['columns']       ?? [],
                  'templeId'      => $templeId,
                ])
              @else
                @include($systemView, [
                  'frame'   => $frame,
                  'slug'    => $slug ?? 'home',
                  'locales' => $locales ?? [],
                ])
              @endif

            </div>

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
