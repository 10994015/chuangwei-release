{{-- resources/views/components/hero.blade.php --}}
@php
    $data                 = $frame['data'] ?? [];
    $caroiselWallImgs     = $data['caroiselWallImgs']     ?? $data['carouselWallImgs'] ?? [];
    $carouselWallHeight   = $data['carouselWallHeight']   ?? 600;
    $carouselWallAutoPlay = $data['carouselWallAutoPlay'] ?? true;
    $carouselWallInterval = $data['carouselWallInterval'] ?? 5000;

    // ── 整理圖片清單 ───────────────────────────────────────────────────
    $slides = [];
    foreach ($caroiselWallImgs as $item) {
        if (!is_array($item)) {
            $slides[] = [
                'desktop'         => $item,
                'tablet'          => $item,
                'mobile'          => $item,
                'title'           => '',
                'subtitle'        => '',
                'overlayOpacity'  => 40,
                'overlayColor'    => '#000000',
                'titleColor'      => '#ffffff',
                'titleFontSize'   => 48,
                'subtitleColor'   => '#eeeeee',
                'subtitleFontSize'=> 20,
            ];
            continue;
        }
        $desktop = ($item['desktopSrc'] ?? '') ?: ($item['srcDesktop'] ?? $item['src'] ?? $item['image'] ?? '');
        $tablet  = ($item['tabletSrc']  ?? '') ?: ($item['srcTablet']  ?? $desktop);
        $mobile  = ($item['mobileSrc']  ?? '') ?: ($item['srcMobile']  ?? $tablet);
        if ($desktop || $tablet || $mobile) {
            $slides[] = [
                'desktop'         => $desktop,
                'tablet'          => $tablet,
                'mobile'          => $mobile,
                'title'           => $item['title']             ?? '',
                'subtitle'        => $item['subtitle']          ?? '',
                'overlayOpacity'  => $item['overlayOpacity']   ?? 40,
                'overlayColor'    => $item['overlayColor']     ?? '#000000',
                'titleColor'      => $item['titleColor']       ?? '#ffffff',
                'titleFontSize'   => $item['titleFontSize']    ?? 48,
                'subtitleColor'   => $item['subtitleColor']    ?? '#eeeeee',
                'subtitleFontSize'=> $item['subtitleFontSize'] ?? 20,
            ];
        }
    }

    // ── Fallback placeholder ───────────────────────────────────────────
    if (empty($slides)) {
        foreach ([
            'https://images.unsplash.com/photo-1548013146-72479768bada?w=1280&h=600&fit=crop',
            'https://images.unsplash.com/photo-1528127269322-539801943592?w=1280&h=600&fit=crop',
            'https://images.unsplash.com/photo-1604881991720-f91add269bed?w=1280&h=600&fit=crop',
        ] as $src) {
            $slides[] = [
                'desktop' => $src, 'tablet' => $src, 'mobile' => $src,
                'title' => '', 'subtitle' => '',
                'overlayOpacity' => 40, 'overlayColor' => '#000000',
                'titleColor' => '#ffffff', 'titleFontSize' => 48,
                'subtitleColor' => '#eeeeee', 'subtitleFontSize' => 20,
            ];
        }
    }

    $heightVal      = is_numeric($carouselWallHeight) ? $carouselWallHeight . 'px' : $carouselWallHeight;
    $autoPlay       = $carouselWallAutoPlay ? 'true' : 'false';
    $interval       = (int) $carouselWallInterval;
    $isSingle       = count($slides) === 1;
    $heroId         = 'hero-' . uniqid();
@endphp

<section
    class="hero"
    id="{{ $heroId }}"
    style="--hero-height: {{ $heightVal }}"
    data-autoplay="{{ $autoPlay }}"
    data-interval="{{ $interval }}"
