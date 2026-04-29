{{-- resources/views/components/pv-navbar.blade.php --}}
@php
  $data = $frame['data'] ?? [];

  $logoSrc    = $data['logoImgSrc'] ?? null;
  $tenantName = $data['tenantName'] ?? ($data['temple_name'] ?? 'LOGO');
  $tabs       = $data['tabs']       ?? ($data['tab'] ?? []);

  $locale      = request()->query('locale', 'ZH-TW');
  $currentSlug = $slug ?? 'home';

  $navbarId   = 'pv-navbar-' . uniqid();
  $navModalId = 'pv-modal-' . uniqid();

  if (empty($locales)) {
    $locales = [
      ['locale' => 'ZH-TW', 'label' => '繁體中文'],
      ['locale' => 'ZH-CN', 'label' => '简体中文'],
      ['locale' => 'EN-US', 'label' => 'English'],
    ];
  }

  // Server-side auth check — avoid flash of unauthenticated state
  $ssrUserName = null;
  $token = request()->cookie('token');
  if ($token) {
    try {
      $apiBase  = rtrim(config('app.api_base_url', env('API_BASE_URL', '')), '/');
      $res = \Illuminate\Support\Facades\Http::withOptions(['cookies' => false])
        ->withHeaders(['Cookie' => 'token=' . $token])
        ->get($apiBase . '/api/frontend/user/');
      if ($res->status() === 200) {
        $ssrUserName = $res->json('data.name');
      }
    } catch (\Throwable $e) {}
  }
@endphp

<x-login-modal :modalId="$navModalId" />

<header class="pv-navbar" id="{{ $navbarId }}">
  <div class="pv-navbar-container">

    {{-- Logo --}}
    <a href="/home?locale={{ $locale }}" class="pv-logo-wrapper">
      <div class="pv-logo">
        @if($logoSrc)
          <img src="{{ $logoSrc }}" alt="Logo" class="pv-logo-image" />
        @else
          <span class="pv-logo-icon">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
              <rect width="28" height="28" rx="6" fill="#E8572A"/>
              <text x="14" y="20" text-anchor="middle" font-size="14" fill="#fff" font-weight="bold">宮</text>
            </svg>
          </span>
        @endif
        <span class="pv-logo-name">{{ $tenantName }}</span>
      </div>
    </a>

    {{-- 桌機導航 --}}
    <nav class="pv-nav-menu pv-desktop-only">
      @foreach($tabs as $tab)
        @php
          $tabSlug  = $tab['slug'] ?? '';
          $tabName  = $tab['name'] ?? '';
          $tabHref  = "/{$tabSlug}?locale={$locale}";
          $isActive = $tabSlug === $currentSlug;
        @endphp
        @if($tabSlug !== 'portal')
          <a href="{{ $tabHref }}" class="pv-nav-item{{ $isActive ? ' active' : '' }}">
            {{ $tabName }}
          </a>
        @endif
      @endforeach
    </nav>

    {{-- 桌機右側 --}}
    <div class="pv-nav-actions pv-desktop-only">
      {{-- 未登入 --}}
      <button class="pv-login-btn" id="{{ $navbarId }}-login-btn" style="{{ $ssrUserName ? 'display:none' : '' }}">{{ __('ui.navbarBasemap.loginRegister') }}</button>
      {{-- 已登入 --}}
      <div class="pv-user-wrapper" id="{{ $navbarId }}-user-wrapper" style="{{ $ssrUserName ? '' : 'display:none' }}">
        <button class="pv-user-btn" id="{{ $navbarId }}-user-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <span class="pv-user-name" id="{{ $navbarId }}-user-name">{{ $ssrUserName ?? '' }}</span>
          <svg class="pv-chevron" id="{{ $navbarId }}-user-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="pv-user-dropdown" id="{{ $navbarId }}-user-dropdown">
          <button class="pv-user-option pv-user-logout" id="{{ $navbarId }}-logout-btn">{{ __('ui.navbarBasemap.logout') }}</button>
        </div>
      </div>

      {{-- 語言切換 --}}
      <div class="pv-locale-wrapper" id="{{ $navbarId }}-locale">
        <button class="pv-locale-btn" id="{{ $navbarId }}-locale-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="2" y1="12" x2="22" y2="12"/>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
          <span class="pv-locale-label" id="{{ $navbarId }}-locale-label">
            @foreach($locales as $loc)
              @if($loc['locale'] === $locale){{ $loc['label'] }}@endif
            @endforeach
          </span>
          <svg class="pv-chevron" id="{{ $navbarId }}-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
          </svg>
        </button>

        <div class="pv-locale-dropdown" id="{{ $navbarId }}-locale-dropdown">
          @foreach($locales as $loc)
            <a
              href="?locale={{ $loc['locale'] }}"
              class="pv-locale-option{{ $loc['locale'] === $locale ? ' active' : '' }}"
            >{{ $loc['label'] }}</a>
          @endforeach
        </div>
      </div>
    </div>

    {{-- 漢堡按鈕 --}}
    <button class="pv-hamburger-btn pv-mobile-only" id="{{ $navbarId }}-hamburger" aria-label="{{ __('ui.navbarBasemap.openMenu') }}">
      <span class="pv-hamburger-line"></span>
      <span class="pv-hamburger-line"></span>
      <span class="pv-hamburger-line"></span>
    </button>

  </div>

  {{-- 行動版選單 --}}
  <div class="pv-mobile-menu pv-mobile-only" id="{{ $navbarId }}-menu">
    <nav class="pv-mobile-nav">
      @foreach($tabs as $tab)
        @php
          $tabSlug  = $tab['slug'] ?? '';
          $tabName  = $tab['name'] ?? '';
          $tabHref  = "/{$tabSlug}?locale={$locale}";
          $isActive = $tabSlug === $currentSlug;
        @endphp
        @if($tabSlug !== 'portal')
          <a href="{{ $tabHref }}" class="pv-mobile-nav-item{{ $isActive ? ' active' : '' }}">
            <span>{{ $tabName }}</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </a>
        @endif
      @endforeach
    </nav>
    <div class="pv-mobile-actions">
      <button class="pv-mobile-login-btn" id="{{ $navbarId }}-mobile-login-btn" style="{{ $ssrUserName ? 'display:none' : '' }}">{{ __('ui.navbarBasemap.loginRegister') }}</button>
      <button class="pv-mobile-login-btn pv-mobile-logout-btn" id="{{ $navbarId }}-mobile-logout-btn" style="{{ $ssrUserName ? '' : 'display:none' }};background:#f5f5f5;color:#444;border:1.5px solid #ddd;">
        <span id="{{ $navbarId }}-mobile-user-name">{{ $ssrUserName ?? '' }}</span>&nbsp;·&nbsp;{{ __('ui.navbarBasemap.logout') }}
      </button>
      <div class="pv-mobile-locale">
        @foreach($locales as $loc)
          <a
            href="?locale={{ $loc['locale'] }}"
            class="pv-mobile-locale-btn{{ $loc['locale'] === $locale ? ' active' : '' }}"
          >{{ $loc['label'] }}</a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="pv-menu-overlay" id="{{ $navbarId }}-overlay"></div>
