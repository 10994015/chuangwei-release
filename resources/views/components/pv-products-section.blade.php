{{-- resources/views/components/pv-products-section.blade.php --}}
@php
  $data     = $frame['data'] ?? [];
  $products = $data['products'] ?? [];

  $mapProduct = fn($p) => [
    'id'         => $p['id']     ?? null,
    'title'      => $p['name']   ?? '',
    'source'     => $p['source'] ?? ($p['temple'] ?? ''),
    'price'      => isset($p['price']) ? 'NT$ ' . number_format((float)$p['price']) : '',
    'image'      => $p['imgSrc'] ?? null,
    'badge'      => (isset($p['labels']) && is_array($p['labels']) && count($p['labels']))
                      ? $p['labels'][0] : null,
    'badgeClass' => (function() use ($p) {
      $l = $p['labels'][0] ?? '';
      if (in_array($l, ['熱門','hot']))               return 'hot';
      if (in_array($l, ['推薦','recommended']))       return 'recommended';
      if (in_array($l, ['新品','new']))               return 'new';
      return 'hot';
    })(),
  ];

  $mappedProducts = collect($products)->map($mapProduct)->values()->toArray();
  $pageSize       = 4;
  $sectionId      = 'pv-prod-' . uniqid();
@endphp

<section class="pv-products-section">
  <div class="pv-ps-container">

    {{-- 標題列 --}}
    <div class="pv-ps-section-header">
      <div class="pv-ps-header-left">
        <h2 class="pv-ps-section-title">{{ __('ui.pvProductsSection.title') }}</h2>
        <p class="pv-ps-section-subtitle">{{ __('ui.pvProductsSection.subtitle') }}</p>
      </div>
      <div class="pv-ps-header-right">
        <a href="#" class="pv-ps-view-all">{{ __('ui.pvProductsSection.viewMore') }}</a>
        <button class="pv-ps-nav-btn" id="{{ $sectionId }}-prev" aria-label="{{ __('ui.pvProductsSection.prev') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
        </button>
        <button class="pv-ps-nav-btn" id="{{ $sectionId }}-next" aria-label="{{ __('ui.pvProductsSection.next') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </button>
      </div>
    </div>

    {{-- 商品 Grid --}}
    <div class="pv-ps-products-grid" id="{{ $sectionId }}-grid">
      @forelse($mappedProducts as $product)
        <div class="pv-ps-product-card">
          <div class="pv-ps-product-image">
            @if($product['image'])
              <img
                src="{{ $product['image'] }}"
                alt="{{ $product['title'] }}"
                class="pv-ps-image"
                loading="lazy"
                onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
              />
              <div class="pv-ps-image-placeholder" style="display:none"><span>{{ __('ui.pvProductsSection.noImage') }}</span></div>
            @else
              <div class="pv-ps-image-placeholder"><span>{{ __('ui.pvProductsSection.noImage') }}</span></div>
            @endif
          </div>
          <div class="pv-ps-product-info">
            @if($product['badge'])
              <span class="pv-ps-badge {{ $product['badgeClass'] }}">{{ $product['badge'] }}</span>
            @endif
            <p class="pv-ps-product-source">{{ $product['source'] }}</p>
            <h3 class="pv-ps-product-title">{{ $product['title'] }}</h3>
            <div class="pv-ps-product-footer">
              <span class="pv-ps-product-price">{{ $product['price'] }}</span>
              <button class="pv-ps-cart-btn" aria-label="{{ __('ui.pvProductsSection.addToCart') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      @empty
        <div class="pv-ps-empty">{{ __('ui.pvProductsSection.empty') }}</div>
      @endforelse
    </div>

  </div>
</section>

