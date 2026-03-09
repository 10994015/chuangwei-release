{{-- resources/views/components/product-list-section.blade.php --}}
@php
    $data            = $frame['data'] ?? [];

    // ── 精選商品（featuredProduct）────────────────────────────────
    $featuredRaw     = $data['featuredProduct'] ?? [];
    $featuredList    = array_map(fn($item) => [
        'id'         => $item['id']     ?? null,
        'rank'       => $item['no']     ?? null,
        'title'      => $item['name']   ?? '',
        'price'      => 'NT$' . number_format($item['price'] ?? 0),
        'image'      => $item['imgSrc'] ?? null,
        'badge'      => !empty($item['labels']) ? $item['labels'][0] : null,
        'badgeClass' => 'hot',
    ], $featuredRaw);

    // ── 一般商品清單（productList.data）──────────────────────────
    $productRaw      = $data['productList']['data'] ?? [];
    $productList     = array_map(fn($item) => [
        'id'         => $item['id']     ?? null,
        'rank'       => null,
        'title'      => $item['name']   ?? '',
        'price'      => 'NT$' . number_format($item['price'] ?? 0),
        'image'      => $item['imgSrc'] ?? null,
        'badge'      => !empty($item['labels']) ? $item['labels'][0] : null,
        'badgeClass' => '',
    ], $productRaw);

    // ── 合併：精選在前，一般在後 ─────────────────────────────────
    $allProducts     = array_merge($featuredList, $productList);

    $festivalOptions = [];
    $typeOptions     = [];
    $categoryOptions = [];
    $sortOptions     = [
        ['label' => '價格低到高', 'value' => 'price_asc'],
        ['label' => '價格高到低', 'value' => 'price_desc'],
    ];
    $device          = $device ?? 'desktop';
@endphp

<section
    class="product-list-section device-{{ $device }}"
    x-data="{
        keyword: '',
        products: {{ json_encode(array_values($allProducts)) }},
        get filtered() {
            if (!this.keyword.trim()) return this.products
            const kw = this.keyword.trim().toLowerCase()
            return this.products.filter(p => p.title.toLowerCase().includes(kw))
        }
    }"
>
    <div class="container">

        {{-- 篩選欄 --}}
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">慶典活動</label>
                <select class="filter-select wide" name="festival">
                    <option value="">全部</option>
                    @foreach ($festivalOptions as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">類型</label>
                <select class="filter-select narrow" name="type">
                    <option value="">全部</option>
                    @foreach ($typeOptions as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">需求分類</label>
                <select class="filter-select mid" name="category">
                    <option value="">全部</option>
                    @foreach ($categoryOptions as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">排序方式</label>
                <select class="filter-select mid" name="sort">
                    @foreach ($sortOptions as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group search-group">
                <label class="filter-label">關鍵字搜尋</label>
                <div class="search-box">
                    <input
                        type="text"
                        placeholder="搜尋商品或服務名稱"
                        class="search-input"
                        x-model="keyword"
                        @keydown.enter.prevent
                    />
                    <button class="search-btn" type="button">搜尋</button>
                </div>
            </div>
        </div>

        {{-- 批次選擇 --}}
        <div class="batch-actions">
            <button class="batch-select-btn" type="button">批次選擇</button>
        </div>

        {{-- 標題 --}}
        <h2 class="section-title">精選推薦</h2>

        {{-- 商品 Grid --}}
        <div class="products-grid">
            <template x-for="product in filtered" :key="product.id">
                <div class="product-card">

                    {{-- 圖片區 --}}
                    <div class="product-image">
                        <template x-if="product.rank">
                            <div class="rank-badge" x-text="'NO.' + product.rank"></div>
                        </template>
                        <template x-if="product.image">
                            <img :src="product.image" :alt="product.title" class="image" />
                        </template>
                        <div x-show="!product.image" class="image-placeholder">
                            <span>商品圖片</span>
                        </div>
                    </div>

                    {{-- 資訊區 --}}
                    <div class="product-info">
                        <template x-if="product.badge">
                            <span class="product-badge" :class="product.badgeClass" x-text="product.badge"></span>
                        </template>
                        <div x-show="!product.badge" class="badge-placeholder"></div>

                        <h3 class="product-title" x-text="product.title"></h3>

                        <div class="product-footer">
                            <span class="product-price" x-text="product.price"></span>
                            <button class="add-to-cart-btn" type="button" @click.stop>
                                <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                    <line x1="3" y1="6" x2="21" y2="6"/>
                                    <path d="M16 10a4 4 0 01-8 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
            </template>

            {{-- 關鍵字無結果 --}}
            <div
                class="empty-state"
                x-show="filtered.length === 0"
                style="display:none; grid-column: 1 / -1;"
            >
                <p>找不到符合條件的商品</p>
            </div>
        </div>

    </div>
</section>
