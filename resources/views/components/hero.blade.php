{{-- resources/views/components/hero.blade.php --}}
@php
    $data                  = $frame['data'] ?? [];
    $caroiselWallImgs      = $data['caroiselWallImgs']     ?? $data['carouselWallImgs'] ?? [];
    $carouselWallHeight    = $data['carouselWallHeight']   ?? 600;
    $carouselWallAutoPlay  = $data['carouselWallAutoPlay'] ?? true;
    $carouselWallInterval  = $data['carouselWallInterval'] ?? 5000;

    $placeholderSlides = [
        ['image' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=1280&h=600&fit=crop', 'title' => '輪播圖片 1', 'subtitle' => '', 'overlayOpacity' => 40, 'overlayColor' => '#000000', 'titleColor' => '#ffffff', 'titleFontSize' => 48, 'subtitleColor' => '#eeeeee', 'subtitleFontSize' => 20],
        ['image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=1280&h=600&fit=crop', 'title' => '輪播圖片 2', 'subtitle' => '', 'overlayOpacity' => 40, 'overlayColor' => '#000000', 'titleColor' => '#ffffff', 'titleFontSize' => 48, 'subtitleColor' => '#eeeeee', 'subtitleFontSize' => 20],
        ['image' => 'https://images.unsplash.com/photo-1604881991720-f91add269bed?w=1280&h=600&fit=crop', 'title' => '輪播圖片 3', 'subtitle' => '', 'overlayOpacity' => 40, 'overlayColor' => '#000000', 'titleColor' => '#ffffff', 'titleFontSize' => 48, 'subtitleColor' => '#eeeeee', 'subtitleFontSize' => 20],
    ];

    if (!empty($caroiselWallImgs)) {
        $displaySlides = array_map(fn($item) => [
            'image'           => $item['src']             ?? $item['image'] ?? '',
            'title'           => $item['title']           ?? '',
            'subtitle'        => $item['subtitle']        ?? '',
            'overlayOpacity'  => $item['overlayOpacity']  ?? 40,
            'overlayColor'    => $item['overlayColor']    ?? '#000000',
            'titleColor'      => $item['titleColor']      ?? '#ffffff',
            'titleFontSize'   => $item['titleFontSize']   ?? 48,
            'subtitleColor'   => $item['subtitleColor']   ?? '#eeeeee',
            'subtitleFontSize'=> $item['subtitleFontSize'] ?? 20,
        ], $caroiselWallImgs);
    } else {
        $displaySlides = $placeholderSlides;
    }

    $heightVal      = is_numeric($carouselWallHeight) ? $carouselWallHeight . 'px' : $carouselWallHeight;
    $autoPlay       = $carouselWallAutoPlay ? 'true' : 'false';
    $interval       = (int) $carouselWallInterval;
    $slidesJson     = json_encode(array_values($displaySlides));
    $multipleSlides = count($displaySlides) > 1;
@endphp

<section
    class="hero"
    style="height: {{ $heightVal }}"
    x-data="{
        slides: {{ $slidesJson }},
        current: 0,
        autoPlay: {{ $autoPlay }},
        interval: {{ $interval }},
        timer: null,

        next() { this.current = (this.current + 1) % this.slides.length },
        prev() { this.current = this.current === 0 ? this.slides.length - 1 : this.current - 1 },
        goTo(index) { this.current = index },

        getOverlayStyle(slide) {
            return {
                backgroundColor: slide.overlayColor || '#000000',
                opacity: (slide.overlayOpacity ?? 40) / 100
            }
        },
        getTitleStyle(slide) {
            return {
                color: slide.titleColor || '#ffffff',
                fontSize: (slide.titleFontSize ?? 48) + 'px'
            }
        },
        getSubtitleStyle(slide) {
            return {
                color: slide.subtitleColor || '#eeeeee',
                fontSize: (slide.subtitleFontSize ?? 20) + 'px'
            }
        },

        startAutoPlay() {
            this.stopAutoPlay()
            if (this.autoPlay && this.slides.length > 1) {
                this.timer = setInterval(() => this.next(), this.interval)
            }
        },
        stopAutoPlay() {
            if (this.timer) { clearInterval(this.timer); this.timer = null }
        }
    }"
    x-init="startAutoPlay()"
    @mouseover="stopAutoPlay()"
    @mouseleave="startAutoPlay()"
>
    <div class="hero-swiper">
        <div class="swiper-wrapper">
            <template x-for="(slide, index) in slides" :key="index">
                <div class="swiper-slide" :class="{ active: current === index }">
                    <img :src="slide.image" :alt="slide.title || '輪播圖片'" class="slide-image" />

                    {{-- 每張圖片獨立遮罩 --}}
                    <div class="slide-overlay" :style="getOverlayStyle(slide)"></div>

                    {{-- 每張圖片獨立文字 --}}
                    <div
                        class="slide-text-content"
                        x-show="slide.title || slide.subtitle"
                    >
                        <h2
                            class="slide-title"
                            x-show="slide.title"
                            x-text="slide.title"
                            :style="getTitleStyle(slide)"
                        ></h2>
                        <p
                            class="slide-subtitle"
                            x-show="slide.subtitle"
                            x-text="slide.subtitle"
                            :style="getSubtitleStyle(slide)"
                        ></p>
                    </div>
                </div>
            </template>
        </div>

        @if ($multipleSlides)
            <button class="hero-btn prev" @click="prev()">‹</button>
            <button class="hero-btn next" @click="next()">›</button>

            <div class="hero-pagination">
                <template x-for="(slide, index) in slides" :key="index">
                    <button
                        class="pagination-dot"
                        :class="{ active: current === index }"
                        @click="goTo(index)"
                    ></button>
                </template>
            </div>
        @endif
    </div>
</section>
