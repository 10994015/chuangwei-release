{{-- resources/views/components/donation-section.blade.php --}}
@php
    $data       = $frame['data'] ?? [];
    $title      = $data['donationTitle']      ?? '捐款護持';
    $text = $data['donationBrief'] ?? "您的捐款將用於宮廟維護與慈善公益\n支持本宮日常運作、建設修繕及幫助弱勢族群\n每一分善款都將妥善運用 功德無量";
    $btnText    = $data['donationButtonText'] ?? '前往捐款 ›';
    $btnLink    = $data['donationButtonLink'] ?? '';
    $templeId   = $templeId ?? '';

    // 若 frame.data 沒有設定連結，才 fallback 到 templeId 路由
    if (!$btnLink && $templeId) {
        $btnLink = "/site/{$templeId}/donation";
    }
    if (!$btnLink) {
        $btnLink = '#';
    }
@endphp

<section class="donation-section" style="background: linear-gradient(135deg, #8b7355 0%, #a0826d 100%)">
    <div class="donation-content">
        <h2 class="donation-title">{!! $title !!}</h2>
        <p class="donation-text">{!! $text !!}</p>
        <a href="{{ $btnLink }}" class="donation-btn">{{ $btnText }}</a>
    </div>
</section>
