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
            <div class="nav-user-wrapper" id="{{ $navModalId }}-nav-user-wrapper" style="display:none">
              <button class="nav-user-btn" id="{{ $navModalId }}-user-menu-btn">
                <span class="nav-user-name" id="{{ $navModalId }}-nav-user-name"></span>
                <svg class="nav-user-chevron" id="{{ $navModalId }}-user-chevron"
                     width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"/>
                </svg>
              </button>
              <div class="nav-user-dropdown" id="{{ $navModalId }}-user-dropdown">
                <div class="nav-user-dropdown-header">
                  <div class="nav-user-dropdown-name" id="{{ $navModalId }}-dropdown-name">王小明</div>
                </div>
                <button class="nav-user-dropdown-item nav-user-dropdown-danger" id="{{ $navModalId }}-nav-logout-btn">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                       stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                  </svg>
                  {{ __('ui.navbarBasemap.logout') }}
                </button>
              </div>
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

<style>
/* ── 已登入 user widget ────────────────────────────── */
.nav-user-wrapper { position: relative; }

.nav-user-btn {
  display: flex; align-items: center; gap: 8px;
  background: transparent; border: none; cursor: pointer;
  padding: 5px 10px; border-radius: 8px;
  transition: background 0.18s;
}
.nav-user-btn:hover { background: #f5f5f5; }

.nav-user-name {
  font-size: 14px; font-weight: 500; color: #333;
  max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

.nav-user-chevron {
  color: #888; transition: transform 0.2s; flex-shrink: 0;
}
.nav-user-chevron.open { transform: rotate(180deg); }

/* dropdown */
.nav-user-dropdown {
  position: absolute; top: calc(100% + 6px); right: 0;
  min-width: 180px; background: #fff;
  border: 1px solid #e5e7eb; border-radius: 10px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  overflow: hidden; z-index: 300;
  opacity: 0; transform: translateY(-6px); pointer-events: none;
  transition: opacity 0.15s, transform 0.15s;
}
.nav-user-dropdown.open {
  opacity: 1; transform: translateY(0); pointer-events: auto;
}

.nav-user-dropdown-header {
  padding: 12px 16px 10px;
  border-bottom: 1px solid #f3f4f6;
}
.nav-user-dropdown-name {
  font-size: 14px; font-weight: 600; color: #111;
  max-width: 148px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}

.nav-user-dropdown-item {
  display: flex; align-items: center; gap: 10px;
  width: 100%; padding: 10px 16px;
  background: transparent; border: none; cursor: pointer;
  font-size: 14px; color: #374151; text-align: left;
  transition: background 0.12s; text-decoration: none;
}
.nav-user-dropdown-item:hover { background: #f9fafb; }
.nav-user-dropdown-danger:hover { background: #fef2f2 !important; color: #dc2626 !important; }
</style>

<script>
(function () {
  var MODAL_ID   = '{{ $navModalId }}';
  var PROXY_BASE = '';

  var loginBtn         = document.getElementById(MODAL_ID + '-nav-login-btn');
  var userWrapper      = document.getElementById(MODAL_ID + '-nav-user-wrapper');
  var userNameEl       = document.getElementById(MODAL_ID + '-nav-user-name');
  var userMenuBtn      = document.getElementById(MODAL_ID + '-user-menu-btn');
  var userDropdown     = document.getElementById(MODAL_ID + '-user-dropdown');
  var userChevron      = document.getElementById(MODAL_ID + '-user-chevron');
  var dropdownName     = document.getElementById(MODAL_ID + '-dropdown-name');
  var logoutBtn        = document.getElementById(MODAL_ID + '-nav-logout-btn');
  var mobileLoginBtn   = document.getElementById(MODAL_ID + '-nav-mobile-login-btn');
  var mobileLogoutBtn  = document.getElementById(MODAL_ID + '-nav-mobile-logout-btn');
  var mobileUserNameEl = document.getElementById(MODAL_ID + '-nav-mobile-user-name');

  // ── dropdown toggle ──────────────────────────────────────────
  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = userDropdown.classList.toggle('open');
      userChevron && userChevron.classList.toggle('open', open);
    });
    document.addEventListener('click', function () {
      userDropdown.classList.remove('open');
      userChevron && userChevron.classList.remove('open');
    });
  }

  function setLoggedIn(name) {
    if (loginBtn)         loginBtn.style.display    = 'none';
    if (userWrapper)      userWrapper.style.display  = '';
    if (userNameEl)       userNameEl.textContent     = name;
    if (dropdownName)     dropdownName.textContent   = name;
    if (mobileLoginBtn)   mobileLoginBtn.style.display  = 'none';
    if (mobileLogoutBtn)  mobileLogoutBtn.style.display = '';
    if (mobileUserNameEl) mobileUserNameEl.textContent  = name;
  }

  function setLoggedOut() {
    if (loginBtn)        loginBtn.style.display    = '';
    if (userWrapper)     userWrapper.style.display  = 'none';
    if (mobileLoginBtn)  mobileLoginBtn.style.display  = '';
    if (mobileLogoutBtn) mobileLogoutBtn.style.display = 'none';
  }

  // 把 setLoggedIn / setLoggedOut 注入 modal，讓登入成功後能更新 navbar
  var api = window['loginModal_' + MODAL_ID];
  if (api) {
    api.setLoggedIn  = setLoggedIn;
    api.setLoggedOut = setLoggedOut;
  }

  // 跳到登入頁（manage.父網域）
  function goToLogin() {
    var locale   = new URLSearchParams(window.location.search).get('locale') || 'ZH-TW';
    var host     = window.location.hostname; // e.g. abc.angkeinfo.com / angkeinfo.com
    var parts    = host.split('.');
    // 有子網域（parts >= 3，或 2 段且第二段是 localhost）→ 取父網域
    var parent;
    if (parts.length >= 3) {
      parent = parts.slice(1).join('.');        // abc.angkeinfo.com → angkeinfo.com
    } else if (parts.length === 2 && parts[1] === 'localhost') {
      parent = 'localhost';                     // abc.localhost → localhost
    } else {
      parent = host;                            // angkeinfo.com → angkeinfo.com
    }
    var port       = window.location.port ? ':' + window.location.port : '';
    var manageHost = 'manage.' + parent + port;
    var protocol   = window.location.protocol;
    var loginUrl   = protocol + '//' + manageHost + '/login?locale=' + locale + '&redirect=' + encodeURIComponent(window.location.href);
    window.location.href = loginUrl;
  }

  // if (loginBtn)       loginBtn.addEventListener('click', goToLogin);
  // if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', goToLogin);

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
