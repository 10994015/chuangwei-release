{{-- resources/views/components/products-section.blade.php --}}
@php
    $data     = $frame['data'] ?? [];
    $rawList  = $data['products'] ?? [];

    $products = array_map(fn($item) => [
        'id'    => $item['id']     ?? null,
        'rank'  => $item['no']     ?? null,
        'title' => $item['name']   ?? '',
        'price' => 'NT$' . number_format($item['price'] ?? 0),
        'image' => $item['imgSrc'] ?? null,
        'badge' => !empty($item['labels']) ? $item['labels'][0] : null,
    ], $rawList);

    $displayProducts = array_slice($products, 0, 3);

    $device     = $device ?? 'desktop';
    $templeId   = $templeId ?? '';
    $viewAllUrl = $templeId ? "/site/{$templeId}/products" : '#';
@endphp

<section class="products-section">
    <div class="container">

        {{-- 標題列 --}}
        <div class="section-header">
            <h2 class="section-title">{{ __('ui.productsBasemap.title') }}</h2>
        </div>

        {{-- 商品 Grid — 固定 3 筆 --}}
        <div class="products-grid">
            @forelse ($displayProducts as $product)
                <div class="product-card">

                    {{-- 圖片區 --}}
                    <div class="product-image">
                        @if (!empty($product['rank']))
                            <div class="rank-badge">NO.{{ $product['rank'] }}</div>
                        @endif
                        @if (!empty($product['image']))
                            <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="image" />
                        @else
                            <div class="image-placeholder">
                                <span>{{ __('ui.productsBasemap.imagePlaceholder') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- 資訊區 --}}
                    <div class="product-info">
                        @if (!empty($product['badge']))
                            <span class="product-badge hot">{{ $product['badge'] }}</span>
                        @else
                            <div class="badge-placeholder"></div>
                        @endif

                        <h3 class="product-title">{{ $product['title'] }}</h3>

                        <div class="product-footer">
                            <span class="product-price">{{ $product['price'] }}</span>
                            <button class="add-to-cart-btn" type="button">
                                <svg class="cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                    <line x1="3" y1="6" x2="21" y2="6"/>
                                    <path d="M16 10a4 4 0 01-8 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
            @empty
                <div class="empty-state">
                    <p>{{ __('ui.productsBasemap.empty') }}</p>
                </div>
            @endforelse
        </div>

        {{-- 查看更多 --}}
        @if (!empty($displayProducts))
            <div class="view-more-wrap">
                <a href="{{ $viewAllUrl }}" class="view-more-btn">{{ __('ui.productsBasemap.viewMore') }}</a>
            </div>
        @endif

    </div>
</section>
