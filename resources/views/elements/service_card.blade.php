{{-- resources/views/frames/elements/service_card.blade.php --}}
@php
  $value = $element['value'] ?? [];
  $image = $value['image'] ?? '/images/service-card/03.png';
  $tag   = $value['tag']   ?? '祈福服務';
  $title = $value['title'] ?? '服務標題';
  $date  = $value['date']  ?? '2024-08-22';
@endphp

<div class="service-card">
  <div class="service-card__image">
    @if($image)
      <img src="{{ $image }}" alt="{{ $title ?: '服務圖片' }}" />
    @else
      <div class="service-card__placeholder">
        <span>🙏</span>
      </div>
    @endif
  </div>
  <div class="service-card__content">
    <span class="service-card__tag">{{ $tag }}</span>
    <h3 class="service-card__title">{{ $title }}</h3>
    <p class="service-card__date">{{ $date }}</p>
  </div>
</div>
