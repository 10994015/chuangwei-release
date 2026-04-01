{{-- resources/views/components/pv-hero.blade.php --}}
@php
  $data = $frame['data'] ?? [];

  $imgs            = $data['caroiselWallImgs']     ?? [];
  $height          = $data['carouselWallHeight']   ?? 600;
  $autoPlay        = $data['carouselWallAutoPlay'] ?? true;
  $interval        = $data['carouselWallInterval'] ?? 5000;

  // 取出圖片 src 陣列
  $imageUrls = collect($imgs)->map(fn($img) => is_array($img) ? ($img['src'] ?? null) : $img)->filter()->values()->toArray();

  // fallback
  if (empty($imageUrls)) {
    $imageUrls = [
      'https://images.unsplash.com/photo-1548013146-72479768bada?w=1200&h=700&fit=crop',
      'https://images.unsplash.com/photo-1528127269322-539801943592?w=1200&h=700&fit=crop',
      'https://images.unsplash.com/photo-1604881991720-f91add269bed?w=1200&h=700&fit=crop',
    ];
  }

  $carouselId = 'pv-carousel-' . uniqid();
@endphp

<div
  class="pv-carousel"
  id="{{ $carouselId }}"
  style="height:{{ $height }}px;"
  data-autoplay="{{ $autoPlay ? 'true' : 'false' }}"
  data-interval="{{ $interval }}"
>
  <div class="pv-viewport">
    <div class="pv-track">
      {{-- 尾部 clone --}}
      @if(count($imageUrls) > 1)
        <div class="pv-slide" data-clone="true">
          <img src="{{ $imageUrls[count($imageUrls) - 1] }}" alt="輪播圖片" class="pv-img" />
        </div>
      @endif

      @foreach($imageUrls as $i => $src)
        <div class="pv-slide{{ $i === 0 ? ' is-active' : '' }}">
          <img src="{{ $src }}" alt="輪播圖片 {{ $i + 1 }}" class="pv-img" loading="{{ $i === 0 ? 'eager' : 'lazy' }}" />
        </div>
      @endforeach

      {{-- 頭部 clone --}}
      @if(count($imageUrls) > 1)
        <div class="pv-slide" data-clone="true">
          <img src="{{ $imageUrls[0] }}" alt="輪播圖片" class="pv-img" />
        </div>
      @endif
    </div>

    @if(count($imageUrls) > 1)
      <button class="pv-arrow pv-arrow--left" aria-label="上一張">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </button>
      <button class="pv-arrow pv-arrow--right" aria-label="下一張">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </button>

      <div class="pv-indicators">
        @foreach($imageUrls as $i => $src)
          <button class="pv-dot{{ $i === 0 ? ' active' : '' }}" aria-label="第 {{ $i + 1 }} 張"></button>
        @endforeach
      </div>
    @endif
  </div>
</div>

