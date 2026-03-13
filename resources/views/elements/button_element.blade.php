{{-- resources/views/frames/elements/button_element.blade.php --}}
@php
  $value     = $element['value'] ?? [];
  $text      = $value['text']      ?? '按鈕文字';
  $link      = $value['link']      ?? '#';
  $align     = $value['align']     ?? 'center';
  $textColor = $value['textColor'] ?? '#fff';
  $bgColor   = $value['bgColor']   ?? '#E8572A';

  $ensureUnit = function($value, $default) {
      if (!$value) return $default;
      if (is_numeric($value)) return $value . 'px';
      if (preg_match('/^\d+$/', (string)$value)) return $value . 'px';
      return $value;
  };

  $fontSize = $ensureUnit($value['fontSize'] ?? null, '16px');
  $padding  = $ensureUnit($value['padding']  ?? null, '12px 32px');
@endphp

<div class="button-element" style="text-align: {{ $align }};">
    <a
        href="{{ $link }}"
        class="element-button"
        target="_blank"
        rel="noopener noreferrer"
        style="display: inline-block; color: {{ $textColor }}; background-color: {{ $bgColor }}; font-size: {{ $fontSize }}; padding: {{ $padding }};"
    >
        {{ $text }}
    </a>
</div>
