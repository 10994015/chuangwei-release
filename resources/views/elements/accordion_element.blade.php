{{-- resources/views/elements/accordion_element.blade.php --}}
@php
  $items       = $el['value']['items'] ?? [];
  $content     = $el['content']        ?? [];

  $titleColor     = $content['titleColor']     ?? null;
  $titleFontSize  = $content['titleFontSize']  ?? null;
  $contentColor   = $content['contentColor']   ?? null;
  $contentFontSize = $content['contentFontSize'] ?? null;
  $defaultOpen    = $content['defaultOpen']    ?? false;

  // 確保單位
  $ensureUnit = fn($v, $default) => !$v ? $default
    : (is_numeric($v) ? $v . 'px' : $v);

  $titleFontSizeCss   = $ensureUnit($titleFontSize,   '16px');
  $contentFontSizeCss = $ensureUnit($contentFontSize, '15px');

  $titleColorCss   = $titleColor   ?: 'var(--frame-text-color, #333)';
  $contentColorCss = $contentColor ?: 'var(--frame-text-color, #666)';

  $accordionId = 'accordion-' . uniqid();
@endphp

<div class="accordion-element" id="{{ $accordionId }}">
  @foreach($items as $index => $item)
    @php
      $isOpen   = $defaultOpen && $index === 0;
      $isHtml   = is_string($item['content'] ?? '') && preg_match('/<[a-z][\s\S]*>/i', $item['content'] ?? '');
    @endphp
    <div
      class="ac-item{{ $isOpen ? ' is-open' : '' }}"
      data-index="{{ $index }}"
    >
      <button
        class="ac-header"
        style="color:{{ $titleColorCss }};font-size:{{ $titleFontSizeCss }};font-weight:600;"
        onclick="acToggle(this)"
        type="button"
      >
        <span class="ac-title">{{ $item['title'] ?? '' }}</span>
        <svg
          class="ac-icon"
          width="18" height="18" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round"
        >
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </button>
      <div class="ac-body{{ $isOpen ? ' is-open' : '' }}">
        <div
          class="ac-content"
          style="color:{{ $contentColorCss }};font-size:{{ $contentFontSizeCss }};"
        >
          @if($isHtml)
            {!! $item['content'] ?? '' !!}
          @else
            {{ $item['content'] ?? '' }}
          @endif
        </div>
      </div>
    </div>
  @endforeach
</div>

<style>
.accordion-element {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.ac-item {
  border: 1px solid var(--frame-border-color, #e8e8e8);
  border-radius: 10px;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  overflow: hidden;
  transition: box-shadow 0.2s, border-color 0.2s;
}
.ac-item:hover { box-shadow: 0 3px 12px rgba(0,0,0,0.10); border-color: #d0d0d0; }
.ac-item.is-open { border-color: #E8572A; box-shadow: 0 3px 12px rgba(232,87,42,0.12); }
.ac-header {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 20px;
  background: transparent;
  border: none;
  cursor: pointer;
  text-align: left;
  gap: 12px;
  transition: color 0.2s;
}
.ac-item.is-open .ac-header { color: #E8572A !important; }
.ac-header:hover { color: #E8572A !important; }
.ac-title { flex: 1; line-height: 1.5; }
.ac-icon  {
  flex-shrink: 0;
  transition: transform 0.25s ease;
  opacity: 0.6;
}
.ac-item.is-open .ac-icon { transform: rotate(180deg); opacity: 1; }

/* grid trick for smooth height animation */
.ac-body {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.28s ease;
}
.ac-body.is-open { grid-template-rows: 1fr; }
.ac-content {
  overflow: hidden;
  padding: 0 20px;
  line-height: 1.8;
  white-space: pre-line;
  transition: padding 0.28s ease;
}
.ac-body.is-open .ac-content { padding: 0 20px 20px; }
</style>

<script>
function acToggle(btn) {
  var item = btn.closest('.ac-item');
  var body = item.querySelector('.ac-body');
  var isOpen = item.classList.contains('is-open');
  item.classList.toggle('is-open', !isOpen);
  body.classList.toggle('is-open', !isOpen);
}
</script>
