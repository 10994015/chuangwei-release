{{-- resources/views/components/product-list-section.blade.php --}}
@php
    // ── 呼叫 /api/product/temple/donation ─────────────────────
    $currentPage  = max(1, (int) request('page', 1));
    $apiKeyword   = trim(request('name', ''));
    $apiSortCombo = request('sort', '');
    [$apiSortBy, $apiSortOrder] = array_pad(explode('|', $apiSortCombo, 2), 2, '');

    $donationRaw  = [];
    $totalPages   = 1;
    $total        = 0;

    try {
        $apiBase     = rtrim(config('app.api_base_url', env('API_BASE_URL', '')), '/');
        $donationRes = \Illuminate\Support\Facades\Http::withOptions(['cookies' => false])
            ->withHeaders(['Cookie' => request()->header('Cookie', '')])
            ->get($apiBase . '/api/product/temple', array_filter([
                'page'       => $currentPage,
                'pageSize'   => 10,
                'name'       => $apiKeyword   ?: null,
                'sortBy'     => $apiSortBy    ?: null,
                'sortOrder'  => $apiSortOrder ?: null,
            ], fn($v) => $v !== null));

        if ($donationRes->status() === 200) {
            $resData    = $donationRes->json('data') ?? [];
            $donationRaw = $resData['data']       ?? [];
            $totalPages  = (int)($resData['totalPages'] ?? 1);
            $total       = (int)($resData['total']      ?? 0);
            \Illuminate\Support\Facades\Log::debug('[product-list-section] API response', [
                'status'     => $donationRes->status(),
                'totalPages' => $totalPages,
                'total'      => $total,
                'data'       => $donationRaw,
            ]);
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('[product-list-section] donation API error: ' . $e->getMessage());
    }
    // ─────────────────────────────────────────────────────────

    $data         = $frame['data'] ?? [];

    // 精選：暫無 API，留空（有 API 後再串）
    $featuredList = [];

    // 商品列表：使用 API 資料
    $productList = array_map(function($item) {
        // 價格：優先用頂層 price，沒有再取 skus 最低價
        if (isset($item['price'])) {
            $price = 'NT$ ' . number_format((float)$item['price']);
        } else {
            $skus = $item['skus'] ?? [];
            $minPrice = null;
            foreach ($skus as $sku) {
                $p = isset($sku['price']) ? (float)$sku['price'] : null;
                if ($p !== null && ($minPrice === null || $p < $minPrice)) {
                    $minPrice = $p;
                }
            }
            $price = $minPrice !== null ? 'NT$ ' . number_format($minPrice) : '';
        }
        $image = !empty($item['imgs']) ? ($item['imgs'][0]['url'] ?? null) : null;

        return [
            'id'         => $item['id']         ?? null,
            'title'      => $item['nameZhTw']    ?? ($item['name'] ?? ''),
            'tenantName' => $item['tenantName']  ?? '',   // API 目前無此欄位
            'price'      => $price,
            'image'      => $image,
            'status'     => $item['status']      ?? '',
        ];
    }, $donationRaw);

    // 分頁頁碼
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
        ['label' => '預設排序',  'value' => ''],
        ['label' => '最新上架',  'value' => 'publishAt|DESC'],
        ['label' => '價格低到高', 'value' => 'price|ASC'],
        ['label' => '價格高到低', 'value' => 'price|DESC'],
    ];
    $device = $device ?? 'desktop';
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
                        placeholder="搜尋捐款商品名稱" class="search-input" />
                    <button class="search-btn" type="submit">搜尋</button>
                    @if($apiKeyword || $apiSortCombo)
                        <a href="{{ url()->current() }}?locale={{ request('locale','ZH-TW') }}" class="reset-btn">重置</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- 精選（frame data，有才顯示）--}}
        @if(!empty($featuredList))
            <h2 class="section-title">精選推薦</h2>
            <div class="products-grid products-grid--featured">
                @foreach($featuredList as $product)
                    <div class="product-card">
                        <div class="product-image">
                            @if($product['rank'])
                                <div class="rank-badge">NO.{{ $product['rank'] }}</div>
                            @endif
                            @if($product['image'])
                                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="image" />
                            @else
                                <div class="image-placeholder"><span>商品圖片</span></div>
                            @endif
                        </div>
                        <div class="product-info">
                            @if($product['badge'])
                                <span class="product-badge {{ $product['badgeClass'] }}">{{ $product['badge'] }}</span>
                            @else
                                <div class="badge-placeholder"></div>
                            @endif
                            <h3 class="product-title">{{ $product['title'] }}</h3>
                            <div class="product-footer">
                                <span class="product-price">{{ $product['price'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 商品列表 --}}
        @if(count($productList) > 0)
            <div class="products-grid products-grid--rest">
                @foreach($productList as $product)
                    <a class="product-card" href="/product/temple/{{ $product['id'] }}?locale={{ request('locale','ZH-TW') }}&from={{ request()->segment(1) ?: 'home' }}">
                        <div class="product-image">
                            @if($product['image'])
                                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="image" loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
                                <div class="image-placeholder" style="display:none"><span>暫無圖片</span></div>
                            @else
                                <div class="image-placeholder"><span>暫無圖片</span></div>
                            @endif
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

<style>
.product-list-section a.product-card,
.product-list-section a.product-card:hover,
.product-list-section a.product-card:visited {
    text-decoration: none !important;
    color: inherit;
}
.reset-btn {
    padding: 8px 16px; border: 1.5px solid #ddd; border-radius: 4px;
    background: transparent; color: #666; font-size: 13px;
    text-decoration: none; white-space: nowrap; transition: all 0.2s;
}
.reset-btn:hover { border-color: #E8572A; color: #E8572A; }
.invoice-badge {
    position: absolute; top: 8px; left: 8px;
    padding: 3px 8px; background: #16a34a; color: #fff;
    font-size: 11px; font-weight: 600; border-radius: 4px;
}
.product-source {
    font-size: 11px; color: #999; margin: 0 0 4px; line-height: 1.4;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.product-desc {
    font-size: 12px; color: #888; margin: 4px 0 6px;
    line-height: 1.5; display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
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
