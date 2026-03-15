{{-- resources/views/frames/elements/album_card.blade.php --}}
@php
  $image       = $element['value']['image']       ?? '/images/service-card/01.png';
  $tag         = $element['value']['tag']         ?? '相簿封面';
  $title       = $element['value']['title']       ?? '相簿標題';
  $description = $element['value']['description'] ?? '';
@endphp

<div class="album-card {{ $image ? 'has-image' : '' }}">
  <div class="album-card__image">
    @if($image)
      <img src="{{ $image }}" alt="{{ $title ?: '相簿圖片' }}" />
    @else
      <div class="album-card__placeholder">
        <span>📷</span>
      </div>
    @endif
  </div>
  <div class="album-card__content">
    <span class="album-card__tag">{{ $tag }}</span>
    <h3 class="album-card__title">{{ $title }}</h3>
    @if($description)
      <p class="album-card__description">{{ $description }}</p>
    @endif
  </div>
</div>
