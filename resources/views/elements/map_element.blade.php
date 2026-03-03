{{-- resources/views/frames/elements/map_element.blade.php --}}
@php
  $value   = $element['value'] ?? [];
  $lat     = $value['lat']     ?? 25.033;
  $lng     = $value['lng']     ?? 121.565;
  $zoom    = $value['zoom']    ?? 15;
  $address = $value['address'] ?? '';
  $mapId   = 'map-' . uniqid(); // 同頁多個地圖互不干擾
@endphp

<div class="map-element">
  <div class="map-container">

    {{-- Leaflet 地圖容器 --}}
    <div
      id="{{ $mapId }}"
      class="map-display"
      data-lat="{{ $lat }}"
      data-lng="{{ $lng }}"
      data-zoom="{{ $zoom }}"
      data-address="{{ $address }}"
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