>
    <div class="hero-swiper">
        <div class="swiper-wrapper">
            {{-- 尾部 clone（無縫輪播用）--}}
            @if(!$isSingle)
                @php $last = $slides[count($slides) - 1]; @endphp
                <div class="swiper-slide clone">
                    <picture>
                        @if($last['desktop'])<source media="(min-width: 1024px)" srcset="{{ $last['desktop'] }}">@endif
                        @if($last['tablet'])<source media="(min-width: 768px)"  srcset="{{ $last['tablet'] }}">@endif
                        <img src="{{ $last['mobile'] ?: $last['desktop'] }}" alt="輪播圖片" class="slide-image" />
                    </picture>
                </div>
            @endif

            @foreach($slides as $i => $slide)
                <div class="swiper-slide{{ $i === 0 ? ' active' : '' }}">
                    <picture>
                        @if($slide['desktop'])<source media="(min-width: 1024px)" srcset="{{ $slide['desktop'] }}">@endif
                        @if($slide['tablet'])<source media="(min-width: 768px)"  srcset="{{ $slide['tablet'] }}">@endif
                        <img
                            src="{{ $slide['mobile'] ?: $slide['desktop'] }}"
                            alt="{{ $slide['title'] ?: '輪播圖片 ' . ($i + 1) }}"
                            class="slide-image"
                            loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                        />
                    </picture>

                    {{-- 文字 --}}
                    @if($slide['title'] || $slide['subtitle'])
                        <div class="slide-text-content">
                            @if($slide['title'])
                                <h2 class="slide-title" style="color:{{ $slide['titleColor'] }};font-size:{{ $slide['titleFontSize'] }}px">
                                    {{ $slide['title'] }}
                                </h2>
                            @endif
                            @if($slide['subtitle'])
                                <p class="slide-subtitle" style="color:{{ $slide['subtitleColor'] }};font-size:{{ $slide['subtitleFontSize'] }}px">
                                    {{ $slide['subtitle'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- 頭部 clone --}}
            @if(!$isSingle)
                @php $first = $slides[0]; @endphp
                <div class="swiper-slide clone">
                    <picture>
                        @if($first['desktop'])<source media="(min-width: 1024px)" srcset="{{ $first['desktop'] }}">@endif
                        @if($first['tablet'])<source media="(min-width: 768px)"  srcset="{{ $first['tablet'] }}">@endif
                        <img src="{{ $first['mobile'] ?: $first['desktop'] }}" alt="輪播圖片" class="slide-image" />
                    </picture>
                </div>
            @endif
        </div>

        @if(!$isSingle)
            <button class="hero-btn prev" aria-label="上一張">‹</button>
            <button class="hero-btn next" aria-label="下一張">›</button>

            <div class="hero-pagination">
                @foreach($slides as $i => $slide)
                    <button class="pagination-dot{{ $i === 0 ? ' active' : '' }}" aria-label="第 {{ $i + 1 }} 張"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
(function () {
    var hero = document.getElementById('{{ $heroId }}');
    if (!hero) return;

    var wrapper  = hero.querySelector('.swiper-wrapper');
    var allSlides = Array.from(hero.querySelectorAll('.swiper-slide'));
    var dots      = Array.from(hero.querySelectorAll('.pagination-dot'));
    var btnPrev   = hero.querySelector('.hero-btn.prev');
    var btnNext   = hero.querySelector('.hero-btn.next');

    var autoPlay  = hero.dataset.autoplay === 'true';
    var interval  = parseInt(hero.dataset.interval) || 5000;

    var clones = hero.querySelectorAll('.swiper-slide.clone');
    var total  = allSlides.length - clones.length;

    if (total <= 1) return;

    var current  = 0;
    var timer    = null;
    var jumping  = false;

    function getW() { return hero.offsetWidth; }

    function setWidths() {
        var w = getW();
        allSlides.forEach(function (s) { s.style.width = w + 'px'; });
        moveTo(current + 1, false);
    }

    function moveTo(idx, animate) {
        wrapper.style.transition = animate ? 'transform 0.65s cubic-bezier(0.4,0,0.2,1)' : 'none';
        wrapper.style.transform  = 'translateX(-' + (idx * getW()) + 'px)';
    }

    function updateUI() {
        allSlides.forEach(function (s, i) {
            s.classList.toggle('active', i === current + 1);
        });
        dots.forEach(function (d, i) {
            d.classList.toggle('active', i === current);
        });
    }

    function next() {
        if (jumping) return;
        current++;
        moveTo(current + 1, true);
        updateUI();
        if (current >= total) {
            jumping = true;
            setTimeout(function () {
                current = 0;
                moveTo(current + 1, false);
                setTimeout(function () { jumping = false; }, 20);
                updateUI();
            }, 670);
        }
    }

    function prev() {
        if (jumping) return;
        current--;
        moveTo(current + 1, true);
        updateUI();
        if (current < 0) {
            jumping = true;
            setTimeout(function () {
                current = total - 1;
                moveTo(current + 1, false);
                setTimeout(function () { jumping = false; }, 20);
                updateUI();
            }, 670);
        }
    }

    function startTimer() {
        if (!autoPlay) return;
        stopTimer();
        timer = setInterval(next, interval);
    }

    function stopTimer() {
        if (timer) { clearInterval(timer); timer = null; }
    }

    setWidths();
    updateUI();

    if (btnPrev) btnPrev.addEventListener('click', function () { prev(); startTimer(); });
    if (btnNext) btnNext.addEventListener('click', function () { next(); startTimer(); });

    dots.forEach(function (d, i) {
        d.addEventListener('click', function () {
            current = i;
            moveTo(current + 1, true);
            updateUI();
            startTimer();
        });
    });

    hero.addEventListener('mouseenter', stopTimer);
    hero.addEventListener('mouseleave', startTimer);

    window.addEventListener('resize', setWidths);

    startTimer();
})();
</script>

<style>
#{{ $heroId }} {
  height: var(--hero-height, 600px);
}
#{{ $heroId }} .slide-image {
  object-fit: contain;
}
@media (max-width: 1024px) and (min-width: 769px) {
  #{{ $heroId }} {
    height: unset;
    aspect-ratio: 16 / 7;
  }
}
@media (max-width: 768px) {
  #{{ $heroId }} {
    height: unset;
    aspect-ratio: 3 / 4;
  }
  #{{ $heroId }} .slide-image {
    object-fit: cover;
  }
}
</style>
