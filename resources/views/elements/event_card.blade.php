{{-- resources/views/frames/elements/event_card.blade.php --}}
@php
  $value       = $element['value'] ?? [];
  $image       = $value['image']       ?? null;
  $tag         = $value['tag']         ?? '法會活動';
  $title       = $value['title']       ?? '中元普渡法會';
  $description = $value['description'] ?? '2024年中元普渡法會活動紀錄';
@endphp

<div class="event-card">
  <div class="event-card__image">
    @if($image)
      <img src="{{ $image }}" alt="{{ $title ?: '活動圖片' }}" />
    @else
      <div class="event-card__placeholder">
        <span>🎉</span>
      </div>
    @endif
  </div>
  <div class="event-card__content">
    <span class="event-card__tag">{{ $tag }}</span>
    <h3 class="event-card__title">{{ $title }}</h3>
    <p class="event-card__description">{{ $description }}</p>
  </div>
</div>
