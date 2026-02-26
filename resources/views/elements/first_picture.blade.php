{{-- resources/views/frames/system/first_picture.blade.php --}}
@php
  $fd = $frame['data'] ?? [];

  $PLACEHOLDER_BG = 'https://images.unsplash.com/photo-1548013146-72479768bada?w=1280&h=600&fit=crop';

  // 工具函數：優先 camelCase，fallback 到 snake_case，再 fallback 預設值
  $get = fn($camel, $snake, $default) =>
      $fd[$camel] ?? $fd[$snake] ?? $default;

  $bgSrc          = $fd['heroBgImgSrc']   ?? $fd['hero_bg_img_src']   ?? null;
  $backgroundImage = $bgSrc ?: $PLACEHOLDER_BG;

  $heroTitle      = $fd['heroTitle']      ?? $fd['hero_title']      ?? '';
  $heroSubtitle   = $fd['heroSubtitle']   ?? $fd['hero_subtitle']   ?? '';
  $heroHeight     = $get('heroHeight',    'hero_height',            '600px');

  $overlayOpacityRaw = $fd['overlayOpacity'] ?? $fd['overlay_opacity'] ?? 40;
  $overlayOpacity    = round($overlayOpacityRaw / 100, 2);  // 0~1
  $overlayColor      = $get('overlayColor',   'overlay_color',   '#000000');

  $textBoxBorderRadius = $get('textBoxBorderRadius', 'text_box_border_radius', '12px');

  $titleColor      = $get('titleColor',    'title_color',    '#ffffff');
  $titleFontSize   = $get('titleFontSize', 'title_font_size','48px');
  $subtitleColor   = $get('subtitleColor', 'subtitle_color', '#eeeeee');
  $subtitleFontSize= $get('subtitleFontSize','subtitle_font_size','20px');

  // inline style 字串
  $heroStyle    = "min-height:{$heroHeight}; background-image:url('{$backgroundImage}');";
  $overlayStyle = "background-color:{$overlayColor}; opacity:{$overlayOpacity};";
  $textBoxStyle = "border-radius:{$textBoxBorderRadius};";
  $titleStyle   = "color:{$titleColor}; font-size:{$titleFontSize};";
  $subtitleStyle= "color:{$subtitleColor}; font-size:{$subtitleFontSize};";
@endphp

<div class="hero-banner preview-mode">
  <div class="hero-container" style="{{ $heroStyle }}">

    {{-- 半透明遮罩層 --}}
    <div class="hero-overlay" style="{{ $overlayStyle }}"></div>

    {{-- 文字內容區 --}}
    <div class="hero-content">
      <div class="hero-text-box" style="{{ $textBoxStyle }}">
        @if($heroTitle)
          <h1 class="hero-title" style="{{ $titleStyle }}">{{ $heroTitle }}</h1>
        @endif
        @if($heroSubtitle)
          <p class="hero-subtitle" style="{{ $subtitleStyle }}">{{ $heroSubtitle }}</p>
        @endif
      </div>
    </div>

  </div>
</div>
