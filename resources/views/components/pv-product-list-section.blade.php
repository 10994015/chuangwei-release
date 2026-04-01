{{-- resources/views/components/pv-product-list-section.blade.php --}}
@php
  $data            = $frame['data'] ?? [];
  $featuredProduct = $data['featuredProduct'] ?? [];
  $productList     = $data['productList']     ?? [];
  $rawRest         = $productList['data']     ?? $productList ?? [];

  $mapProduct = fn($p) => [
    'id'     => $p['id']     ?? null,
    'title'  => $p['name']   ?? '',
    'source' => $p['source'] ?? ($p['tenantName'] ?? ($p['temple'] ?? '')),
    'price'  => isset($p['price']) ? 'NT$ ' . number_format((float)$p['price']) : '',
    'image'  => $p['imgSrc'] ?? null,
    'badge'  => (isset($p['labels']) && is_array($p['labels']) && count($p['labels']))
                  ? $p['labels'][0] : null,
    'badgeClass' => (function() use ($p) {
      $l = $p['labels'][0] ?? '';
      if (in_array($l, ['熱門', 'hot']))               return 'hot';
      if (in_array($l, ['推薦', 'recommended']))       return 'recommended';
      return 'hot';
    })(),
  ];

  $featuredProducts = collect($featuredProduct)->map($mapProduct)->values()->toArray();
  $restProducts     = collect($rawRest)->map($mapProduct)->values()->toArray();

  // 精選：每頁 3 筆，server-side 只顯示第一頁（JS 做翻頁）
  $featuredPageSize = 3;
  $restPageSize     = 5;

  $listId = 'pv-pl-' . uniqid();
@endphp

