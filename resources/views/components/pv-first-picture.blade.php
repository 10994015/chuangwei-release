{{-- resources/views/components/pv-first-picture.blade.php --}}
@php
  $data = $frame['data'] ?? [];

  // Logo
  $logoSrc    = $data['logoImgSrc'] ?? null;
  $brandName  = $data['brandName']  ?? ($data['tenantName'] ?? '');
  $brandColor = $data['brandColor'] ?? '#E8572A';
  $logoWidth  = $data['logoWidth']  ?? null;
  $logoHeight = $data['logoHeight'] ?? null;

  $logoWidthStyle  = $logoWidth  ? "width:{$logoWidth}px;"   : '';
  $logoHeightStyle = $logoHeight ? "height:{$logoHeight}px;" : '';
  $logoImgStyle    = "{$logoWidthStyle}{$logoHeightStyle}object-fit:contain;";

  $logoPadding = $data['logoPadding'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
  $logoPaddingStyle = "padding:{$logoPadding['top']}px {$logoPadding['right']}px {$logoPadding['bottom']}px {$logoPadding['left']}px;";

  // Hero
  $heroImgSrc    = $data['heroImgSrc']    ?? null;
  $heroImgWidth  = $data['heroImgWidth']  ?? null;
  $heroImgHeight = $data['heroImgHeight'] ?? null;
  $heroImgWidthStyle  = $heroImgWidth  ? "width:{$heroImgWidth}px;"   : 'width:100%;';
  $heroImgHeightStyle = $heroImgHeight ? "height:{$heroImgHeight}px;" : 'height:auto;';
  $heroImgStyle = "{$heroImgWidthStyle}{$heroImgHeightStyle}max-width:100%;object-fit:contain;";

  $displayTitle  = $data['heroTitle']     ?? '';
  $titleFontSize = $data['titleFontSize'] ?? null;
  $titleColor    = $data['titleColor']    ?? '#1a1a1a';
  $titleFontSizeStyle = $titleFontSize ? "font-size:{$titleFontSize}px;" : '';
  $titleStyle = "color:{$titleColor};{$titleFontSizeStyle}";

  $heroPadding = $data['heroPadding'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
  $heroPaddingStyle = "padding:{$heroPadding['top']}px {$heroPadding['right']}px {$heroPadding['bottom']}px {$heroPadding['left']}px;";

  // Buttons
  $buttons  = $data['buttons'] ?? [];
  $btnColor = $data['btnColor'] ?? '#E8572A';
  if (empty($buttons)) {
    $buttons = [
      ['text' => __('ui.pvFirstPicture.lottery'),   'url' => '#'],
      ['text' => __('ui.pvFirstPicture.templeMap'), 'url' => '#'],
      ['text' => __('ui.pvFirstPicture.enterHome'), 'url' => '#'],
    ];
  }

  $buttonsPadding = $data['buttonsPadding'] ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
  $buttonsPaddingStyle = "padding:{$buttonsPadding['top']}px {$buttonsPadding['right']}px {$buttonsPadding['bottom']}px {$buttonsPadding['left']}px;";

  // Copyright
  $copyright = $data['copyright'] ?? ('Copyright © ' . date('Y') . ' 創蔚國際有限公司 All Rights Reserved.');

  $locale = request()->query('locale', 'ZH-TW');
@endphp

<section class="pv-first-picture">

  {{-- Logo --}}
  <div class="pv-fp-logo" style="{{ $logoPaddingStyle }}">
    @if($logoSrc)
      <img src="{{ $logoSrc }}" alt="Logo" class="pv-fp-logo-img" style="{{ $logoImgStyle }}" />
    @else
      <span class="pv-fp-logo-icon">
        <img src="/images/icon.png" alt="icon" class="pv-fp-logo-icon-img" />
      </span>
    @endif
    @if($brandName !== '')
      <span class="pv-fp-brand" style="color:{{ $brandColor }}">{{ $brandName }}</span>
    @endif
  </div>

  {{-- Hero --}}
  <div class="pv-fp-hero" style="{{ $heroPaddingStyle }}">
    @if($heroImgSrc)
      <img src="{{ $heroImgSrc }}" alt="{{ __('ui.pvFirstPicture.mainImgAlt') }}" class="pv-fp-hero-img" style="{{ $heroImgStyle }}" />
    @endif
    @if($displayTitle !== '')
      <h1 class="pv-fp-title" style="{{ $titleStyle }}">{{ $displayTitle }}</h1>
    @endif
  </div>

  {{-- Buttons --}}
  <div class="pv-fp-buttons" style="{{ $buttonsPaddingStyle }}">
    @foreach($buttons as $btn)
      @php
        $url          = $btn['internalSlug'] ?? ($btn['url'] ?? '#');
        $btnItemColor = $btn['color'] ?? $btnColor;

        $isInternal = $url && $url !== '#'
          && !str_contains($url, '://')
          && !str_starts_with($url, '/')
          && !str_starts_with($url, '#');

        $href   = $isInternal ? "/{$url}?locale={$locale}" : $url;
        $target = $isInternal ? '_self' : '_blank';
      @endphp
      <a
        href="{{ $href }}"
        target="{{ $target }}"
        class="pv-fp-btn"
        style="border-color:{{ $btnItemColor }};color:{{ $btnItemColor }};"
      >{{ $btn['text'] ?? '' }}</a>
    @endforeach
  </div>

  {{-- Copyright --}}
  <div class="pv-fp-copyright">
    <p>{{ $copyright }}</p>
  </div>

</section>

<style>
.pv-first-picture {
  min-height: 100vh; width: 100%; display: flex; flex-direction: column;
  align-items: center; justify-content: center; padding: 3rem 2rem 2rem;
  box-sizing: border-box; position: relative; background: transparent;
}
.pv-fp-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 1.2rem; }
.pv-fp-logo-img { max-width: 1000px; max-height: 1000px; object-fit: contain; }
.pv-fp-logo-icon { display: flex; align-items: center; }
.pv-fp-logo-icon-img { width: 36px; height: 36px; object-fit: contain; }
.pv-fp-brand { font-size: 22px; font-weight: 700; letter-spacing: 1px; }
.pv-fp-hero {
  display: flex; flex-direction: column; align-items: center; gap: 1.5rem;
  text-align: center; margin-bottom: 1.2rem; min-height: 80px; justify-content: center;
}
.pv-fp-hero-img { display: block; }
.pv-fp-title {
  font-size: 64px; font-weight: 900; line-height: 1.25; margin: 0;
  white-space: pre-line; letter-spacing: -0.5px;
  font-family: 'Noto Serif TC', 'Source Han Serif TC', serif; text-align: center;
}
.pv-fp-buttons {
  display: flex; align-items: center; justify-content: center;
  gap: 16px; flex-wrap: wrap; margin-bottom: 4rem;
}
.pv-fp-btn {
  display: inline-block; padding: 12px 32px; border: 1.5px solid #E8572A;
  border-radius: 28px; background: transparent; font-size: 15px; font-weight: 500;
  text-decoration: none; white-space: nowrap; cursor: pointer; letter-spacing: 0.5px;
  transition: background 0.22s, color 0.22s;
}
.pv-fp-btn:hover { background: #E8572A; color: #fff !important; }
.pv-fp-copyright { position: absolute; bottom: 1.5rem; left: 0; right: 0; text-align: center; }
.pv-fp-copyright p { margin: 0; font-size: 13px; color: #aaa; }
@media (max-width: 768px) {
  .pv-first-picture { padding: 2.5rem 1.25rem 2rem; }
  .pv-fp-logo  { margin-bottom: 2.5rem; }
  .pv-fp-brand { font-size: 18px; }
  .pv-fp-hero  { gap: 1rem; margin-bottom: 2.5rem; }
  .pv-fp-title { font-size: 36px; }
  .pv-fp-buttons { gap: 12px; margin-bottom: 3rem; }
  .pv-fp-btn   { padding: 10px 24px; font-size: 14px; }
}
</style>
