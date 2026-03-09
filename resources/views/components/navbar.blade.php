{{-- resources/views/components/navbar.blade.php --}}
@php
    $data        = $frame['data'] ?? [];
    $logoSrc     = $data['logoImgSrc']  ?? null;
    $tenantName  = $data['tenantName']  ?? 'LOGO';
    $tabs        = $data['tabs']        ?? [];

    // tabs 的 url 要組合，JSON 只有 slug，要拼成完整路徑
    // 取 templeId（由 page.blade.php 傳入）
    $tid = $templeId ?? '';
@endphp

<header
    class="navbar"
    x-data="{ mobileMenuOpen: false }"
    @click.outside="mobileMenuOpen = false"
>
    <div class="navbar-container">

        {{-- Logo --}}
        <div class="logo-wrapper">
            <a href="/site/{{ $tid }}" class="logo">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" class="logo-image" />
                @endif
                <span class="logo-name">{{ $tenantName }}</span>
            </a>
        </div>

        {{-- 桌機導航 --}}
        <nav class="nav-menu">
            @foreach ($tabs as $tab)
                @php
                    $tabSlug = $tab['slug'] ?? '';
                    $tabUrl  = $tid ? "/site/{$tid}/{$tabSlug}" : '#';
                @endphp
                <a
                    href="{{ $tabUrl }}"
                    class="nav-item {{ ($slug ?? '') === $tabSlug ? 'active' : '' }}"
                >
                    {{ $tab['name'] }}
                </a>
            @endforeach
        </nav>

        {{-- 右側按鈕 --}}
        <div class="nav-actions">
            <button class="cart-btn">🛒</button>
            <button class="login-btn">會員登入</button>
        </div>

        {{-- 漢堡按鈕（手機） --}}
        <button
            class="hamburger-btn"
            :class="{ 'is-open': mobileMenuOpen }"
            @click.stop="mobileMenuOpen = !mobileMenuOpen"
            aria-label="開啟選單"
        >
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>

    {{-- 行動版下拉選單 --}}
    <div
        class="mobile-menu"
        x-show="mobileMenuOpen"
        x-cloak
        @click.stop
    >
        <nav class="mobile-nav">
            @foreach ($tabs as $tab)
                @php
                    $tabSlug = $tab['slug'] ?? '';
                    $tabUrl  = $tid ? "/site/{$tid}/{$tabSlug}" : '#';
                @endphp

                <a
                    href="{{ $tabUrl }}"
                    class="mobile-nav-item {{ ($slug ?? '') === $tabSlug ? 'active' : '' }}"
                    @click="mobileMenuOpen = false"
                >
                    <span class="mobile-nav-text">{{ $tab['name'] }}</span>
                    <svg class="mobile-nav-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            @endforeach
        </nav>

        <div class="mobile-actions">
            <button class="mobile-cart-btn">🛒 購物車</button>
            <button class="mobile-login-btn">會員登入</button>
        </div>
    </div>

    {{-- 遮罩 --}}
    <div
        class="menu-overlay"
        x-show="mobileMenuOpen"
        x-cloak
        @click="mobileMenuOpen = false"
    ></div>
</header>
