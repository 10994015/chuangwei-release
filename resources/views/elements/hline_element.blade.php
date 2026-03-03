{{-- resources/views/frames/elements/hline_element.blade.php --}}
@php
  $value = $element['value'] ?? [];

  $color     = $value['color']     ?? '#E0E0E0';

  // ensureUnit：數字自動加 px，百分比保留，否則用預設值
  $ensureUnit = function($val, $default) {
      if (!$val && $val !== 0) return $default;
      if (is_numeric($val)) return $val . 'px';
      if (preg_match('/^\d+$/', (string)$val)) return $val . 'px';
      return $val;
  };

  $thickness = $ensureUnit($value['thickness'] ?? null, '2px');
  $width     = $ensureUnit($value['width']     ?? null, '100%');
@endphp

<div class="hline-element">
  <hr class="hline-element__line"
      style="border-color: {{ $color }}; border-top-width: {{ $thickness }}; width: {{ $width }};" />
</div>