<style>
.pv-carousel {
  position: relative;
  width: 100%;
  background: transparent;
  user-select: none;
}
.pv-viewport {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  box-sizing: border-box;
  padding: 0 4%;
}
.pv-track {
  display: flex;
  height: 100%;
  transition: transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
}
.pv-track.no-transition { transition: none; }
.pv-slide {
  flex-shrink: 0;
  height: 100%;
  padding: 0 6px;
  box-sizing: border-box;
  opacity: 0.45;
  transition: opacity 0.45s ease;
}
.pv-slide.is-active { opacity: 1; }
.pv-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: 12px;
}
.pv-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: rgba(255,255,255,0.88);
  color: #333;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  transition: background 0.2s, transform 0.2s;
  box-shadow: 0 2px 12px rgba(0,0,0,0.2);
}
.pv-arrow svg { width: 18px; height: 18px; }
.pv-arrow--left  { left: 13%; }
.pv-arrow--right { right: 13%; }
.pv-arrow:hover  { background: #fff; transform: translateY(-50%) scale(1.08); }
.pv-indicators {
  position: absolute;
  bottom: 18px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 10;
}
.pv-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: none;
  background: rgba(255,255,255,0.4);
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 0;
}
.pv-dot.active { background: #fff; width: 24px; border-radius: 4px; }
.pv-dot:hover  { background: rgba(255,255,255,0.75); }

@media (max-width: 768px) {
  .pv-arrow { width: 34px; height: 34px; }
  .pv-arrow svg { width: 15px; height: 15px; }
  .pv-arrow--left  { left: 10%; }
  .pv-arrow--right { right: 10%; }
  .pv-viewport { padding: 0 8%; }
}
</style>

<script>
(function () {
  var carousel = document.getElementById('{{ $carouselId }}');
  if (!carousel) return;

  var track     = carousel.querySelector('.pv-track');
  var slides    = Array.from(carousel.querySelectorAll('.pv-slide'));
  var dots      = Array.from(carousel.querySelectorAll('.pv-dot'));
  var btnLeft   = carousel.querySelector('.pv-arrow--left');
  var btnRight  = carousel.querySelector('.pv-arrow--right');
  var viewport  = carousel.querySelector('.pv-viewport');

  var autoPlay  = carousel.dataset.autoplay === 'true';
  var interval  = parseInt(carousel.dataset.interval) || 5000;

  // 實際圖片數（不含 clone）
  var clones    = carousel.querySelectorAll('.pv-slide[data-clone]');
  var total     = slides.length - clones.length; // 實際張數
  var current   = 0; // 0-based，對應實際圖片
  var timer     = null;
  var jumping   = false;

  if (total <= 1) return;

  function getSlideWidth() {
    return viewport.offsetWidth - parseFloat(getComputedStyle(viewport).paddingLeft) * 2;
  }

  function setWidth() {
    var w = getSlideWidth();
    slides.forEach(function (s) { s.style.width = w + 'px'; });
    moveTo(current + 1, false);
  }

  function moveTo(index, animate) {
    if (!animate) {
      track.classList.add('no-transition');
    } else {
      track.classList.remove('no-transition');
    }
    var w = getSlideWidth();
    track.style.transform = 'translateX(-' + (index * w) + 'px)';
  }

  function updateActive() {
    slides.forEach(function (s, i) {
      s.classList.toggle('is-active', i === current + 1);
    });
    dots.forEach(function (d, i) {
      d.classList.toggle('active', i === current);
    });
  }

  function next() {
    if (jumping) return;
    current++;
    moveTo(current + 1, true);
    updateActive();
    if (current >= total) {
      jumping = true;
      setTimeout(function () {
        current = 0;
        moveTo(current + 1, false);
        setTimeout(function () { jumping = false; }, 20);
        updateActive();
      }, 560);
    }
  }

  function prev() {
    if (jumping) return;
    current--;
    moveTo(current + 1, true);
    updateActive();
    if (current < 0) {
      jumping = true;
      setTimeout(function () {
        current = total - 1;
        moveTo(current + 1, false);
        setTimeout(function () { jumping = false; }, 20);
        updateActive();
      }, 560);
    }
  }

  function goTo(i) {
    current = i;
    moveTo(current + 1, true);
    updateActive();
  }

  function startTimer() {
    if (!autoPlay) return;
    stopTimer();
    timer = setInterval(next, interval);
  }

  function stopTimer() {
    if (timer) { clearInterval(timer); timer = null; }
  }

  // 初始化寬度
  setWidth();
  updateActive();

  if (btnLeft)  btnLeft.addEventListener('click',  function () { prev(); startTimer(); });
  if (btnRight) btnRight.addEventListener('click', function () { next(); startTimer(); });

  dots.forEach(function (d, i) {
    d.addEventListener('click', function () { goTo(i); startTimer(); });
  });

  carousel.addEventListener('mouseenter', stopTimer);
  carousel.addEventListener('mouseleave', startTimer);

  window.addEventListener('resize', setWidth);

  startTimer();
})();
</script>
