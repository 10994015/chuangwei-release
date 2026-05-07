{{-- resources/views/components/product-list-section.blade.php --}}
@php
    // ── 呼叫 /api/product/temple ──────────────────────────────────────
    $currentPage  = max(1, (int) request('page', 1));
    $apiKeyword   = trim(request('name', ''));
    $apiSortCombo = request('sort', '');
    [$apiSortBy, $apiSortOrder] = array_pad(explode('|', $apiSortCombo, 2), 2, '');

    $donationRaw = [];
    $totalPages  = 1;
    $total       = 0;

    try {
        $host      = request()->getHost();
        $parts     = explode('.', $host);
        $subdomain = (count($parts) >= 3) ? $parts[0]
                   : ((count($parts) === 2 && $parts[1] === 'localhost') ? $parts[0] : '');
        $apiBase = $subdomain
            ? rtrim('https://' . $subdomain . '.' . config('api.base_domain'), '/')
            : rtrim(config('api.base_url', env('API_BASE_URL', '')), '/');

        $donationRes = \Illuminate\Support\Facades\Http::withOptions(['cookies' => false])
            ->withHeaders(['Cookie' => request()->header('Cookie', '')])
            ->get($apiBase . '/api/product/temple', array_filter([
                'page'      => $currentPage,
                'pageSize'  => 10,
                'name'      => $apiKeyword  ?: null,
                'sortBy'    => $apiSortBy   ?: null,
                'sortOrder' => $apiSortOrder ?: null,
            ], fn($v) => $v !== null));

        if ($donationRes->status() === 200) {
            $resData     = $donationRes->json('data') ?? [];
            $donationRaw = $resData['data']       ?? [];
            $totalPages  = (int)($resData['totalPages'] ?? 1);
            $total       = (int)($resData['total']      ?? 0);
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('[product-list-section] API error: ' . $e->getMessage());
    }

    $data        = $frame['data'] ?? [];
    $featuredList = [];

    $productList = array_map(function ($item) {
        if (isset($item['price'])) {
            $price = 'NT$ ' . number_format((float)$item['price']);
        } else {
            $skus = $item['skus'] ?? [];
            $minPrice = null;
            foreach ($skus as $sku) {
                $p = isset($sku['price']) ? (float)$sku['price'] : null;
                if ($p !== null && ($minPrice === null || $p < $minPrice)) $minPrice = $p;
            }
            $price = $minPrice !== null ? 'NT$ ' . number_format($minPrice) : '';
        }
        $image = $item['coverImg'] ?? (!empty($item['imgs']) ? ($item['imgs'][0]['url'] ?? null) : null);

        return [
            'id'         => $item['id']        ?? null,
            'title'      => $item['nameZhTw']   ?? ($item['name'] ?? ''),
            'tenantName' => $item['tenantName'] ?? '',
            'price'      => $price,
            'image'      => $image,
            'status'     => $item['status']     ?? '',
        ];
    }, $donationRaw);

    // 分頁
    if ($totalPages <= 7) {
        $pageNumbers = range(1, max(1, $totalPages));
    } elseif ($currentPage <= 4) {
        $pageNumbers = [1, 2, 3, 4, 5, '...', $totalPages];
    } elseif ($currentPage >= $totalPages - 3) {
        $pageNumbers = [1, '...', $totalPages-4, $totalPages-3, $totalPages-2, $totalPages-1, $totalPages];
    } else {
        $pageNumbers = [1, '...', $currentPage-1, $currentPage, $currentPage+1, '...', $totalPages];
    }
    $queryBase = request()->except(['page']);

    $sortOptions = [
        ['label' => '預設排序',   'value' => ''],
        ['label' => '最新上架',   'value' => 'publishAt|DESC'],
        ['label' => '價格低到高', 'value' => 'price|ASC'],
        ['label' => '價格高到低', 'value' => 'price|DESC'],
    ];
    $device  = $device ?? 'desktop';
    $listId  = 'pl-' . uniqid();
    $featuredPageSize = 3;
@endphp

<section class="product-list-section device-{{ $device }}">
    <div class="container">

        {{-- 篩選欄 --}}
        <form class="filter-bar" method="GET" action="">
            <input type="hidden" name="locale" value="{{ request('locale', 'ZH-TW') }}" />
            <div class="filter-group">
                <label class="filter-label">排序方式</label>
                <select class="filter-select mid" name="sort" onchange="this.form.submit()">
                    @foreach ($sortOptions as $opt)
                        <option value="{{ $opt['value'] }}" {{ $apiSortCombo === $opt['value'] ? 'selected' : '' }}>
                            {{ $opt['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group search-group">
                <label class="filter-label">關鍵字搜尋</label>
                <div class="search-box">
                    <input type="text" name="name" value="{{ $apiKeyword }}"
                        placeholder="搜尋結緣商品名稱" class="search-input" />
                    <button class="search-btn" type="submit">搜尋</button>
                    @if($apiKeyword || $apiSortCombo)
                        <a href="{{ url()->current() }}?locale={{ request('locale','ZH-TW') }}" class="reset-btn">重置</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- 精選推薦標題列（永遠顯示，批次按鈕需要）--}}
        <div class="pl-featured-header">
            <h3 class="pl-featured-title">精選推薦</h3>
            <div class="pl-featured-actions">
                {{-- 正常模式 --}}
                <div class="pl-normal-actions" id="{{ $listId }}-normal-actions">
                    <button class="pl-batch-btn" id="{{ $listId }}-batch-btn">批次選擇</button>
                </div>
                {{-- 批次模式 --}}
                <div class="pl-batch-controls" id="{{ $listId }}-batch-controls" style="display:none">
                    <button class="pl-cancel-btn" id="{{ $listId }}-cancel-btn">✕ 取消批次</button>
                    <button class="pl-select-all-btn" id="{{ $listId }}-select-all-btn">✓ 全選</button>
                </div>
                {{-- 左右導覽 --}}
                <div class="pl-nav-buttons">
                    <button class="pl-nav-circle" id="{{ $listId }}-feat-prev" aria-label="上一頁">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="pl-nav-circle" id="{{ $listId }}-feat-next" aria-label="下一頁">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- 精選（有資料才顯示）--}}
        @if(!empty($featuredList))
            <div class="products-grid products-grid--featured" id="{{ $listId }}-featured">
                @foreach($featuredList as $product)
                    <div class="product-card" data-id="{{ $product['id'] ?? '' }}">
                        <div class="product-image">
                            @if($product['image'] ?? null)
                                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="image" />
                            @else
                                <div class="image-placeholder"><span>暫無圖片</span></div>
                            @endif
                            <div class="pl-check" style="display:none">
                                <svg class="pl-check-icon" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" style="display:none">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title">{{ $product['title'] }}</h3>
                            <div class="product-footer">
                                <span class="product-price">{{ $product['price'] ?: '免費參與' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 商品列表 --}}
        @if(count($productList) > 0)
            <div class="products-grid products-grid--rest" id="{{ $listId }}-rest">
                @foreach($productList as $product)
                    <a class="product-card" data-id="{{ $product['id'] }}"
                       href="/product/temple/{{ $product['id'] }}?locale={{ request('locale','ZH-TW') }}&from={{ request()->segment(1) ?: 'home' }}">
                        <div class="product-image">
                            @if($product['image'])
                                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="image" loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
                                <div class="image-placeholder" style="display:none"><span>暫無圖片</span></div>
                            @else
                                <div class="image-placeholder"><span>暫無圖片</span></div>
                            @endif
                            <div class="pl-check" style="display:none">
                                <svg class="pl-check-icon" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" style="display:none">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                        </div>
                        <div class="product-info">
                            @if($product['tenantName'])
                                <p class="product-source">{{ $product['tenantName'] }}</p>
                            @endif
                            <h3 class="product-title">{{ $product['title'] }}</h3>
                            <div class="product-footer">
                                <span class="product-price">{{ $product['price'] ?: '免費參與' }}</span>
                                <span class="product-cart-icon">
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

            {{-- 分頁 --}}
            @if($totalPages > 1)
                <div class="pl-pagination">
                    @if($currentPage <= 1)
                        <span class="pl-page-btn disabled">&lsaquo;</span>
                    @else
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['page' => $currentPage - 1])) }}" class="pl-page-btn">&lsaquo;</a>
                    @endif

                    @foreach($pageNumbers as $pn)
                        @if($pn === '...')
                            <span class="pl-page-ellipsis">...</span>
                        @else
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['page' => $pn])) }}"
                               class="pl-page-btn {{ $currentPage == $pn ? 'active' : '' }}">{{ $pn }}</a>
                        @endif
                    @endforeach

                    @if($currentPage >= $totalPages)
                        <span class="pl-page-btn disabled">&rsaquo;</span>
                    @else
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['page' => $currentPage + 1])) }}" class="pl-page-btn">&rsaquo;</a>
                    @endif
                </div>
            @endif

        @else
            <div class="empty-state"><p>找不到符合條件的商品</p></div>
        @endif

    </div>
