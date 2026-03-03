{{-- resources/views/components/donation-section.blade.php --}}
@props([
    'title'           => '捐款護持',
    'text'            => "您的捐款將用於宮廟維護與慈善公益\n支持本宮日常運作、建設修繕及幫助弱勢族群\n每一分善款都將妥善運用 功德無量",
    'buttonText'      => '查看所有商品 ›',
    'buttonLink'      => '#',
    'backgroundColor' => 'linear-gradient(135deg, #8b7355 0%, #a0826d 100%)',
])

<section class="donation-section" style="background: {{ $backgroundColor }}">
    <div class="donation-content">
        <h2 class="donation-title">{{ $title }}</h2>
        <p class="donation-text">{{ $text }}</p>
        <a href="{{ $buttonLink }}" class="donation-btn">{{ $buttonText }}</a>
    </div>
</section>
