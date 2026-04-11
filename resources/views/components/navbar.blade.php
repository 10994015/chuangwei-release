{{-- resources/views/components/navbar.blade.php --}}
@php
    $data       = $frame['data'] ?? [];
    $logoSrc    = $data['logoImgSrc'] ?? null;
    $tenantName = $data['tenantName'] ?? 'LOGO';
    $tabs       = $data['tabs']       ?? [];
    $navModalId = 'navbar-modal-' . uniqid();
@endphp

<x-login-modal :modalId="$navModalId" />

<header
    class="navbar"
    x-data="{ mobileMenuOpen: false }"
    @click.outside="mobileMenuOpen = false"
>
    <div class="navbar-container">

        {{-- Logo --}}
        <div class="logo-wrapper">
            <a href="/home" class="logo">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" class="logo-image" />
                @endif
                <span class="logo-name">{{ $tenantName }}</span>
            </a>
        </div>

        {{-- 桌機導航 --}}
        <nav class="nav-menu">
            @foreach ($tabs as $tab)
                @php $tabSlug = $tab['slug'] ?? ''; @endphp
                <a
                    href="/{{ $tabSlug }}"
                    class="nav-item {{ ($slug ?? '') === $tabSlug ? 'active' : '' }}"
                >
                    {{ $tab['name'] }}
                </a>
            @endforeach
        </nav>

        {{-- 右側按鈕 --}}
        <div class="nav-actions">
            <button class="cart-btn">🛒</button>
            {{-- 未登入 --}}
            <button class="login-btn" id="{{ $navModalId }}-nav-login-btn">
              {{ __('ui.navbarBasemap.login') }}
            </button>
            {{-- 已登入 --}}
            <div class="user-wrapper" id="{{ $navModalId }}-nav-user-wrapper" style="display:none">
              <span class="user-name" id="{{ $navModalId }}-nav-user-name"></span>
              <button class="logout-btn" id="{{ $navModalId }}-nav-logout-btn">
                {{ __('ui.navbarBasemap.logout') }}
              </button>
            </div>
        </div>

        {{-- 漢堡按鈕（手機） --}}
        <button
            class="hamburger-btn"
            :class="{ 'is-open': mobileMenuOpen }"
            @click.stop="mobileMenuOpen = !mobileMenuOpen"
            aria-label="{{ __('ui.navbarBasemap.openMenu') }}"
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
        x-transition:enter="mobile-menu-enter-active"
        x-transition:enter-start="mobile-menu-enter-from"
        x-transition:enter-end=""
        x-transition:leave="mobile-menu-leave-active"
        x-transition:leave-start=""
        x-transition:leave-end="mobile-menu-leave-to"
        @click.stop
    >
        <nav class="mobile-nav">
            @foreach ($tabs as $tab)
                @php $tabSlug = $tab['slug'] ?? ''; @endphp
                <a
                    href="/{{ $tabSlug }}"
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
            <button class="mobile-cart-btn">{{ __('ui.navbarBasemap.cart') }}</button>
            {{-- 未登入 --}}
            <button class="mobile-login-btn" id="{{ $navModalId }}-nav-mobile-login-btn">
              {{ __('ui.navbarBasemap.login') }}
            </button>
            {{-- 已登入 --}}
            <button class="mobile-login-btn" id="{{ $navModalId }}-nav-mobile-logout-btn" style="display:none;background:#f5f5f5;color:#444;border:1.5px solid #ddd;">
              <span id="{{ $navModalId }}-nav-mobile-user-name"></span>&nbsp;·&nbsp;{{ __('ui.navbarBasemap.logout') }}
            </button>
        </div>
    </div>

    {{-- 遮罩 --}}
    <div
        class="menu-overlay"
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="overlay-fade-enter-active"
        x-transition:enter-start="overlay-fade-enter-from"
        x-transition:enter-end=""
        x-transition:leave="overlay-fade-leave-active"
        x-transition:leave-start=""
        x-transition:leave-end="overlay-fade-leave-to"
        @click="mobileMenuOpen = false"
    ></div>
</header>

<script>
(function () {
  var MODAL_ID   = '{{ $navModalId }}';
  var PROXY_BASE = '';

  var loginBtn         = document.getElementById(MODAL_ID + '-nav-login-btn');
  var userWrapper      = document.getElementById(MODAL_ID + '-nav-user-wrapper');
  var userNameEl       = document.getElementById(MODAL_ID + '-nav-user-name');
  var logoutBtn        = document.getElementById(MODAL_ID + '-nav-logout-btn');
  var mobileLoginBtn   = document.getElementById(MODAL_ID + '-nav-mobile-login-btn');
  var mobileLogoutBtn  = document.getElementById(MODAL_ID + '-nav-mobile-logout-btn');
  var mobileUserNameEl = document.getElementById(MODAL_ID + '-nav-mobile-user-name');

  function setLoggedIn(name) {
    if (loginBtn)         loginBtn.style.display        = 'none';
    if (userWrapper)      userWrapper.style.display      = '';
    if (userNameEl)       userNameEl.textContent         = name;
    if (mobileLoginBtn)   mobileLoginBtn.style.display   = 'none';
    if (mobileLogoutBtn)  mobileLogoutBtn.style.display  = '';
    if (mobileUserNameEl) mobileUserNameEl.textContent   = name;
  }

  function setLoggedOut() {
    if (loginBtn)        loginBtn.style.display        = '';
    if (userWrapper)     userWrapper.style.display      = 'none';
    if (mobileLoginBtn)  mobileLoginBtn.style.display   = '';
    if (mobileLogoutBtn) mobileLogoutBtn.style.display  = 'none';
  }

  // 把 setLoggedIn / setLoggedOut 注入 modal，讓登入成功後能更新 navbar
  var api = window['loginModal_' + MODAL_ID];
  if (api) {
    api.setLoggedIn  = setLoggedIn;
    api.setLoggedOut = setLoggedOut;
  }

  // 開啟彈窗
  function openModal() {
    if (api) api.open();
  }

  if (loginBtn)       loginBtn.addEventListener('click', openModal);
  if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', openModal);

  // 登出
  async function doLogout() {
    try {
      await fetch(PROXY_BASE + '/proxy/api/login/out', { method: 'POST', credentials: 'include' });
    } catch (e) {}
    if (api) api.clearAuth();
    setLoggedOut();
  }

  if (logoutBtn)       logoutBtn.addEventListener('click', doLogout);
  if (mobileLogoutBtn) mobileLogoutBtn.addEventListener('click', doLogout);
})();
</script>
