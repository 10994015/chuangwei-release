{{-- resources/views/components/donation-section.blade.php --}}
@php
    $data       = $frame['data'] ?? [];
    $title      = $data['donationTitle']      ?? __('ui.donationBasemap.title');
    $text = $data['donationBrief'] ?? __('ui.donationBasemap.brief');
    $btnText    = $data['donationButtonText'] ?? __('ui.donationBasemap.buttonText');
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
