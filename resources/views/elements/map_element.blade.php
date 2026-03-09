{{-- resources/views/frames/elements/map_element.blade.php --}}
@php
  // 支援兩種呼叫方式：
  // 1. custom_frame 直接傳入：$address, $lat, $lng, $zoom
  // 2. 舊版透過 $element['value'] 傳入
  $value   = $element['value'] ?? [];
  $lat     = $lat     ?? $value['lat']     ?? 25.033;
  $lng     = $lng     ?? $value['lng']     ?? 121.565;
  $zoom    = $zoom    ?? $value['zoom']    ?? 15;
  $address = $address ?? $value['address'] ?? '';
  $mapId   = 'map-' . uniqid();
  $apiKey  = env('VITE_GOOGLE_MAPS_API_KEY', '');
@endphp

<div class="map-element">
  <div class="map-container">

    {{-- Google Maps 容器 --}}
    <div
      id="{{ $mapId }}"
      class="map-display"
      data-lat="{{ $lat }}"
      data-lng="{{ $lng }}"
      data-zoom="{{ $zoom }}"
      data-address="{{ e($address) }}"
    ></div>

    {{-- 地址資訊 --}}
    @if($address)
      <div class="map-info">
        <div class="map-info__icon">📍</div>
        <div class="map-info__text">{{ $address }}</div>
      </div>
    @endif

  </div>
</div>

{{-- 只在第一次出現時注入 API Key 設定，避免多個地圖重複注入 --}}
@once
  <script>
    window.__GOOGLE_MAPS_API_KEY = "{{ $apiKey }}";
  </script>
  <script src="{{ asset('js/map-element.js') }}" defer></script>
@endonce
