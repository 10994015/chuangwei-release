{{-- resources/views/frames/_element.blade.php --}}
{{-- 接收 $el（由 custom_frame.blade.php 的 resolveElementVars() 產生） --}}

@php
  $elType  = $el['type']  ?? '';
  $elValue = $el['value'] ?? [];
  $elMeta  = $el['meta']  ?? [];
@endphp

@if($elType === 'IMG')
  @include('elements.image_element', ['src' => $elValue['src'] ?? null, 'alt' => $elValue['alt'] ?? '', 'meta' => $elMeta])
@elseif($elType === 'TEXT')
  @include('elements.text_element', ['text' => $elValue['text'] ?? '', 'meta' => $elMeta])
@elseif($elType === 'BUTTON')
  @include('elements.button_element', ['text' => $elValue['text'] ?? '', 'url' => $elValue['url'] ?? '#', 'meta' => $elMeta])
@elseif($elType === 'HORIZON_LINE')
  @include('elements.hline_element', ['meta' => $elMeta])
@elseif($elType === 'VERTICAL_LINE')
  @include('elements.vline_element', ['meta' => $elMeta])
@elseif($elType === 'CAROUSEL_IMG')
  @include('elements.carousel_element', ['imgs' => $elValue['imgs'] ?? [], 'meta' => $elMeta])
@elseif($elType === 'GOOGLE_MAP')
  @include('elements.map_element', ['address' => $elValue['address'] ?? '', 'lat' => $elValue['lat'] ?? null, 'lng' => $elValue['lng'] ?? null, 'zoom' => $elValue['zoom'] ?? 15, 'meta' => $elMeta])
@elseif($elType === 'ALBUM')
  @include('elements.album_card', ['data' => $elValue, 'meta' => $elMeta])
@elseif($elType === 'PRODUCT_CARD')
  @include('elements.product_card', ['data' => $elValue, 'meta' => $elMeta])
@elseif($elType === 'SERVICE_CARD')
  @include('elements.service_card', ['data' => $elValue, 'meta' => $elMeta])
@elseif($elType === 'EVENT_CARD')
  @include('elements.event_card', ['data' => $elValue, 'meta' => $elMeta])
@endif
