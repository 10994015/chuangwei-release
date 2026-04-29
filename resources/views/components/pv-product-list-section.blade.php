{{-- resources/views/components/pv-product-list-section.blade.php --}}
@php
  $data            = $frame['data'] ?? [];
  $featuredProduct = $data['featuredProduct'] ?? [];

  // ── 篩選參數（來自 GET query）──────────────────────────────
  $apiPage      = max(1, (int) request('page', 1));
  $apiName      = trim(request('name', ''));
  $apiType      = request('type', '');
  $apiSortCombo = request('sort', '');           // e.g. "publishAt|DESC"
  $apiPageSize  = 10;

  [$apiSortBy, $apiSortOrder] = array_pad(explode('|', $apiSortCombo, 2), 2, '');

  // ── 呼叫 /api/product/all ────────────────────────────────
  $restProducts = [];
  $totalPages   = 1;
  $total        = 0;

  try {
    $apiBase = rtrim(config('app.api_base_url', env('API_BASE_URL', '')), '/');
    $query   = array_filter([
      'page'       => $apiPage,
      'pageSize'   => $apiPageSize,
      'name'       => $apiName      ?: null,
      'type'       => $apiType      ?: null,
      'sortBy'     => $apiSortBy    ?: null,
      'sortOrder'  => $apiSortOrder ?: null,
    ], fn($v) => $v !== null);

    $res = \Illuminate\Support\Facades\Http::withOptions(['cookies' => false])
      ->withHeaders(['Cookie' => request()->header('Cookie', '')])
      ->get($apiBase . '/api/product/all', $query);

    if ($res->status() === 200) {
      $resData      = $res->json('data') ?? [];
      $rawProducts  = $resData['data']       ?? [];
      $totalPages   = (int)($resData['totalPages'] ?? 1);
      $total        = (int)($resData['total']      ?? 0);

      $restProducts = array_map(fn($p) => [
        'id'         => $p['id']         ?? null,
        'title'      => $p['nameZhTw']   ?? ($p['name'] ?? ''),
        'source'     => $p['tenantName'] ?? '',
        'price'      => isset($p['price']) ? 'NT$ ' . number_format((float)$p['price']) : '',
        'image'      => !empty($p['imgs']) ? ($p['imgs'][0]['url'] ?? null) : null,
        'badge'      => null,
        'badgeClass' => 'default',
      ], $rawProducts);
    }
  } catch (\Throwable $e) {}

  // ── 精選（仍從 frame data）──────────────────────────────
  $mapFeatured = fn($p) => [
    'id'     => $p['id']   ?? null,
    'title'  => $p['name'] ?? '',
    'source' => $p['source'] ?? ($p['tenantName'] ?? ''),
    'price'  => isset($p['price']) ? 'NT$ ' . number_format((float)$p['price']) : '',
    'image'  => $p['imgSrc'] ?? null,
    'badge'  => (isset($p['labels']) && is_array($p['labels']) && count($p['labels'])) ? $p['labels'][0] : null,
    'badgeClass' => (function() use ($p) {
      $l = $p['labels'][0] ?? '';
      if (in_array($l, ['熱門', 'hot']))         return 'hot';
      if (in_array($l, ['推薦', 'recommended'])) return 'recommended';
      return 'default';
    })(),
  ];
  $featuredProducts = collect($featuredProduct)->map($mapFeatured)->values()->toArray();

  // ── 分頁頁碼 ─────────────────────────────────────────────
  $currentPage = $apiPage;
  if ($totalPages <= 7) {
    $pageNumbers = range(1, $totalPages);
  } elseif ($currentPage <= 4) {
    $pageNumbers = [1, 2, 3, 4, 5, '...', $totalPages];
  } elseif ($currentPage >= $totalPages - 3) {
    $pageNumbers = [1, '...', $totalPages-4, $totalPages-3, $totalPages-2, $totalPages-1, $totalPages];
  } else {
    $pageNumbers = [1, '...', $currentPage-1, $currentPage, $currentPage+1, '...', $totalPages];
  }

  $queryBase = request()->except(['page']);

  $featuredPageSize = 3;
  $listId = 'pv-pl-' . uniqid();
