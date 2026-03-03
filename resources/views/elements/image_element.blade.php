{{-- resources/views/elements/image_element.blade.php --}}
@php
  $src       = $src       ?? 'https://via.placeholder.com/400x300';
  $alt       = $alt       ?? '圖片';
  $meta      = $meta      ?? [];
  $objectFit = $meta['objectFit'] ?? $meta['object_fit'] ?? 'cover';

  $ensureUnit = function($val, $default) {
    if (!$val && $val !== 0) return $default;
    if ($val === 'auto') return 'auto';
    if (is_numeric($val)) return $val . 'px';
    if (preg_match('/^\d+$/', (string)$val)) return $val . 'px';
    return $val;
  };

  $width  = $ensureUnit($meta['width']  ?? null, '100%');
  $height = $ensureUnit($meta['height'] ?? null, 'auto');
@endphp

<div class="image-element">
  <img
    src="{{ $src }}"
    alt="{{ $alt }}"
    class="image-element__img"
    style="width: {{ $width }}; height: {{ $height }}; object-fit: {{ $objectFit }};"
    loading="lazy"
  />
</div>
