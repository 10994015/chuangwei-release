{{-- resources/views/frames/elements/carousel_element.blade.php --}}
@php
  $value     = $element['value'] ?? [];
  $imgs      = $value['imgs']     ?? [];
  $autoPlay  = $value['autoPlay'] ?? true;
  $interval  = $value['interval'] ?? 3000;

  // 高度處理（對應 Vue 的 carouselHeight computed）
  $heightRaw = $value['height'] ?? 400;
    $height = (is_numeric($heightRaw) ? (int)$heightRaw : (int)filter_var($heightRaw, FILTER_SANITIZE_NUMBER_INT)) ?: 400;

  // 圖片來源：支援 {id, src} 新格式和純字串舊格式
  $displayImages = [];
  if (!empty($imgs)) {
      $first = $imgs[0];
      if (is_array($first) && isset($first['src'])) {
          // 新格式：{id, src}
          $displayImages = array_filter(array_column($imgs, 'src'));
      } else {
          // 舊格式：純 URL 字串
          $displayImages = array_filter($imgs);
      }
      $displayImages = array_values($displayImages);
  }

  // 無圖片時使用預設佔位圖
  if (empty($displayImages)) {
      $displayImages = [
          'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&h=450&fit=crop',
          'https://images.unsplash.com/photo-1528127269322-539801943592?w=800&h=450&fit=crop',
          'https://images.unsplash.com/photo-1604881991720-f91add269bed?w=800&h=450&fit=crop',
      ];
  }

  $imageCount = count($displayImages);
  $carouselId = 'carousel-' . uniqid(); // 同頁多個輪播互不干擾
@endphp

<div class="carousel-element">
  <div
    class="carousel-container"
    id="{{ $carouselId }}"
    data-autoplay="{{ $autoPlay ? 'true' : 'false' }}"
    data-interval="{{ $interval }}"
  >
    <div class="carousel-wrapper" style="height: {{ $height }}px;">

      {{-- 輪播軌道 --}}
      <div class="carousel-track">
        @foreach($displayImages as $index => $src)
          <div class="carousel-slide">
            <img
              src="{{ $src }}"
              alt="輪播圖片 {{ $index + 1 }}"
              class="carousel-image"
              loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
            />
          </div>
        @endforeach
      </div>

      {{-- 上一張按鈕 --}}
      @if($imageCount > 1)
        <button class="carousel-btn prev-btn" aria-label="上一張">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6" />
          </svg>
        </button>

        {{-- 下一張按鈕 --}}
        <button class="carousel-btn next-btn" aria-label="下一張">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 18l6-6-6-6" />
          </svg>
        </button>

        {{-- 指示器 --}}
        <div class="carousel-indicators">
          @foreach($displayImages as $index => $src)
            <button
              class="indicator {{ $index === 0 ? 'active' : '' }}"
              data-index="{{ $index }}"
              aria-label="第 {{ $index + 1 }} 張"
            ></button>
          @endforeach
        </div>
      @endif

    </div>
  </div>
</div>