<section class="pv-product-list-section">
  <div class="pv-pl-container">

    <h2 class="pv-pl-page-title">{{ __('ui.productListBasemap.pageTitle') }}</h2>

    {{-- 篩選欄 --}}
    <div class="pv-pl-filter-bar">
      <select class="pv-pl-filter-select"><option>{{ __('ui.productListBasemap.allTypes') }}</option></select>
      <select class="pv-pl-filter-select"><option>{{ __('ui.productListBasemap.allNeeds') }}</option></select>
      <select class="pv-pl-filter-select"><option>{{ __('ui.productListBasemap.defaultSort') }}</option></select>
      <div class="pv-pl-search-box">
        <input type="text" placeholder="{{ __('ui.productListBasemap.keywordPlaceholder') }}" class="pv-pl-search-input" id="{{ $listId }}-search" />
        <button class="pv-pl-search-btn" id="{{ $listId }}-search-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          {{ __('ui.productListBasemap.searchBtn') }}
        </button>
      </div>
    </div>

    {{-- 精選推薦 --}}
    <div class="pv-pl-featured-header">
      <h3 class="pv-pl-featured-title">{{ __('ui.productListBasemap.featuredTitle') }}</h3>
      <div class="pv-pl-featured-actions">
        <button class="pv-pl-nav-circle" id="{{ $listId }}-feat-prev" aria-label="{{ __('ui.pvProductsSection.prev') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="pv-pl-nav-circle" id="{{ $listId }}-feat-next" aria-label="{{ __('ui.pvProductsSection.next') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>

    <div class="pv-pl-products-grid pv-pl-products-grid--featured" id="{{ $listId }}-featured">
      @foreach($featuredProducts as $product)
        <div class="pv-pl-product-card" data-id="{{ $product['id'] }}">
          <div class="pv-pl-product-image">
            @if($product['image'])
              <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="pv-pl-image" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
              <div class="pv-pl-image-placeholder" style="display:none"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
            @else
              <div class="pv-pl-image-placeholder"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
            @endif
            @if($product['badge'])
              <span class="pv-pl-badge {{ $product['badgeClass'] }}">{{ $product['badge'] }}</span>
            @endif
          </div>
          <div class="pv-pl-product-info">
            <p class="pv-pl-product-source">{{ $product['source'] }}</p>
            <h3 class="pv-pl-product-title">{{ $product['title'] }}</h3>
            <div class="pv-pl-product-footer">
              <span class="pv-pl-product-price">{{ $product['price'] }}</span>
              <button class="pv-pl-cart-btn" aria-label="{{ __('ui.productListBasemap.addToCart') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      @endforeach
      @if(empty($featuredProducts))
        <div class="pv-pl-empty" style="grid-column:1/-1">{{ __('ui.productListBasemap.featuredEmpty') }}</div>
      @endif
    </div>

    {{-- 其餘商品 --}}
    @if(count($restProducts) > 0)
      <div class="pv-pl-products-grid pv-pl-products-grid--rest" id="{{ $listId }}-rest">
        @foreach($restProducts as $product)
          <div class="pv-pl-product-card" data-id="{{ $product['id'] }}">
            <div class="pv-pl-product-image">
              @if($product['image'])
                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="pv-pl-image" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
                <div class="pv-pl-image-placeholder" style="display:none"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
              @else
                <div class="pv-pl-image-placeholder"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
              @endif
              @if($product['badge'])
                <span class="pv-pl-badge {{ $product['badgeClass'] }}">{{ $product['badge'] }}</span>
              @endif
            </div>
            <div class="pv-pl-product-info">
              <p class="pv-pl-product-source">{{ $product['source'] }}</p>
              <h3 class="pv-pl-product-title">{{ $product['title'] }}</h3>
              <div class="pv-pl-product-footer">
                <span class="pv-pl-product-price">{{ $product['price'] }}</span>
                <button class="pv-pl-cart-btn" aria-label="{{ __('ui.productListBasemap.addToCart') }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- 分頁 --}}
      <div class="pv-pl-pagination" id="{{ $listId }}-pagination"></div>
    @endif

  </div>
</section>

<style>
.pv-product-list-section { padding: 2.5rem 0 5rem; background: transparent; min-height: 60vh; }
.pv-pl-container { max-width: 1400px; margin: 0 auto; padding: 0 3rem; }
.pv-pl-page-title { font-size: 26px; font-weight: 700; color: var(--frame-heading-color,#222); margin: 0 0 1.5rem; }

.pv-pl-filter-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 2rem; }
.pv-pl-filter-select {
  height: 40px; padding: 0 14px;
  border: 1.5px solid var(--frame-border-color,#ddd);
  border-radius: 20px; font-size: 14px;
  color: var(--frame-text-color,#444); background: #fff; cursor: pointer;
}
.pv-pl-filter-select:focus { outline: none; border-color: #E8572A; }
.pv-pl-search-box { display: flex; align-items: center; gap: 8px; flex: 1; max-width: 400px; }
.pv-pl-search-input {
  flex: 1; height: 40px; padding: 0 16px;
  border: 1.5px solid var(--frame-border-color,#ddd);
  border-radius: 20px; font-size: 14px;
  color: var(--frame-text-color,#333); background: #fff;
}
.pv-pl-search-input:focus { outline: none; border-color: #E8572A; }
.pv-pl-search-btn {
  display: flex; align-items: center; gap: 6px;
  height: 40px; padding: 0 20px;
  border: 1.5px solid #E8572A; border-radius: 20px;
  background: transparent; color: #E8572A;
  font-size: 14px; font-weight: 500; cursor: pointer; white-space: nowrap;
  transition: all 0.2s;
}
.pv-pl-search-btn:hover { background: #E8572A; color: #fff; }

.pv-pl-featured-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
.pv-pl-featured-title  { font-size: 20px; font-weight: 700; color: var(--frame-heading-color,#222); margin: 0; }
.pv-pl-featured-actions { display: flex; align-items: center; gap: 8px; }
.pv-pl-nav-circle {
  width: 36px; height: 36px;
  border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 50%;
  background: transparent; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s; color: var(--frame-text-color,#444);
}
.pv-pl-nav-circle svg { width: 16px; height: 16px; }
.pv-pl-nav-circle:hover { border-color: #E8572A; color: #E8572A; }

.pv-pl-products-grid { display: grid; gap: 16px; margin-bottom: 16px; }
.pv-pl-products-grid--featured { grid-template-columns: repeat(3,1fr); }
.pv-pl-products-grid--rest     { grid-template-columns: repeat(5,1fr); }

.pv-pl-product-card {
  background: var(--frame-card-bg,#fff);
  border: 1px solid var(--frame-border-color,#eee);
  border-radius: 12px; overflow: hidden; cursor: pointer;
  transition: box-shadow 0.2s, transform 0.2s;
}
.pv-pl-product-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.09); transform: translateY(-2px); }
.pv-pl-product-image { position: relative; width: 100%; aspect-ratio: 4/3; overflow: hidden; background: #f5f5f5; }
.pv-pl-image { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
.pv-pl-product-card:hover .pv-pl-image { transform: scale(1.03); }
.pv-pl-image-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--frame-tag-bg,#f0f0f0); }
.pv-pl-image-placeholder span { font-size: 13px; color: var(--frame-text-muted,#bbb); }
.pv-pl-badge { position: absolute; bottom: 10px; right: 10px; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #fff; }
.pv-pl-badge.hot         { background: #dc3545; }
.pv-pl-badge.recommended { background: #1a73e8; }
.pv-pl-product-info   { padding: 12px 14px 14px; }
.pv-pl-product-source { font-size: 12px; color: var(--frame-text-muted,#999); margin: 0 0 3px; }
.pv-pl-product-title  { font-size: 16px; font-weight: 700; color: var(--frame-text-color,#222); margin: 0 0 10px; line-height: 1.3; }
.pv-pl-product-footer { display: flex; justify-content: space-between; align-items: center; }
.pv-pl-product-price  { font-size: 15px; font-weight: 500; color: #E8572A; }
.pv-pl-cart-btn { width: 32px; height: 32px; background: transparent; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center; color: #E8572A; transition: transform 0.2s; }
.pv-pl-cart-btn svg  { width: 18px; height: 18px; }
.pv-pl-cart-btn:hover { transform: scale(1.15); }
.pv-pl-empty { padding: 2rem; text-align: center; color: var(--frame-text-muted,#bbb); font-size: 14px; }

.pv-pl-pagination { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 2.5rem; flex-wrap: wrap; }
.pv-pl-page-circle {
  width: 38px; height: 38px;
  border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 50%;
  background: transparent; font-size: 14px; color: var(--frame-text-secondary,#555);
  cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.pv-pl-page-circle svg { width: 16px; height: 16px; }
.pv-pl-page-circle:hover:not(.disabled):not(.active) { border-color: #E8572A; color: #E8572A; }
.pv-pl-page-circle.active   { background: #E8572A; border-color: #E8572A; color: #fff; font-weight: 600; }
.pv-pl-page-circle.disabled { opacity: 0.35; cursor: default; pointer-events: none; }
.pv-pl-page-ellipsis { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; color: var(--frame-text-muted,#bbb); letter-spacing: 2px; }

@media (max-width: 1024px) {
  .pv-pl-products-grid--rest { grid-template-columns: repeat(3,1fr); }
}
@media (min-width: 769px) and (max-width: 1024px) {
  .pv-pl-container { padding: 0 1.5rem; }
  .pv-pl-products-grid--featured { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 768px) {
  .pv-product-list-section { padding: 1.5rem 0 3rem; }
  .pv-pl-container { padding: 0 1rem; }
  .pv-pl-filter-bar { gap: 8px; }
  .pv-pl-search-box { max-width: 100%; }
  .pv-pl-products-grid--featured { grid-template-columns: 1fr; }
  .pv-pl-products-grid--rest     { grid-template-columns: repeat(2,1fr); }
  .pv-pl-product-title  { font-size: 14px; }
  .pv-pl-product-source { font-size: 11px; }
}
</style>

<script>
(function () {
  var id          = '{{ $listId }}';
  var featuredEl  = document.getElementById(id + '-featured');
  var restEl      = document.getElementById(id + '-rest');
  var pagination  = document.getElementById(id + '-pagination');
  var btnPrev     = document.getElementById(id + '-feat-prev');
  var btnNext     = document.getElementById(id + '-feat-next');

  var FEAT_SIZE = {{ $featuredPageSize }};
  var REST_SIZE = {{ $restPageSize }};

  // ── 精選翻頁 ──
  if (featuredEl && btnPrev && btnNext) {
    var featCards = Array.from(featuredEl.querySelectorAll('.pv-pl-product-card'));
    var featPage  = 0;
    var featTotal = Math.max(1, Math.ceil(featCards.length / FEAT_SIZE));

    function renderFeatured() {
      var start = featPage * FEAT_SIZE;
      featCards.forEach(function (c, i) {
        c.style.display = (i >= start && i < start + FEAT_SIZE) ? '' : 'none';
      });
    }

    btnPrev.addEventListener('click', function () { if (featPage > 0) { featPage--; renderFeatured(); } });
    btnNext.addEventListener('click', function () { if (featPage < featTotal - 1) { featPage++; renderFeatured(); } });
    renderFeatured();
  }

  // ── rest 分頁 ──
  if (restEl && pagination) {
    var restCards  = Array.from(restEl.querySelectorAll('.pv-pl-product-card'));
    var restPage   = 1;
    var restTotal  = Math.max(1, Math.ceil(restCards.length / REST_SIZE));

    function renderRest() {
      var start = (restPage - 1) * REST_SIZE;
      restCards.forEach(function (c, i) {
        c.style.display = (i >= start && i < start + REST_SIZE) ? '' : 'none';
      });
      renderPagination();
    }

    function renderPagination() {
      pagination.innerHTML = '';
      if (restTotal <= 1) return;

      var pages = buildPages(restPage, restTotal);

      appendCircle(pagination, prevSvg(), restPage === 1, function () { if (restPage > 1) { restPage--; renderRest(); } });

      pages.forEach(function (p) {
        if (p === '...') {
          var ell = document.createElement('span');
          ell.className = 'pv-pl-page-ellipsis';
          ell.textContent = '...';
          pagination.appendChild(ell);
        } else {
          appendCircle(pagination, p, false, function () { restPage = p; renderRest(); }, p === restPage);
        }
      });

      appendCircle(pagination, nextSvg(), restPage === restTotal, function () { if (restPage < restTotal) { restPage++; renderRest(); } });
    }

    function appendCircle(parent, content, disabled, onClick, active) {
      var btn = document.createElement('button');
      btn.className = 'pv-pl-page-circle' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
      if (typeof content === 'string' && content.startsWith('<')) {
        btn.innerHTML = content;
      } else {
        btn.textContent = content;
      }
      btn.addEventListener('click', onClick);
      parent.appendChild(btn);
    }

    function prevSvg() { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>'; }
    function nextSvg() { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>'; }

    function buildPages(cur, total) {
      if (total <= 7) return Array.from({ length: total }, function (_, i) { return i + 1; });
      if (cur <= 4)          return [1, 2, 3, 4, 5, '...', total];
      if (cur >= total - 3)  return [1, '...', total-4, total-3, total-2, total-1, total];
      return [1, '...', cur-1, cur, cur+1, '...', total];
    }

    renderRest();
  }
})();
</script>
