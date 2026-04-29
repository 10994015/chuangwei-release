{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('ui.navbarBasemap.loginTitle') }}</title>
  @vite(['resources/css/app.css', 'resources/css/components.css', 'resources/js/app.js'])
</head>
<body>
<x-login-modal modalId="login-page-modal" />

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-header">
      <img src="/images/icon.png" alt="Logo" class="auth-logo" onerror="this.style.display='none'" />
      <h1 class="auth-card-title">{{ __('ui.navbarBasemap.loginTitle') }}</h1>
    </div>

    {{-- Google 登入 --}}
    <button class="auth-btn-google" id="login-page-google-btn">
      <svg viewBox="0 0 24 24" width="18" height="18">
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
      </svg>
      {{ __('ui.navbarBasemap.googleLogin') }}
    </button>

    <div class="auth-divider"><span>{{ __('ui.navbarBasemap.orWithAccount') }}</span></div>

    {{-- 帳密登入 --}}
    <form id="login-page-form" novalidate>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.navbarBasemap.emailLabel') }}</label>
        <input id="login-page-email" type="text" class="auth-input" placeholder="{{ __('ui.navbarBasemap.emailPlaceholder') }}" autocomplete="username" />
      </div>

      <div class="auth-field">
        <div class="auth-label-row">
          <label class="auth-label">{{ __('ui.navbarBasemap.passwordLabel') }}</label>
          <a href="/forgot-password?locale={{ request('locale','ZH-TW') }}" class="auth-forgot">{{ __('ui.navbarBasemap.forgotPassword') }}</a>
        </div>
        <input id="login-page-password" type="password" class="auth-input" placeholder="{{ __('ui.navbarBasemap.passwordPlaceholder') }}" autocomplete="current-password" />
      </div>

      <div class="auth-error" id="login-page-error" style="display:none">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="login-page-error-text"></span>
      </div>

      <button type="submit" class="auth-btn-submit" id="login-page-submit">{{ __('ui.navbarBasemap.loginBtn') }}</button>
    </form>

    <p class="auth-switch">
      {{ __('ui.navbarBasemap.noAccount') }}
      <a href="/register?locale={{ request('locale','ZH-TW') }}" class="auth-switch-link">{{ __('ui.navbarBasemap.registerNow') }}</a>
    </p>

  </div>
</div>

<style>
.auth-page {
  min-height: 100vh; background: #f3f4f6;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Noto Sans TC', sans-serif; padding: 24px;
}
.auth-card {
  background: #fff; border-radius: 16px; padding: 40px 36px;
  width: 100%; max-width: 420px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}
