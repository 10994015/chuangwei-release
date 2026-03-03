{{-- resources/views/components/products-section.blade.php --}}
@props([
    'productsList' => [
        ['id' => 1, 'rank' => 1, 'title' => '平安符',     'price' => 'NT$200',   'image' => null, 'badge' => '熱門', 'badgeClass' => 'hot'],
        ['id' => 2, 'rank' => 2, 'title' => '個人光明燈', 'price' => 'NT$600',   'image' => null, 'badge' => '推薦', 'badgeClass' => 'recommended'],
        ['id' => 3, 'rank' => 3, 'title' => '全家光明燈', 'price' => 'NT$1,200', 'image' => null],
        ['id' => 4,              'title' => '平安米',     'price' => 'NT$150',   'image' => null, 'badge' => '新品', 'badgeClass' => 'new'],
        ['id' => 5,              'title' => '香油錢',     'price' => 'NT$500',   'image' => null],
        ['id' => 6,              'title' => '祈福手環',   'price' => 'NT$350',   'image' => null, 'badge' => '熱門', 'badgeClass' => 'hot'],
    ],
    'viewAllUrl' => '#',
    'device'     => 'desktop',
])

@php
    $displayProducts = array_slice((array) $productsList, 0, 3);
@endphp

<section class="products-section device-{{ $device }}">
    <div class="container">

        {{-- 標題列 --}}
        <div class="section-header">
            <h2 class="section-title">祈福商品</h2>
        </div>

        {{-- 商品 Grid — 固定 3 筆 --}}
        <div class="products-grid">
            @foreach ($displayProducts as $product)
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
                                <span>商品圖片</span>
                            </div>
                        @endif
                    </div>

                    {{-- 資訊區 --}}
                    <div class="product-info">
                        @if (!empty($product['badge']))
                            <span class="product-badge {{ $product['badgeClass'] ?? '' }}">{{ $product['badge'] }}</span>
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
            @endforeach
        </div>

        {{-- 查看更多 --}}
        <div class="view-more-wrap">
            <a href="{{ $viewAllUrl }}" class="view-more-btn">查看更多商品</a>
        </div>

    </div>
</section>
