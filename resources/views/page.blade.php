{{-- resources/views/page.blade.php --}}
@extends('layouts.site')

@section('content')
  @foreach($basemaps as $basemap)
    @php
      $bgType  = $basemap['bgType'] ?? 'CONTENT';
      $pcImg   = $basemap['bgPcImgSrc']     ?? null;
      $tabImg  = $basemap['bgTabletImgSrc'] ?? null;
      $mobImg  = $basemap['bgPhoneImgSrc']  ?? null;
    @endphp

    <div
      class="basemap-section basemap-{{ strtolower($bgType) }}"
      @if($pcImg)
        style="background-image: url('{{ $pcImg }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
      @endif
      @if($tabImg) data-bg-tablet="{{ $tabImg }}" @endif
      @if($mobImg) data-bg-mobile="{{ $mobImg }}" @endif
    >
      @foreach($basemap['frames'] ?? [] as $frame)
        @php $frameType = $frame['type'] ?? ''; @endphp

        @if(str_starts_with($frameType, 'FRAME'))
          {{-- 自訂框架 --}}
          @include('frames.custom_frame', ['frame' => $frame])
        @else
          {{-- 系統框架 --}}
          @php $systemView = 'frames.system.' . strtolower($frameType); @endphp
          @if(View::exists($systemView))
            @include($systemView, ['frame' => $frame])
          @endif
        @endif
      @endforeach
    </div>
  @endforeach
@endsection

@push('scripts')
<script>
  // 依裝置切換背景圖（對應 PreviewPage.vue 的 getBasemapStyle 邏輯）
  (function () {
    function applyResponsiveBackground() {
      const width    = window.innerWidth
      const sections = document.querySelectorAll('.basemap-section[data-bg-tablet], .basemap-section[data-bg-mobile]')

      sections.forEach(function (el) {
        const tablet = el.dataset.bgTablet
        const mobile = el.dataset.bgMobile

        if (width <= 768 && mobile) {
          el.style.backgroundImage = "url('" + mobile + "')"
        } else if (width <= 1024 && tablet) {
          el.style.backgroundImage = "url('" + tablet + "')"
        }
        // 桌機版已由 inline style 設定，不需要再改
      })
    }

    applyResponsiveBackground()
    window.addEventListener('resize', applyResponsiveBackground)
  })()
</script>
@endpush
