{{-- resources/views/components/pv-footer.blade.php --}}
@php
  $data = $frame['data'] ?? [];

  $logoSrc      = $data['logoImgSrc'] ?? null;
  $tenantName   = $data['tenantName'] ?? null;
  $brandName    = $data['brandName']  ?? null;
  $displayName  = $tenantName ?? $brandName ?? __('ui.pvFooter.defaultName');
  $copyright    = $data['copyright'] ?? ('Copyright © ' . date('Y') . ' ' . $displayName . ' All Rights Reserved.');

  $displayPhone   = $data['tenantPhone']   ?? null;
  $displayAddress = $data['tenantAddress'] ?? null;
  $displayEmail   = $data['tenantEmail']   ?? null;

  $columns = $footerData['columns'] ?? [];
  if (empty($columns)) {
    $columns = [
      [__('ui.pvFooter.aboutUs'), __('ui.pvFooter.latestNews'), __('ui.pvFooter.aboutEco')],
      [__('ui.pvFooter.products'), __('ui.pvFooter.templeMap'), __('ui.pvFooter.lottery')],
    ];
  }

  $footerBgColor   = $data['footerBgColor']   ?? null;
  $footerTextColor = $data['footerTextColor'] ?? null;

  $footerStyle = '';
  if ($footerBgColor)   $footerStyle .= "background:{$footerBgColor};";
  if ($footerTextColor) $footerStyle .= "--pv-footer-text:{$footerTextColor};";

  $locale = request()->query('locale', 'ZH-TW');
@endphp

<footer class="pv-footer" @if($footerStyle) style="{{ $footerStyle }}" @endif>
  <div class="pv-footer-container">

    <div class="pv-footer-content">

      {{-- 左側 Logo --}}
      <div class="pv-footer-brand">
        <div class="pv-footer-logo">
          @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Logo" class="pv-logo-img" />
          @else
            <span class="pv-logo-icon">
              <svg width="32" height="32" viewBox="0 0 28 28" fill="none">
                <rect width="28" height="28" rx="6" fill="#E8572A"/>
                <text x="14" y="20" text-anchor="middle" font-size="14" fill="#fff" font-weight="bold">宮</text>
              </svg>
            </span>
          @endif
          <span class="pv-logo-name">{{ $displayName }}</span>
        </div>
      </div>

      {{-- 中間連結欄 --}}
      <div class="pv-footer-links-wrapper">
        @foreach($columns as $column)
          <div class="pv-footer-column">
            @foreach($column as $item)
              @php
                $itemName = is_array($item) ? ($item['name'] ?? '') : $item;
                $itemSlug = is_array($item) ? ($item['slug'] ?? '') : '';
                $href = ($itemSlug && $itemSlug !== '#')
                  ? "/{$itemSlug}?locale={$locale}"
                  : '#';
              @endphp
              @if($itemSlug !== 'portal')
                <a href="{{ $href }}" class="pv-footer-link">{{ $itemName }}</a>
              @endif
            @endforeach
          </div>
        @endforeach
      </div>

      {{-- 右側聯絡資訊 --}}
      <div class="pv-footer-contact-col">
        <h4 class="pv-contact-heading">{{ __('ui.pvFooter.contactUs') }}</h4>
        @if($displayPhone)
          <p class="pv-contact-item">{{ __('ui.pvFooter.phone') }}{{ $displayPhone }}</p>
        @endif
        @if($displayAddress)
          <p class="pv-contact-item">{{ __('ui.pvFooter.address') }}{{ $displayAddress }}</p>
        @endif
        @if($displayEmail)
          <p class="pv-contact-item">{{ __('ui.pvFooter.email') }}{{ $displayEmail }}</p>
        @endif
      </div>

    </div>

    <div class="pv-footer-divider"></div>

    <div class="pv-footer-bottom">
      <p>{{ $copyright }}</p>
    </div>

  </div>
</footer>

<style>
.pv-footer { background: #1e1e1e; color: var(--pv-footer-text,#fff); padding: 3rem 0 0; word-break: keep-all; overflow-wrap: break-word; }
.pv-footer-container { max-width: 1400px; margin: 0 auto; padding: 0 3rem; }
.pv-footer-content { display: grid; grid-template-columns: 2fr 3fr 2fr; gap: 4rem; padding-bottom: 2.5rem; align-items: start; }
.pv-footer-brand { display: flex; flex-direction: column; }
.pv-footer-logo  { display: flex; align-items: center; gap: 10px; }
.pv-logo-img { max-width: 120px; max-height: 40px; object-fit: contain; filter: brightness(0) invert(1); }
.pv-logo-icon { display: flex; align-items: center; flex-shrink: 0; }
.pv-logo-name { font-size: 20px; font-weight: 700; color: var(--pv-footer-text,#fff); white-space: nowrap; letter-spacing: 1px; }
.pv-footer-links-wrapper { display: grid; grid-template-columns: repeat(2,1fr); gap: 1rem 2rem; }
.pv-footer-column { display: flex; flex-direction: column; gap: 0.85rem; }
.pv-footer-link { font-size: 14px; color: var(--pv-footer-text,rgba(255,255,255,0.7)); opacity: 0.75; text-decoration: none; transition: opacity 0.2s; white-space: nowrap; }
.pv-footer-link:hover { opacity: 1; }
.pv-footer-contact-col { display: flex; flex-direction: column; gap: 0.65rem; }
.pv-contact-heading { font-size: 15px; font-weight: 600; color: var(--pv-footer-text,#fff); margin: 0 0 0.25rem; }
.pv-contact-item { font-size: 14px; color: var(--pv-footer-text,rgba(255,255,255,0.7)); opacity: 0.75; margin: 0; line-height: 1.7; }
.pv-footer-divider { border: none; border-top: 1px solid rgba(255,255,255,0.12); }
.pv-footer-bottom { padding: 1.5rem 0; text-align: center; }
.pv-footer-bottom p { margin: 0; font-size: 13px; color: var(--pv-footer-text,rgba(255,255,255,0.45)); opacity: 0.6; }
@media (min-width: 769px) and (max-width: 1024px) {
  .pv-footer { padding: 2rem 0 0; }
  .pv-footer-container { padding: 0 1.5rem; }
  .pv-footer-content { grid-template-columns: 1fr 2fr 1.5fr; gap: 2rem; }
  .pv-logo-name { font-size: 17px; } .pv-footer-link { font-size: 13px; } .pv-contact-item { font-size: 13px; }
}
@media (max-width: 768px) {
  .pv-footer { padding: 1.5rem 0 0; }
  .pv-footer-container { padding: 0 1.25rem; }
  .pv-footer-content { grid-template-columns: 1fr; gap: 1.75rem; padding-bottom: 1.5rem; }
  .pv-footer-links-wrapper { grid-template-columns: repeat(2,1fr); gap: 0.75rem 1.5rem; }
  .pv-logo-name { font-size: 17px; } .pv-footer-link { font-size: 13px; } .pv-contact-item { font-size: 13px; }
  .pv-footer-bottom { padding: 1rem 0; } .pv-footer-bottom p { font-size: 12px; }
}
</style>