</section>

{{-- 批次操作底欄 --}}
<div class="pl-batch-bar" id="{{ $listId }}-batch-bar">
    <span class="pl-batch-count" id="{{ $listId }}-batch-count">已選 0 個項目</span>
    <button class="pl-batch-cart-btn" id="{{ $listId }}-batch-cart-btn">批次加入購物車</button>
</div>

<style>
.product-list-section a.product-card,
.product-list-section a.product-card:hover,
.product-list-section a.product-card:visited { text-decoration: none !important; color: inherit; }

/* 篩選欄 */
.reset-btn {
    padding: 8px 16px; border: 1.5px solid #ddd; border-radius: 4px;
    background: transparent; color: #666; font-size: 13px;
    text-decoration: none; white-space: nowrap; transition: all 0.2s;
}
.reset-btn:hover { border-color: #E8572A; color: #E8572A; }

/* 精選標題列 */
.pl-featured-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.25rem; margin-top: 1rem;
}
.pl-featured-title { font-size: 20px; font-weight: 700; color: var(--frame-heading-color,#222); margin: 0; }
.pl-featured-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }

/* 正常模式 */
.pl-normal-actions { display: flex; align-items: center; }
.pl-batch-btn {
    height: 36px; padding: 0 18px;
    border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 20px;
    background: transparent; font-size: 14px;
    color: var(--frame-text-color,#555); cursor: pointer; transition: all 0.2s;
}
.pl-batch-btn:hover { border-color: #E8572A; color: #E8572A; }

/* 批次模式控制 */
.pl-batch-controls { display: flex; align-items: center; gap: 8px; }
.pl-cancel-btn {
    height: 36px; padding: 0 16px;
    border: 1.5px solid #ddd; border-radius: 20px;
    background: transparent; font-size: 14px; color: #555;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.pl-cancel-btn:hover { border-color: #dc3545; color: #dc3545; }
.pl-select-all-btn {
    height: 36px; padding: 0 16px;
    border: 1.5px solid #E8572A; border-radius: 20px;
    background: transparent; font-size: 14px; color: #E8572A;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.pl-select-all-btn:hover,
.pl-select-all-btn.all-selected { background: #E8572A; color: #fff; }

/* 左右導覽 */
.pl-nav-buttons { display: flex; align-items: center; gap: 8px; }
.pl-nav-circle {
    width: 36px; height: 36px;
    border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 50%;
    background: transparent; display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s; color: var(--frame-text-color,#444);
}
.pl-nav-circle svg { width: 16px; height: 16px; }
.pl-nav-circle:hover { border-color: #E8572A; color: #E8572A; }

/* 批次 checkbox */
.pl-check {
    position: absolute; top: 10px; right: 10px;
    width: 24px; height: 24px;
    border: 2px solid #fff; border-radius: 50%;
    background: rgba(255,255,255,0.3);
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.pl-check.checked { background: #E8572A; border-color: #E8572A; }
.pl-check-icon { width: 14px; height: 14px; }

/* 卡片選取樣式 */
.product-card.is-selected { box-shadow: 0 0 0 2px #E8572A !important; }

/* 底部批次欄 */
.pl-batch-bar {
    position: fixed; bottom: 0; left: 0; right: 0; z-index: 500;
    display: none; align-items: center; justify-content: space-between;
    padding: 14px 3rem;
    background: #1a1a1a; color: #fff;
    box-shadow: 0 -2px 16px rgba(0,0,0,0.15);
}
.pl-batch-bar.is-open { display: flex; }
.pl-batch-count { font-size: 14px; font-weight: 500; }
.pl-batch-cart-btn {
    height: 40px; padding: 0 28px;
    background: #E8572A; border: none; border-radius: 20px;
    color: #fff; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
}
.pl-batch-cart-btn:hover { background: #d14a1f; }

/* 雜項 */
.product-source { font-size: 11px; color: #999; margin: 0 0 4px; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pl-pagination { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 2rem; flex-wrap: wrap; }
.pl-page-btn {
    min-width: 36px; height: 36px; padding: 0 10px;
    border: 1.5px solid #ddd; border-radius: 50%;
    background: transparent; font-size: 14px; color: #555;
    text-decoration: none; display: inline-flex; align-items: center; justify-content: center;
    transition: all 0.2s;
}
.pl-page-btn:hover:not(.disabled):not(.active) { border-color: #E8572A; color: #E8572A; }
.pl-page-btn.active { background: #E8572A; border-color: #E8572A; color: #fff; font-weight: 600; }
.pl-page-btn.disabled { opacity: 0.35; pointer-events: none; }
.pl-page-ellipsis { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; color: #bbb; }
</style>

<script>
(function () {
    var id            = '{{ $listId }}';
    var featuredEl    = document.getElementById(id + '-featured');
    var restEl        = document.getElementById(id + '-rest');
    var btnPrev       = document.getElementById(id + '-feat-prev');
    var btnNext       = document.getElementById(id + '-feat-next');
    var normalActions = document.getElementById(id + '-normal-actions');
    var batchControls = document.getElementById(id + '-batch-controls');
    var batchBtn      = document.getElementById(id + '-batch-btn');
    var cancelBtn     = document.getElementById(id + '-cancel-btn');
    var selectAllBtn  = document.getElementById(id + '-select-all-btn');
    var batchBar      = document.getElementById(id + '-batch-bar');
    var batchCount    = document.getElementById(id + '-batch-count');
    var batchCartBtn  = document.getElementById(id + '-batch-cart-btn');

    var FEAT_SIZE   = {{ $featuredPageSize }};
    var batchMode   = false;
    var selectedIds = [];

    function getAllCards() {
        var cards = [];
        if (featuredEl) cards = cards.concat(Array.from(featuredEl.querySelectorAll('.product-card')));
        if (restEl)     cards = cards.concat(Array.from(restEl.querySelectorAll('.product-card')));
        return cards;
    }

    function updateBatchUI() {
        if (batchCount) batchCount.textContent = '已選 ' + selectedIds.length + ' 個項目';
        var allCards = getAllCards();
        var isAll = allCards.length > 0 && selectedIds.length === allCards.length;
        if (selectAllBtn) {
            selectAllBtn.textContent = isAll ? '✕ 取消全選' : '✓ 全選';
            selectAllBtn.classList.toggle('all-selected', isAll);
        }
    }

    function enterBatch() {
        batchMode = true;
        selectedIds = [];
        if (normalActions) normalActions.style.display = 'none';
        if (batchControls) batchControls.style.display = 'flex';
        if (batchBar)      batchBar.classList.add('is-open');
        getAllCards().forEach(function (card) {
            var chk  = card.querySelector('.pl-check');
            var icon = card.querySelector('.pl-check-icon');
            if (chk)  { chk.style.display = 'flex'; chk.classList.remove('checked'); }
            if (icon) icon.style.display = 'none';
            card.classList.remove('is-selected');
        });
        updateBatchUI();
    }

    function exitBatch() {
        batchMode = false;
        selectedIds = [];
        if (normalActions) normalActions.style.display = '';
        if (batchControls) batchControls.style.display = 'none';
        if (batchBar)      batchBar.classList.remove('is-open');
        getAllCards().forEach(function (card) {
            var chk  = card.querySelector('.pl-check');
            var icon = card.querySelector('.pl-check-icon');
            if (chk)  { chk.style.display = 'none'; chk.classList.remove('checked'); }
            if (icon) icon.style.display = 'none';
            card.classList.remove('is-selected');
        });
    }

    function toggleSelectAll() {
        var cards = getAllCards();
        var isAll = selectedIds.length === cards.length && cards.length > 0;
        selectedIds = [];
        cards.forEach(function (card) {
            var chk  = card.querySelector('.pl-check');
            var icon = card.querySelector('.pl-check-icon');
            if (isAll) {
                card.classList.remove('is-selected');
                if (chk)  chk.classList.remove('checked');
                if (icon) icon.style.display = 'none';
            } else {
                selectedIds.push(card.dataset.id);
                card.classList.add('is-selected');
                if (chk)  chk.classList.add('checked');
                if (icon) icon.style.display = 'block';
            }
        });
        updateBatchUI();
    }

    function handleCardClick(card) {
        if (!batchMode) return;
        var cardId = card.dataset.id;
        var idx    = selectedIds.indexOf(cardId);
        var chk    = card.querySelector('.pl-check');
        var icon   = card.querySelector('.pl-check-icon');
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
        updateBatchUI();
    }

    if (batchBtn)     batchBtn.addEventListener('click', enterBatch);
    if (cancelBtn)    cancelBtn.addEventListener('click', exitBatch);
    if (selectAllBtn) selectAllBtn.addEventListener('click', toggleSelectAll);

    if (batchCartBtn) {
        batchCartBtn.addEventListener('click', function () {
            if (selectedIds.length === 0) return;
            // TODO: 串接購物車 API
            console.log('批次加入購物車', selectedIds);
        });
    }

    getAllCards().forEach(function (card) {
        card.addEventListener('click', function (e) {
            if (e.target.closest('.product-cart-icon')) return;
            handleCardClick(card);
        });
    });

    // ── 精選翻頁 ──────────────────────────────────────────────────────
    if (featuredEl && btnPrev && btnNext) {
        var featCards = Array.from(featuredEl.querySelectorAll('.product-card'));
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
