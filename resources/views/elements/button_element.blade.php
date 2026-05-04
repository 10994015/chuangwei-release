{{-- resources/views/frames/elements/button_element.blade.php --}}
@php
  $meta      = $meta ?? [];

  $link      = $internalSlug ? '/' . $internalSlug : ($url ?? '#');
  $align     = $meta['align']               ?? 'center';
  $textColor = $meta['color']               ?? '#fff';
  $bgColor   = $meta['backgroundColor']     ?? '#E8572A';

  $ensureUnit = function($v, $default) {
      if (!$v) return $default;
      if (is_numeric($v)) return $v . 'px';
      return $v;
  };

  $fontSize = $ensureUnit($meta['fontSize'] ?? null, '16px');
  $padding  = $ensureUnit($meta['padding']  ?? null, '12px 32px');

  $isDisabled = (empty($link) || $link === '#');
@endphp

<div class="button-element" style="text-align: {{ $align }};">
  <a
    href="{{ $isDisabled ? 'javascript:void(0);' : $link }}"
    class="element-button"
    rel="noopener noreferrer"
    style="display: inline-block; color: {{ $textColor }}; background-color: {{ $bgColor }}; font-size: {{ $fontSize }}; padding: {{ $padding }};"
    @if($isDisabled) onclick="return false;" @endif
  >
    {{ $text }}
  </a>
</div>