<style>
.pv-products-section { padding: 3rem 0 4rem; background: transparent; }
.pv-ps-container { max-width: 1400px; margin: 0 auto; padding: 0 3rem; }
.pv-ps-section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.75rem; }
.pv-ps-header-left  { display: flex; flex-direction: column; gap: 4px; }
.pv-ps-section-title { font-size: 26px; font-weight: 700; color: var(--frame-heading-color,#222); margin: 0; line-height: 1.2; }
.pv-ps-section-subtitle { font-size: 13px; color: var(--frame-text-muted,#999); margin: 0; }
.pv-ps-header-right { display: flex; align-items: center; gap: 8px; }
.pv-ps-view-all {
  padding: 8px 20px;
  border: 1.5px solid var(--frame-border-color,#ddd);
  border-radius: 20px; font-size: 14px;
  color: var(--frame-text-color,#444); text-decoration: none; white-space: nowrap; transition: all 0.2s;
}
.pv-ps-view-all:hover { border-color: #E8572A; color: #E8572A; }
.pv-ps-nav-btn {
  width: 36px; height: 36px;
  border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 50%;
  background: transparent; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s; color: var(--frame-text-color,#444);
}
.pv-ps-nav-btn svg  { width: 16px; height: 16px; }
.pv-ps-nav-btn:hover { border-color: #E8572A; color: #E8572A; }

.pv-ps-products-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }

.pv-ps-product-card {
  background: var(--frame-card-bg,#fff);
  border: 1px solid var(--frame-border-color,#eee);
  border-radius: 12px; overflow: hidden; cursor: pointer;
  transition: box-shadow 0.2s, transform 0.2s;
}
.pv-ps-product-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }
.pv-ps-product-image { position: relative; width: 100%; aspect-ratio: 4/3; overflow: hidden; background: #f5f5f5; }
.pv-ps-image { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
.pv-ps-product-card:hover .pv-ps-image { transform: scale(1.03); }
.pv-ps-image-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--frame-tag-bg,#f0f0f0); }
.pv-ps-image-placeholder span { font-size: 13px; color: var(--frame-text-muted,#bbb); }
.pv-ps-badge { position: absolute; top: 14px; right: 16px; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #fff; line-height: 1.4; }
.pv-ps-badge.hot         { background: #dc3545; }
.pv-ps-badge.recommended { background: #1a73e8; }
.pv-ps-badge.new         { background: #2ecc71; }
.pv-ps-badge.default     { background: #E8572A; }
.pv-ps-product-info   { position: relative; padding: 14px 16px 16px; }
.pv-ps-product-source { font-size: 13px; color: var(--frame-text-muted,#999); margin: 0 0 4px; }
.pv-ps-product-title  { font-size: 18px; font-weight: 700; color: var(--frame-text-color,#222); margin: 0 0 14px; line-height: 1.3; }
.pv-ps-product-footer { display: flex; justify-content: space-between; align-items: center; }
.pv-ps-product-price  { font-size: 16px; font-weight: 500; color: var(--frame-text-color,#333); }
.pv-ps-cart-btn { width: 36px; height: 36px; background: transparent; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #E8572A; transition: transform 0.2s; padding: 0; }
.pv-ps-cart-btn svg   { width: 20px; height: 20px; }
.pv-ps-cart-btn:hover { transform: scale(1.15); }
.pv-ps-empty { grid-column: 1/-1; padding: 2rem; text-align: center; color: var(--frame-text-muted,#bbb); font-size: 14px; }

@media (max-width: 1024px) { .pv-ps-products-grid { grid-template-columns: repeat(2,1fr); gap: 16px; } }
@media (max-width: 768px) {
  .pv-products-section    { padding: 2rem 0 2.5rem; }
  .pv-ps-container        { padding: 0 1rem; }
  .pv-ps-section-header   { flex-direction: column; align-items: flex-start; gap: 12px; }
  .pv-ps-header-right     { align-self: flex-end; }
  .pv-ps-products-grid    { grid-template-columns: repeat(2,1fr); gap: 12px; }
  .pv-ps-section-title    { font-size: 20px; }
  .pv-ps-product-title    { font-size: 15px; }
  .pv-ps-product-source   { font-size: 12px; }
  .pv-ps-product-price    { font-size: 14px; }
}
@media (max-width: 480px) { .pv-ps-products-grid { grid-template-columns: 1fr; } }
</style>

<script>
(function () {
  var id       = '{{ $sectionId }}';
  var grid     = document.getElementById(id + '-grid');
  var btnPrev  = document.getElementById(id + '-prev');
  var btnNext  = document.getElementById(id + '-next');
  if (!grid || !btnPrev || !btnNext) return;

  var PAGE_SIZE = {{ $pageSize }};
  var cards     = Array.from(grid.querySelectorAll('.pv-ps-product-card'));
  var total     = Math.max(1, Math.ceil(cards.length / PAGE_SIZE));
  var page      = 0;

  function render() {
    var start = page * PAGE_SIZE;
    cards.forEach(function (c, i) {
      c.style.display = (i >= start && i < start + PAGE_SIZE) ? '' : 'none';
    });
  }

  btnPrev.addEventListener('click', function () {
    page = (page - 1 + total) % total;
    render();
  });
  btnNext.addEventListener('click', function () {
    page = (page + 1) % total;
    render();
  });

  render();
})();
</script>
