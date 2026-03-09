{{-- resources/views/elements/text_element.blade.php --}}
@php
  $text  = $text  ?? '文字內容';
  $meta  = $meta  ?? [];

  $color    = $meta['color']     ?? '#333';
  $align    = $meta['textAlign'] ?? $meta['text_align'] ?? $meta['align'] ?? 'left';

  $ensureUnit = function($val, $default) {
    if (!$val && $val !== 0) return $default;
    if (is_numeric($val)) return $val . 'px';
    if (preg_match('/^\d+$/', (string)$val)) return $val . 'px';
    return $val;
  };

  $fontSize = $ensureUnit($meta['fontSize'] ?? $meta['font_size'] ?? null, '16px');
@endphp

<div class="text-element"
     style="font-size: {{ $fontSize }}; color: {{ $color }}; text-align: {{ $align }};">
  {!! $text !!}
</div>
