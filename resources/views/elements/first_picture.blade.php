{{-- resources/views/elements/first_picture.blade.php --}}
@php
    $data = $frame['data'] ?? [];

    $placeholderBg = 'https://images.unsplash.com/photo-1548013146-72479768bada?w=1280&h=600&fit=crop';

    $bgImg         = $data['heroBgImgSrc']        ?? $data['hero_bg_img_src']        ?? $placeholderBg;
    $heroTitle     = $data['heroTitle']            ?? $data['hero_title']             ?? '';
    $heroSubtitle  = $data['heroSubtitle']         ?? $data['hero_subtitle']          ?? '';

    $heroHeight    = $data['heroHeight']           ?? $data['hero_height']            ?? '600px';
    $overlayColor  = $data['overlayColor']         ?? $data['overlay_color']          ?? '#000000';
    $overlayOpacity= isset($data['overlayOpacity'])
        ? ($data['overlayOpacity'] / 100)
        : (isset($data['overlay_opacity']) ? ($data['overlay_opacity'] / 100) : 0.4);

    $borderRadius  = $data['textBoxBorderRadius']  ?? $data['text_box_border_radius'] ?? '12px';
    $titleColor    = $data['titleColor']           ?? $data['title_color']            ?? '#ffffff';
    $titleSize     = $data['titleFontSize']        ?? $data['title_font_size']        ?? '48px';
    $subtitleColor = $data['subtitleColor']        ?? $data['subtitle_color']         ?? '#eeeeee';
    $subtitleSize  = $data['subtitleFontSize']     ?? $data['subtitle_font_size']     ?? '20px';

    // 確保高度有單位
    if (is_numeric($heroHeight)) {
        $heroHeight = $heroHeight . 'px';
    }
    if (is_numeric($borderRadius)) {
        $borderRadius = $borderRadius . 'px';
    }
    if (is_numeric($titleSize)) {
        $titleSize = $titleSize . 'px';
    }
    if (is_numeric($subtitleSize)) {
        $subtitleSize = $subtitleSize . 'px';
    }
@endphp

<section
    class="first-picture"
    style="
        min-height: {{ $heroHeight }};
        background-image: url('{{ $bgImg }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    "
>
    {{-- 遮罩層 --}}
    <div style="
        position: absolute;
        inset: 0;
        background-color: {{ $overlayColor }};
        opacity: {{ $overlayOpacity }};
        pointer-events: none;
        z-index: 1;
    "></div>

    {{-- 文字內容 --}}
    <div style="
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 1400px;
        padding: 0 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            padding: 60px 80px;
            border-radius: {{ $borderRadius }};
            text-align: center;
            max-width: 800px;
            width: 100%;
        ">
            @if($heroTitle)
                <h1 style="
                    font-size: {{ $titleSize }};
                    color: {{ $titleColor }};
                    font-weight: 700;
                    margin: 0 0 20px;
                    line-height: 1.2;
                ">
                    {{ $heroTitle }}
                </h1>
            @endif

            @if($heroSubtitle)
                <p style="
                    font-size: {{ $subtitleSize }};
                    color: {{ $subtitleColor }};
                    margin: 0;
                    line-height: 1.6;
                ">
                    {{ $heroSubtitle }}
                </p>
            @endif
        </div>
    </div>
</section>
