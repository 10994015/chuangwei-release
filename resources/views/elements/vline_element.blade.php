{{-- resources/views/frames/elements/vline_element.blade.php --}}
@php
  $value = $element['value'] ?? [];
  $color = $value['color'] ?? '#E0E0E0';

  $ensureUnit = function($val, $default) {
      if (!$val && $val !== 0) return $default;
      if (is_numeric($val)) return $val . 'px';
      if (preg_match('/^\d+$/', (string)$val)) return $val . 'px';
      return $val;
  };

  $thickness = $ensureUnit($value['thickness'] ?? null, '2px');
  $height    = $ensureUnit($value['height']    ?? null, '100px');
@endphp

<div class="vline-element">
  <div class="vline-element__line"
       style="border-color: {{ $color }}; border-left-width: {{ $thickness }}; height: {{ $height }};"></div>
</div>