.auth-card-header { display: flex; flex-direction: column; align-items: center; gap: 12px; margin-bottom: 28px; }
.auth-logo { height: 48px; object-fit: contain; }
.auth-card-title { font-size: 22px; font-weight: 700; color: #1f2937; margin: 0; }
.auth-btn-google {
  width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;
  padding: 11px; border: 1px solid #d1d5db; border-radius: 8px;
  background: #fff; font-size: 14px; font-weight: 500; color: #374151;
  cursor: pointer; transition: background 0.15s; margin-bottom: 16px;
}
.auth-btn-google:hover:not(:disabled) { background: #f9fafb; }
.auth-btn-google:disabled { opacity: 0.6; cursor: not-allowed; }
.auth-divider {
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 16px; color: #9ca3af; font-size: 13px;
}
.auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }
.auth-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.auth-label { font-size: 13px; font-weight: 500; color: #374151; }
.auth-label-row { display: flex; justify-content: space-between; align-items: center; }
.auth-label-row .auth-label { margin-bottom: 0; }
.auth-forgot { font-size: 13px; color: #6b7280; text-decoration: none; }
.auth-forgot:hover { color: #E8572A; text-decoration: underline; }
.auth-input {
  width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px;
  font-size: 14px; color: #374151; box-sizing: border-box; outline: none; transition: border-color 0.2s;
}
.auth-input:focus { border-color: #E8572A; box-shadow: 0 0 0 3px rgba(232,87,42,0.1); }
.auth-error {
  display: flex; align-items: center; gap: 8px; padding: 10px 12px;
  background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;
  color: #dc2626; font-size: 13px; margin-bottom: 14px;
}
.auth-error svg { width: 16px; height: 16px; flex-shrink: 0; }
.auth-btn-submit {
  width: 100%; padding: 12px; border: none; border-radius: 8px;
  background: #E8572A; color: #fff; font-size: 15px; font-weight: 600;
  cursor: pointer; transition: background 0.15s; margin-bottom: 16px; margin-top: 4px;
}
.auth-btn-submit:hover:not(:disabled) { background: #d04a20; }
.auth-btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.auth-switch { text-align: center; font-size: 13px; color: #6b7280; margin: 0; }
.auth-switch-link { color: #E8572A; font-weight: 500; text-decoration: none; }
.auth-switch-link:hover { text-decoration: underline; }
@media (max-width: 480px) {
  .auth-card { padding: 28px 20px; }
}
</style>

<script>
(function () {
  var PROXY_BASE = '';
  var REDIRECT   = '{{ request("redirect", "/home?locale=" . request("locale","ZH-TW")) }}';
  var LOCALE     = '{{ request("locale", "ZH-TW") }}';

  var form      = document.getElementById('login-page-form');
  var emailEl   = document.getElementById('login-page-email');
  var passwordEl= document.getElementById('login-page-password');
  var submitBtn = document.getElementById('login-page-submit');
  var errorBox  = document.getElementById('login-page-error');
  var errorText = document.getElementById('login-page-error-text');
  var googleBtn = document.getElementById('login-page-google-btn');

  function showError(msg) {
    errorText.textContent = msg;
    errorBox.style.display = 'flex';
  }
  function hideError() { errorBox.style.display = 'none'; }

  async function sha256(msg) {
    var buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(msg));
    return Array.from(new Uint8Array(buf)).map(function(b){ return b.toString(16).padStart(2,'0'); }).join('');
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      hideError();
      var credential = emailEl ? emailEl.value.trim() : '';
      var password   = passwordEl ? passwordEl.value : '';
      if (!credential) { showError('{{ __("ui.navbarBasemap.errNoAccount") }}'); return; }
      if (!password)   { showError('{{ __("ui.navbarBasemap.errNoPassword") }}'); return; }

      submitBtn.disabled    = true;
      submitBtn.textContent = '{{ __("ui.navbarBasemap.loggingIn") }}';

      try {
        var hashedPw = await sha256(password);
        var res = await fetch(PROXY_BASE + '/proxy/api/login', {
          method: 'POST', credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ credential: credential, password: hashedPw })
        });
        var json = await res.json();

        if (res.status === 200) {
          window.location.href = REDIRECT;
        } else if (res.status === 202) {
          var d = json.data || {};
          alert(d.firstLogin ? '首次登入需重設密碼！' : '密碼已過期，請重新設定密碼！');
          if (d.changePwToken) window.location.href = '/init-password/' + d.changePwToken;
        } else {
          var errMsg = json.message || '登入失敗，請檢查帳號或密碼';
          if (res.status === 407) errMsg = json.message || '帳號未審核';
          showError(errMsg);
          submitBtn.disabled    = false;
          submitBtn.textContent = '{{ __("ui.navbarBasemap.loginBtn") }}';
        }
      } catch (err) {
        console.error(err);
        showError('網路連線錯誤，請檢查網路狀態');
        submitBtn.disabled    = false;
        submitBtn.textContent = '{{ __("ui.navbarBasemap.loginBtn") }}';
      }
    });
  }

  // Google 登入：透過 login-modal 的 Google 機制
  var modalApi = window['loginModal_login-page-modal'];
  if (googleBtn) {
    googleBtn.addEventListener('click', function () {
      var api = window['loginModal_login-page-modal'];
      if (api) api.open();
    });
  }

  // 若已登入直接跳轉
  fetch(PROXY_BASE + '/proxy/api/frontend/user', { credentials: 'include' })
    .then(function(r){ if (r.status === 200) window.location.href = REDIRECT; })
    .catch(function(){});
})();
</script>
</body>
</html>
