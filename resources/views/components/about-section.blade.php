{{-- resources/views/components/about-section.blade.php --}}
@props([
    'title'            => '關於我們',
    'subtitle'         => '認識天上聖母宮的歷史與特色',
    'descriptions'     => [
        '天上聖母宮創建於民國XX年，主祀天上聖母媽祖娘娘，為當地重要信仰中心。',
        '本宮歷經數十載風雨，香火鼎盛，神威顯赫，守護在地居民，庇佑四方信眾。',
    ],
    'linkText'         => '了解宮廟歷史 ›',
    'linkUrl'          => '#',
    'imagePlaceholder' => '宮廟圖片',
])

<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-image">
                <div class="image-placeholder">{{ $imagePlaceholder }}</div>
            </div>
            <div class="about-text">
                <h2 class="about-title">{{ $title }}</h2>
                <p class="about-subtitle">{{ $subtitle }}</p>

                @foreach ($descriptions as $desc)
                    <p class="about-description">{{ $desc }}</p>
                @endforeach

                <a href="{{ $linkUrl }}" class="about-link">{{ $linkText }}</a>
            </div>
        </div>
    </div>
</section>
