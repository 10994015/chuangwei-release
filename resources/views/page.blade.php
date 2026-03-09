{{-- resources/views/page.blade.php --}}
@extends('layouts.site')

@section('content')

  @php
    use App\Helpers\FrameHelper;

    $componentMap = [
      'HEADER'           => 'components.navbar',
      'FOOTER'           => 'components.footer',
      'CAROUSEL_WALL'    => 'components.hero',
      'FIRST_PICTURE'    => 'elements.first_picture',
      'INDEX_NEWS'       => 'components.news-section',
      'INDEX_EVENT'      => 'components.events-section',
      'INDEX_PRODUCT'    => 'components.products-section',
      'INDEX_DONATION'   => 'components.donation-section',
      'BRIGHT_LAMP'      => 'components.about-section',
      'NEWS_LIST'        => 'components.news-list-section',
      'PRODUCT_LIST'     => 'components.product-list-section',
      'ALBUM_LIST'       => 'components.album-list-section',
      'EVENT_LIST'       => 'components.event-list-section',
      'DONATION_PRODUCT' => 'components.donation-product',
    ];
    $systemFrameTypes = array_keys($componentMap);

    // 這幾種框架有自己的背景色邏輯，不套文字色主題變數
    $noThemeTypes = ['HEADER', 'FOOTER', 'INDEX_DONATION', 'CAROUSEL_WALL', 'FIRST_PICTURE'];
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
            if ($frameType === 'FOOTER') {
              $wrapperStyle = FrameHelper::resolveFooterStyle($frameData);
            } elseif ($frameType === 'INDEX_DONATION') {
              $wrapperStyle = FrameHelper::resolveDonationStyle($frameData);
            } elseif (!in_array($frameType, $noThemeTypes)) {
              // 一般系統框架套文字色主題
              $wrapperStyle = FrameHelper::resolveTextThemeCssVars($frameData);
            }
          } elseif ($isCustom) {
            // 自訂框架也繼承文字色主題（basemap 背景圖上文字色需要）
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

              @if($frameType === 'FOOTER')
                @include($systemView, [
                  'frame'         => $frame,
                  'tenantName'    => $footerData['tenantName']    ?? null,
                  'tenantPhone'   => $footerData['tenantPhone']   ?? null,
                  'tenantAddress' => $footerData['tenantAddress'] ?? null,
                  'tenantEmail'   => $footerData['tenantEmail']   ?? null,
                  'columns'       => $footerData['columns']       ?? [],
                  'templeId'      => $templeId,
                ])
              @else
                @include($systemView, ['frame' => $frame])
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