</header>
<div style="height:64px"></div>

<style>
.pv-navbar {
  background: #fff;
  border-bottom: 1px solid #eee;
  padding: 0 2rem;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 200;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.pv-navbar-container {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  position: relative;
}
.pv-logo-wrapper {
  display: flex;
  align-items: center;
  text-decoration: none;
  flex-shrink: 0;
  padding: 4px;
  border-radius: 4px;
  border: 2px solid transparent;
  transition: border-color 0.2s;
}
.pv-logo { display: flex; align-items: center; gap: 8px; min-width: 80px; min-height: 40px; }
.pv-logo-image { max-width: 140px; max-height: 40px; object-fit: contain; flex-shrink: 0; }
.pv-logo-icon  { flex-shrink: 0; display: flex; align-items: center; }
.pv-logo-name  { font-size: 16px; font-weight: 700; color: #222; white-space: nowrap; letter-spacing: 0.5px; }
.pv-nav-menu   { display: flex; gap: 0.25rem; flex: 1; justify-content: center; }
.pv-nav-item {
  color: #444;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: color 0.2s;
}
.pv-nav-item:hover  { color: #E8572A; }
.pv-nav-item.active { color: #E8572A; font-weight: 600; }
.pv-nav-actions { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.pv-login-btn {
  padding: 7px 20px;
  border: 1.5px solid #E8572A;
  border-radius: 20px;
  background: transparent;
  color: #E8572A;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s;
}
.pv-login-btn:hover { background: #E8572A; color: #fff; }
.pv-locale-wrapper { position: relative; }
.pv-locale-btn {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 7px 12px;
  border: 1.5px solid #ddd;
  border-radius: 20px;
  background: transparent;
  color: #555;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.pv-locale-btn:hover { border-color: #E8572A; color: #E8572A; }
.pv-locale-label { font-size: 13px; }
.pv-chevron { transition: transform 0.2s; }
.pv-chevron.open { transform: rotate(180deg); }
.pv-locale-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 100px;
  background: #fff;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  overflow: hidden;
  z-index: 300;
  opacity: 0;
  transform: translateY(-6px);
  pointer-events: none;
  transition: opacity 0.18s ease, transform 0.18s ease;
}
.pv-locale-dropdown.open {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}
.pv-locale-option {
  display: block;
  width: 100%;
  padding: 10px 16px;
  background: transparent;
  border: none;
  text-align: left;
  font-size: 13px;
  color: #444;
  cursor: pointer;
  transition: background 0.15s;
  text-decoration: none;
}
.pv-locale-option:hover  { background: #fff5f2; color: #E8572A; }
.pv-locale-option.active { color: #E8572A; font-weight: 600; background: #fff5f2; }
.pv-hamburger-btn {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 5px;
  width: 40px;
  height: 40px;
  padding: 8px;
  background: none;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}
.pv-hamburger-btn:hover { background: #f5f5f5; }
.pv-hamburger-btn.is-open .pv-hamburger-line:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.pv-hamburger-btn.is-open .pv-hamburger-line:nth-child(2) { opacity: 0; transform: scaleX(0); }
.pv-hamburger-btn.is-open .pv-hamburger-line:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.pv-hamburger-line {
  display: block;
  width: 22px;
  height: 2px;
  background: #333;
  border-radius: 2px;
  transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
  transform-origin: center;
}
.pv-mobile-menu {
  position: absolute;
  top: 64px;
  left: 0;
  right: 0;
  background: #fff;
  border-top: 1px solid #f0f0f0;
  border-bottom: 1px solid #e5e5e5;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  z-index: 300;
  max-height: 0;
  overflow: hidden;
  opacity: 0;
  transform: translateY(-8px);
  transition: max-height 0.28s cubic-bezier(0.4,0,0.2,1),
              opacity    0.22s cubic-bezier(0.4,0,0.2,1),
              transform  0.25s cubic-bezier(0.4,0,0.2,1);
}
.pv-mobile-menu.is-open {
  max-height: 600px;
  opacity: 1;
  transform: translateY(0);
}
.pv-mobile-nav { padding: 8px 0; }
.pv-mobile-nav-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 24px;
  color: #444;
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  border-left: 3px solid transparent;
  transition: all 0.18s;
}
.pv-mobile-nav-item:hover  { color: #E8572A; background: #fff8f6; border-left-color: #E8572A; }
.pv-mobile-nav-item.active { color: #E8572A; background: #fff8f6; border-left-color: #E8572A; font-weight: 600; }
.pv-mobile-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 14px 24px 20px;
  border-top: 1px solid #f0f0f0;
}
.pv-mobile-login-btn {
  width: 100%;
  padding: 10px 16px;
  background: #E8572A;
  border: none;
  border-radius: 8px;
  color: #fff;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.pv-mobile-login-btn:hover { background: #d14a1f; }
.pv-mobile-locale { display: flex; gap: 8px; flex-wrap: wrap; }
.pv-mobile-locale-btn {
  padding: 7px 14px;
  border: 1.5px solid #ddd;
  border-radius: 16px;
  background: transparent;
  color: #555;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}
.pv-mobile-locale-btn:hover  { border-color: #E8572A; color: #E8572A; }
.pv-mobile-locale-btn.active { border-color: #E8572A; color: #E8572A; background: #fff5f2; font-weight: 600; }
.pv-menu-overlay {
  position: fixed;
  inset: 0;
  top: 64px;
  background: rgba(0,0,0,0.2);
  z-index: 150;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
}
.pv-menu-overlay.is-open { opacity: 1; pointer-events: auto; }
.pv-user-wrapper { position: relative; }
.pv-user-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px; border: 1.5px solid #E8572A; border-radius: 20px;
  background: transparent; color: #E8572A; font-size: 14px; font-weight: 500;
  cursor: pointer; white-space: nowrap; transition: all 0.2s;
}
.pv-user-btn:hover { background: #fff5f2; }
.pv-user-dropdown {
  position: absolute; top: calc(100% + 8px); right: 0;
  min-width: 120px; background: #fff;
  border: 1px solid #e5e5e5; border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1); overflow: hidden;
  z-index: 300; opacity: 0; transform: translateY(-6px);
  pointer-events: none; transition: opacity 0.18s, transform 0.18s;
}
.pv-user-dropdown.open { opacity: 1; transform: translateY(0); pointer-events: auto; }
.pv-user-option {
  display: block; width: 100%; padding: 10px 16px;
  background: transparent; border: none; text-align: left;
  font-size: 13px; color: #444; cursor: pointer; transition: background 0.15s;
}
.pv-user-option:hover { background: #fff5f2; color: #E8572A; }
.pv-user-logout { color: #dc2626; }
.pv-user-logout:hover { background: #fef2f2; color: #dc2626; }
.pv-desktop-only { display: flex; }
.pv-mobile-only  { display: none; }
@media (max-width: 768px) {
  .pv-navbar { padding: 0 1rem; }
  .pv-desktop-only { display: none !important; }
  .pv-mobile-only  { display: flex !important; }
}
</style>

<script>
(function () {
  var id         = '{{ $navbarId }}';
  var MODAL_ID   = '{{ $navModalId }}';
  var PROXY_BASE = '';

  var hamburger        = document.getElementById(id + '-hamburger');
  var menu             = document.getElementById(id + '-menu');
  var overlay          = document.getElementById(id + '-overlay');
  var localeBtn        = document.getElementById(id + '-locale-btn');
  var localeDd         = document.getElementById(id + '-locale-dropdown');
  var chevron          = document.getElementById(id + '-chevron');

  var loginBtn         = document.getElementById(id + '-login-btn');
  var mobileLoginBtn   = document.getElementById(id + '-mobile-login-btn');
  var userWrapper      = document.getElementById(id + '-user-wrapper');
  var userBtn          = document.getElementById(id + '-user-btn');
  var userNameEl       = document.getElementById(id + '-user-name');
  var userDd           = document.getElementById(id + '-user-dropdown');
  var userChevron      = document.getElementById(id + '-user-chevron');
  var logoutBtn        = document.getElementById(id + '-logout-btn');
  var mobileLogoutBtn  = document.getElementById(id + '-mobile-logout-btn');
  var mobileUserNameEl = document.getElementById(id + '-mobile-user-name');

  // ── Hamburger ─────────────────────────────────────────────
  if (hamburger && menu) {
    function openMenu() { menu.classList.add('is-open'); overlay.classList.add('is-open'); hamburger.classList.add('is-open'); }
    function closeMenu() { menu.classList.remove('is-open'); overlay.classList.remove('is-open'); hamburger.classList.remove('is-open'); }
    hamburger.addEventListener('click', function (e) { e.stopPropagation(); menu.classList.contains('is-open') ? closeMenu() : openMenu(); });
    overlay.addEventListener('click', closeMenu);
  }

  // ── Locale dropdown ───────────────────────────────────────
  if (localeBtn && localeDd) {
    localeBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = localeDd.classList.toggle('open');
      chevron && chevron.classList.toggle('open', open);
    });
  }

  // ── Login / Logout state ──────────────────────────────────
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

  // 把 setLoggedIn / setLoggedOut 注入 modal
  var api = window['loginModal_' + MODAL_ID];
  if (api) {
    api.setLoggedIn  = setLoggedIn;
    api.setLoggedOut = setLoggedOut;
  }

  // 前往登入頁
  var LOCALE = '{{ $locale }}';
  function goToLogin() {
    window.location.href = '/login?locale=' + LOCALE + '&redirect=' + encodeURIComponent(window.location.href);
  }

  if (loginBtn)       loginBtn.addEventListener('click', goToLogin);
  if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', goToLogin);

  // ── User dropdown ─────────────────────────────────────────
  if (userBtn && userDd) {
    userBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = userDd.classList.toggle('open');
      userChevron && userChevron.classList.toggle('open', open);
    });
  }

  // ── Logout ────────────────────────────────────────────────
  async function doLogout() {
    try {
      await fetch(PROXY_BASE + '/proxy/api/login/out', { method: 'POST', credentials: 'include' });
    } catch (e) {}
    if (api) api.clearAuth();
    setLoggedOut();
    if (userDd) userDd.classList.remove('open');
  }

  if (logoutBtn)       logoutBtn.addEventListener('click', doLogout);
  if (mobileLogoutBtn) mobileLogoutBtn.addEventListener('click', doLogout);

  // ── Close dropdowns on outside click ─────────────────────
  document.addEventListener('click', function () {
    if (localeDd) { localeDd.classList.remove('open'); chevron && chevron.classList.remove('open'); }
    if (userDd)   { userDd.classList.remove('open'); userChevron && userChevron.classList.remove('open'); }
  });
})();
</script>