@endphp

<section class="pv-product-list-section">
  <div class="pv-pl-container">

    <h2 class="pv-pl-page-title">{{ __('ui.productListBasemap.pageTitle') }}</h2>

    {{-- 篩選欄 --}}
    <form class="pv-pl-filter-bar" method="GET" action="">
      <input type="hidden" name="locale" value="{{ request('locale', 'ZH-TW') }}" />

      <select name="type" class="pv-pl-filter-select" onchange="this.form.submit()">
        <option value="" {{ $apiType === '' ? 'selected' : '' }}>{{ __('ui.productListBasemap.allTypes') }}</option>
        <option value="PRODUCT_AND_SERVICE" {{ $apiType === 'PRODUCT_AND_SERVICE' ? 'selected' : '' }}>{{ __('ui.productListBasemap.typeProductService') }}</option>
        <option value="LAMP" {{ $apiType === 'LAMP' ? 'selected' : '' }}>{{ __('ui.productListBasemap.typeLamp') }}</option>
      </select>

      <select name="sort" class="pv-pl-filter-select" onchange="this.form.submit()">
        <option value="" {{ $apiSortCombo === '' ? 'selected' : '' }}>{{ __('ui.productListBasemap.defaultSort') }}</option>
        <option value="publishAt|DESC" {{ $apiSortCombo === 'publishAt|DESC' ? 'selected' : '' }}>{{ __('ui.productListBasemap.sortNewest') }}</option>
        <option value="price|ASC"     {{ $apiSortCombo === 'price|ASC'     ? 'selected' : '' }}>{{ __('ui.productListBasemap.sortPriceAsc') }}</option>
        <option value="price|DESC"    {{ $apiSortCombo === 'price|DESC'    ? 'selected' : '' }}>{{ __('ui.productListBasemap.sortPriceDesc') }}</option>
      </select>

      <div class="pv-pl-search-box">
        <input type="text" name="name" value="{{ $apiName }}" placeholder="{{ __('ui.productListBasemap.keywordPlaceholder') }}" class="pv-pl-search-input" />
        <button type="submit" class="pv-pl-search-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          {{ __('ui.productListBasemap.searchBtn') }}
        </button>
        @if($apiName || $apiType || $apiSortCombo)
          <a href="{{ url()->current() }}?locale={{ request('locale','ZH-TW') }}" class="pv-pl-reset-btn">{{ __('ui.eventListBasemap.resetBtn') }}</a>
        @endif
      </div>
    </form>

    {{-- 精選推薦（有資料才顯示）--}}
    @if(!empty($featuredProducts))
    <div class="pv-pl-featured-header">
      <h3 class="pv-pl-featured-title">{{ __('ui.productListBasemap.featuredTitle') }}</h3>
      <div class="pv-pl-featured-actions">
        <button class="pv-pl-batch-btn" id="{{ $listId }}-batch-btn">
          {{ __('ui.productListBasemap.batchSelect') }}
        </button>
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
        <a class="pv-pl-product-card" data-id="{{ $product['id'] }}"
           href="/product/{{ $product['id'] }}?locale={{ request('locale','ZH-TW') }}&from={{ $currentSlug ?? 'home' }}">
          <div class="pv-pl-product-image">
            @if($product['image'])
              <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="pv-pl-image" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
              <div class="pv-pl-image-placeholder" style="display:none"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
            @else
              <div class="pv-pl-image-placeholder"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
            @endif
            <div class="pv-pl-check" style="display:none">
              <svg class="pv-pl-check-icon" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" style="display:none">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
            </div>
          </div>
          <div class="pv-pl-product-info">
            @if($product['badge'])
              <span class="pv-pl-badge {{ $product['badgeClass'] }}">{{ $product['badge'] }}</span>
            @endif
            <p class="pv-pl-product-source">{{ $product['source'] }}</p>
            <h3 class="pv-pl-product-title">{{ $product['title'] }}</h3>
            <div class="pv-pl-product-footer">
              <span class="pv-pl-product-price">{{ $product['price'] }}</span>
              <span class="pv-pl-cart-btn" aria-label="{{ __('ui.productListBasemap.addToCart') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
              </span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
    @endif

    {{-- 其餘商品 --}}
    @if(count($restProducts) > 0)
      <div class="pv-pl-products-grid pv-pl-products-grid--rest" id="{{ $listId }}-rest">
        @foreach($restProducts as $product)
          <a class="pv-pl-product-card" data-id="{{ $product['id'] }}"
             href="/product/{{ $product['id'] }}?locale={{ request('locale','ZH-TW') }}&from={{ $currentSlug ?? 'home' }}">
            <div class="pv-pl-product-image">
              @if($product['image'])
                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="pv-pl-image" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
                <div class="pv-pl-image-placeholder" style="display:none"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
              @else
                <div class="pv-pl-image-placeholder"><span>{{ __('ui.productListBasemap.noImage') }}</span></div>
              @endif
              <div class="pv-pl-check" style="display:none">
                <svg class="pv-pl-check-icon" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" style="display:none">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </div>
            </div>
            <div class="pv-pl-product-info">
              @if($product['badge'])
                <span class="pv-pl-badge {{ $product['badgeClass'] }}">{{ $product['badge'] }}</span>
              @endif
              <p class="pv-pl-product-source">{{ $product['source'] }}</p>
              <h3 class="pv-pl-product-title">{{ $product['title'] }}</h3>
              <div class="pv-pl-product-footer">
                <span class="pv-pl-product-price">{{ $product['price'] }}</span>
                <span class="pv-pl-cart-btn" aria-label="{{ __('ui.productListBasemap.addToCart') }}">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                  </svg>
                </span>
              </div>
            </div>
          </a>
        @endforeach
      </div>

      {{-- 分頁（server-side）--}}
      @if($totalPages > 1)
        <div class="pv-pl-pagination">
          @if($currentPage <= 1)
            <span class="pv-pl-page-circle disabled">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
          @else
            <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['page' => $currentPage - 1])) }}" class="pv-pl-page-circle">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
          @endif

          @foreach($pageNumbers as $pn)
            @if($pn === '...')
              <span class="pv-pl-page-ellipsis">...</span>
            @else
              <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['page' => $pn])) }}"
                 class="pv-pl-page-circle {{ $currentPage == $pn ? 'active' : '' }}">{{ $pn }}</a>
            @endif
          @endforeach

          @if($currentPage >= $totalPages)
            <span class="pv-pl-page-circle disabled">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
          @else
            <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['page' => $currentPage + 1])) }}" class="pv-pl-page-circle">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
          @endif
        </div>
      @endif

    @else
      <div class="pv-pl-empty">{{ __('ui.productListBasemap.empty') }}</div>
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
.pv-pl-reset-btn {
  height: 40px; padding: 0 16px;
  border: 1.5px solid #ddd; border-radius: 20px;
  background: transparent; color: #666;
  font-size: 14px; text-decoration: none; white-space: nowrap;
  display: inline-flex; align-items: center; transition: all 0.2s;
}
.pv-pl-reset-btn:hover { border-color: #E8572A; color: #E8572A; }

.pv-pl-featured-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
.pv-pl-featured-title  { font-size: 20px; font-weight: 700; color: var(--frame-heading-color,#222); margin: 0; }
.pv-pl-featured-actions { display: flex; align-items: center; gap: 8px; }

/* 新增：批次選擇按鈕 */
.pv-pl-batch-btn {
  height: 36px; padding: 0 18px;
  border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 20px;
  background: transparent; font-size: 14px;
  color: var(--frame-text-color,#555); cursor: pointer; transition: all 0.2s;
}
.pv-pl-batch-btn:hover { border-color: #E8572A; color: #E8572A; }
.pv-pl-batch-btn.active { border-color: #E8572A; color: #E8572A; }

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
  text-decoration: none; display: block; color: inherit;
}
.pv-pl-product-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.09); transform: translateY(-2px); }
/* 新增：被選取的卡片樣式 */
.pv-pl-product-card.is-selected { box-shadow: 0 0 0 2px #E8572A; }

.pv-pl-product-image { position: relative; width: 100%; aspect-ratio: 4/3; overflow: hidden; background: #f5f5f5; }
.pv-pl-image { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
.pv-pl-product-card:hover .pv-pl-image { transform: scale(1.03); }
.pv-pl-image-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--frame-tag-bg,#f0f0f0); }
.pv-pl-image-placeholder span { font-size: 13px; color: var(--frame-text-muted,#bbb); }

/* 修正：badge 位移對齊 Vue（top:12 right:14） */
.pv-pl-badge { position: absolute; top: 12px; right: 14px; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #fff; }
.pv-pl-badge.hot         { background: #dc3545; }
.pv-pl-badge.recommended { background: #1a73e8; }
.pv-pl-badge.new         { background: #2ecc71; }
.pv-pl-badge.default     { background: #E8572A; }

/* 新增：批次選取 checkbox */
.pv-pl-check {
  position: absolute; top: 10px; right: 10px;
  width: 24px; height: 24px;
  border: 2px solid #fff; border-radius: 50%;
  background: rgba(255,255,255,0.3);
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}
.pv-pl-check.checked { background: #E8572A; border-color: #E8572A; }
.pv-pl-check-icon { width: 14px; height: 14px; }

.pv-pl-product-info   { position: relative; padding: 12px 14px 14px; }
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
  var id         = '{{ $listId }}';
  var featuredEl = document.getElementById(id + '-featured');
  var restEl     = document.getElementById(id + '-rest');
  var btnPrev    = document.getElementById(id + '-feat-prev');
  var btnNext    = document.getElementById(id + '-feat-next');
  var batchBtn   = document.getElementById(id + '-batch-btn');

  var FEAT_SIZE = {{ $featuredPageSize }};

  // ── 批次選擇 ──
  var batchMode   = false;
  var selectedIds = [];

  function getAllCards() {
    var cards = [];
    if (featuredEl) cards = cards.concat(Array.from(featuredEl.querySelectorAll('.pv-pl-product-card')));
    if (restEl)     cards = cards.concat(Array.from(restEl.querySelectorAll('.pv-pl-product-card')));
    return cards;
  }

  function toggleBatch() {
    batchMode = !batchMode;
    selectedIds = [];
    batchBtn.classList.toggle('active', batchMode);
    getAllCards().forEach(function (card) {
      var chk = card.querySelector('.pv-pl-check');
      if (chk) chk.style.display = batchMode ? 'flex' : 'none';
      card.classList.remove('is-selected');
      var icon = card.querySelector('.pv-pl-check-icon');
      if (icon) icon.style.display = 'none';
      if (chk) chk.classList.remove('checked');
    });
  }

  function handleCardClick(card) {
    if (!batchMode) return;
    var cardId = card.dataset.id;
    var idx    = selectedIds.indexOf(cardId);
    var chk    = card.querySelector('.pv-pl-check');
    var icon   = card.querySelector('.pv-pl-check-icon');
    if (idx === -1) {
      selectedIds.push(cardId);
      card.classList.add('is-selected');
      if (chk)  chk.classList.add('checked');
      if (icon) icon.style.display = 'block';
    } else {
      selectedIds.splice(idx, 1);
      card.classList.remove('is-selected');
      if (chk)  chk.classList.remove('checked');
      if (icon) icon.style.display = 'none';
    }
  }

  if (batchBtn) batchBtn.addEventListener('click', toggleBatch);

  getAllCards().forEach(function (card) {
    card.addEventListener('click', function (e) {
      if (e.target.closest('.pv-pl-cart-btn')) return;
      handleCardClick(card);
    });
  });

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
})();
</script>
