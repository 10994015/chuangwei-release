{{-- resources/views/frames/elements/product_card.blade.php --}}
@php
  $value = $element['value'] ?? [];
  $image = $value['image'] ?? null;
  $tag   = $value['tag']   ?? '法會活動';
  $title = $value['title'] ?? '商品標題';
  $date  = $value['date']  ?? '2024-08-22';
@endphp

<div class="product-card">
  <div class="product-card__image">
    @if($image)
      <img src="{{ $image }}" alt="{{ $title ?: '商品圖片' }}" />
    @else
      <div class="product-card__placeholder">
        <span>🛍️</span>
      </div>
    @endif
  </div>
  <div class="product-card__content">
    <span class="product-card__tag">{{ $tag }}</span>
    <h3 class="product-card__title">{{ $title }}</h3>
    <p class="product-card__date">{{ $date }}</p>
  </div>
</div>
