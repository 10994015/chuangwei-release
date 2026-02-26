{{-- resources/views/frames/elements/album_element.blade.php --}}
@php
  $value       = $element['value'] ?? [];
  $title       = $value['title']       ?? null;
  $columns     = $value['columns']     ?? 3;
  $maxDisplay  = $value['maxDisplay']  ?? 6;
  $showViewAll = $value['showViewAll'] ?? false;
  $albumId     = $value['albumId']     ?? null;
  $viewAllUrl  = $value['viewAllUrl']  ?? '#';

  // 照片來源：有 albumId 且有照片用真實資料，否則用預設佔位圖
  if ($albumId && !empty($value['photos'])) {
      $photos = $value['photos'];
  } else {
      $photos = [
          ['src' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=400&h=400&fit=crop', 'alt' => '照片 1'],
          ['src' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=400&h=400&fit=crop', 'alt' => '照片 2'],
          ['src' => 'https://images.unsplash.com/photo-1604881991720-f91add269bed?w=400&h=400&fit=crop', 'alt' => '照片 3'],
          ['src' => 'https://images.unsplash.com/photo-1590736969955-71cc94901144?w=400&h=400&fit=crop', 'alt' => '照片 4'],
          ['src' => 'https://images.unsplash.com/photo-1519315901367-f34ff9154487?w=400&h=400&fit=crop', 'alt' => '照片 5'],
          ['src' => 'https://images.unsplash.com/photo-1551361415-69c87624334f?w=400&h=400&fit=crop', 'alt' => '照片 6'],
          ['src' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&h=400&fit=crop', 'alt' => '照片 7'],
          ['src' => 'https://images.unsplash.com/photo-1610375461246-83df859d849d?w=400&h=400&fit=crop', 'alt' => '照片 8'],
      ];
  }

  $totalCount     = count($photos);
  $hasMore        = $totalCount > $maxDisplay;
  $displayPhotos  = $hasMore ? array_slice($photos, 0, $maxDisplay - 1) : $photos;
  $remainingCount = $hasMore ? $totalCount - ($maxDisplay - 1) : 0;
@endphp

<div class="album-element">
  <div class="album-container">

    {{-- 相簿標題 --}}
    @if($title)
      <div class="album-header">
        <h3 class="album-title">{{ $title }}</h3>
        <span class="photo-count">{{ $totalCount }} 張照片</span>
      </div>
    @endif

    {{-- 照片網格 --}}
    <div class="album-grid grid-cols-{{ $columns }}">

      @foreach($displayPhotos as $index => $photo)
        <div class="photo-item" data-index="{{ $index }}">
          <img
            src="{{ $photo['src'] }}"
            alt="{{ $photo['alt'] ?? '照片 ' . ($index + 1) }}"
            class="photo-image"
            loading="lazy"
          />
          <div class="photo-overlay">
            <div class="overlay-icon">🔍</div>
          </div>
        </div>
      @endforeach

      {{-- 顯示更多格子 --}}
      @if($hasMore)
        <a href="{{ $viewAllUrl }}" class="photo-item more-item">
          <div class="more-content">
            <div class="more-icon">+{{ $remainingCount }}</div>
            <div class="more-text">查看更多</div>
          </div>
        </a>
      @endif

    </div>

    {{-- 查看全部按鈕 --}}
    @if($showViewAll)
      <div class="album-footer">
        <a href="{{ $viewAllUrl }}" class="view-all-btn">
          查看完整相簿
          <svg class="arrow-icon" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
        </a>
      </div>
    @endif

  </div>
</div>
