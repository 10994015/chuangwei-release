{{-- resources/views/components/login-modal.blade.php --}}
<script src="https://accounts.google.com/gsi/client" async defer></script>
@props(['modalId'])

<div class="pv-login-modal-overlay" id="{{ $modalId }}-overlay" style="display:none">
  <div class="pv-login-modal" role="dialog" aria-modal="true">
    <button class="pv-lm-close" id="{{ $modalId }}-close" aria-label="關閉">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
    </button>

    <h2 class="pv-lm-title">{{ __('ui.navbarBasemap.loginTitle') }}</h2>

    <button class="pv-lm-google-btn" id="{{ $modalId }}-google">
      <svg class="pv-lm-google-icon" viewBox="0 0 24 24">
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
      </svg>
      {{ __('ui.navbarBasemap.googleLogin') }}
    </button>

    <div class="pv-lm-divider"><span>{{ __('ui.navbarBasemap.orWithAccount') }}</span></div>

    <form id="{{ $modalId }}-form" novalidate>
      <div class="pv-lm-field">
        <label class="pv-lm-label" for="{{ $modalId }}-email">{{ __('ui.navbarBasemap.emailLabel') }}</label>
        <input id="{{ $modalId }}-email" class="pv-lm-input" type="text" placeholder="{{ __('ui.navbarBasemap.emailPlaceholder') }}" autocomplete="username" />
      </div>

      <div class="pv-lm-field">
        <div class="pv-lm-label-row">
          <label class="pv-lm-label" for="{{ $modalId }}-password">{{ __('ui.navbarBasemap.passwordLabel') }}</label>
          <button type="button" class="pv-lm-forgot" id="{{ $modalId }}-forgot">{{ __('ui.navbarBasemap.forgotPassword') }}</button>
        </div>
        <input id="{{ $modalId }}-password" class="pv-lm-input" type="password" placeholder="{{ __('ui.navbarBasemap.passwordPlaceholder') }}" autocomplete="current-password" />
      </div>

      <div class="pv-lm-error" id="{{ $modalId }}-error" style="display:none">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="{{ $modalId }}-error-text"></span>
      </div>

      <label class="pv-lm-remember">
        <input type="checkbox" id="{{ $modalId }}-remember" class="pv-lm-checkbox" />
        <span>{{ __('ui.navbarBasemap.rememberMe') }}</span>
      </label>

      <button type="submit" class="pv-lm-submit" id="{{ $modalId }}-submit">
        {{ __('ui.navbarBasemap.loginBtn') }}
      </button>
    </form>

    <div class="pv-lm-register">
      <span>{{ __('ui.navbarBasemap.noAccount') }}</span>
      <button type="button" class="pv-lm-register-btn" id="{{ $modalId }}-register">{{ __('ui.navbarBasemap.registerNow') }}</button>
    </div>
  </div>
</div>

<style>
.pv-login-modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 2000; padding: 20px;
}
.pv-login-modal {
  background: #fff; border-radius: 12px;
  width: 100%; max-width: 440px; max-height: 90vh; overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
  position: relative; padding: 32px 32px 28px;
}
.pv-lm-close {
  position: absolute; top: 14px; right: 14px;
  width: 30px; height: 30px; border: none; background: none;
  cursor: pointer; color: #9ca3af; display: flex; align-items: center; justify-content: center;
  border-radius: 4px; transition: color 0.2s;
}
.pv-lm-close:hover { color: #374151; }
.pv-lm-close svg { width: 18px; height: 18px; }
.pv-lm-title { font-size: 22px; font-weight: 700; color: #111; margin: 0 0 22px; }
.pv-lm-google-btn {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  width: 100%; padding: 11px; border: 1px solid #e5e7eb; border-radius: 8px;
  background: #fff; cursor: pointer; font-size: 14px; font-weight: 500; color: #374151;
  transition: background 0.2s; margin-bottom: 16px;
}
.pv-lm-google-btn:hover:not(:disabled) { background: #f9fafb; }
.pv-lm-google-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.pv-lm-google-icon { width: 20px; height: 20px; flex-shrink: 0; }
.pv-lm-divider {
  display: flex; align-items: center; color: #9ca3af;
  font-size: 13px; margin-bottom: 16px;
}
.pv-lm-divider::before, .pv-lm-divider::after { content: ''; flex: 1; border-bottom: 1px solid #e5e7eb; }
.pv-lm-divider span { padding: 0 12px; }
.pv-lm-field { margin-bottom: 16px; }
.pv-lm-label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
.pv-lm-label-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.pv-lm-label-row .pv-lm-label { margin-bottom: 0; }
.pv-lm-forgot { background: none; border: none; color: #E8572A; font-size: 13px; cursor: pointer; padding: 0; }
.pv-lm-forgot:hover { text-decoration: underline; }
.pv-lm-input {
  width: 100%; padding: 10px 14px;
  border: 1px solid #d1d5db; border-radius: 8px;
  font-size: 14px; color: #374151; box-sizing: border-box;
  outline: none; transition: border-color 0.2s;
}
.pv-lm-input:focus { border-color: #E8572A; box-shadow: 0 0 0 3px rgba(232,87,42,0.1); }
.pv-lm-input::placeholder { color: #9ca3af; }
.pv-lm-error {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 12px; background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 8px; color: #dc2626; font-size: 13px; margin-bottom: 14px;
}
.pv-lm-error svg { width: 18px; height: 18px; flex-shrink: 0; }
.pv-lm-remember {
  display: flex; align-items: center; gap: 8px;
  font-size: 14px; color: #374151; cursor: pointer; margin-bottom: 18px;
}
.pv-lm-checkbox { width: 15px; height: 15px; cursor: pointer; accent-color: #E8572A; }
.pv-lm-submit {
  width: 100%; padding: 12px; border: none; border-radius: 8px;
  background: #E8572A; color: #fff; font-size: 15px; font-weight: 600;
  cursor: pointer; transition: background 0.2s; margin-bottom: 16px;
}
.pv-lm-submit:hover:not(:disabled) { background: #d14a1f; }
.pv-lm-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.pv-lm-register { text-align: center; font-size: 14px; color: #6b7280; }
.pv-lm-register-btn {
  background: none; border: none; color: #E8572A; font-weight: 500;
  cursor: pointer; padding: 0; font-size: 14px;
}
.pv-lm-register-btn:hover { text-decoration: underline; }
@media (max-width: 768px) {
  .pv-login-modal { padding: 24px 20px 20px; }
}
</style>

<script>
(function () {
  var MODAL_ID         = '{{ $modalId }}';
  var PROXY_BASE       = '';
  var GOOGLE_CLIENT_ID = '{{ env("GOOGLE_CLIENT_ID") }}';

  var GOOGLE_ICON_HTML = '<svg class="pv-lm-google-icon" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg> {{ __("ui.navbarBasemap.googleLogin") }}';

  var modalOverlay = document.getElementById(MODAL_ID + '-overlay');
  var lmClose      = document.getElementById(MODAL_ID + '-close');
  var lmForm       = document.getElementById(MODAL_ID + '-form');
  var lmEmail      = document.getElementById(MODAL_ID + '-email');
  var lmPassword   = document.getElementById(MODAL_ID + '-password');
  var lmSubmit     = document.getElementById(MODAL_ID + '-submit');
  var lmError      = document.getElementById(MODAL_ID + '-error');
  var lmErrorText  = document.getElementById(MODAL_ID + '-error-text');
  var lmRemember   = document.getElementById(MODAL_ID + '-remember');
  var lmGoogleBtn  = document.getElementById(MODAL_ID + '-google');
  var lmForgot     = document.getElementById(MODAL_ID + '-forgot');
  var lmRegister   = document.getElementById(MODAL_ID + '-register');

  var hiddenGoogleDiv = null;

  // ── 對外暴露方法，讓 navbar 可以呼叫 ─────────────────────
  window['loginModal_' + MODAL_ID] = {
    open: openModal,
    close: closeModal,
    clearAuth: function () {
      var api = window['loginModal_' + MODAL_ID];
      if (api && api.setLoggedOut) api.setLoggedOut();
    },
    setLoggedIn: null,
    setLoggedOut: null,
  };

  // ── Modal open / close ────────────────────────────────────
  function openModal() {
    lmError    && (lmError.style.display = 'none');
    lmEmail    && (lmEmail.value         = '');
    lmPassword && (lmPassword.value      = '');
    lmSubmit   && (lmSubmit.disabled     = false);
    lmSubmit   && (lmSubmit.textContent  = '{{ __("ui.navbarBasemap.loginBtn") }}');
    modalOverlay && (modalOverlay.style.display = 'flex');
  }

  function closeModal() {
    modalOverlay && (modalOverlay.style.display = 'none');
  }

  function showError(msg) {
    if (lmError && lmErrorText) {
      lmErrorText.textContent = msg;
      lmError.style.display = 'flex';
    }
  }

  if (lmClose) lmClose.addEventListener('click', closeModal);
  if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
      if (e.target === modalOverlay) closeModal();
    });
  }

  // ── SHA-256 ───────────────────────────────────────────────
  async function sha256(message) {
    var buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(message));
    return Array.from(new Uint8Array(buf)).map(function (b) { return b.toString(16).padStart(2, '0'); }).join('');
  }

  // ── 頁面載入時從 API 確認登入狀態 ────────────────────────
  async function fetchCurrentUser() {
    try {
      var res = await fetch(PROXY_BASE + '/proxy/api/frontend/user', {
        credentials: 'include'
      });
      if (res.status === 200) {
        var json = await res.json();
        var name = json.data && json.data.name ? json.data.name : '';
        if (name) {
          var api = window['loginModal_' + MODAL_ID];
          if (api && api.setLoggedIn) api.setLoggedIn(name);
        }
      }
      // 401 = not logged in, do nothing
    } catch (e) {
      // network error, do nothing
    }
  }

  // defer so navbar has time to wire up setLoggedIn/setLoggedOut callbacks first
  function runInit() {
    setTimeout(function () {
      fetchCurrentUser();
      initGoogleSignIn();
    }, 0);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runInit);
  } else {
    runInit();
  }

  // ── Login submit ──────────────────────────────────────────
  if (lmForm) {
    lmForm.addEventListener('submit', async function (e) {
      e.preventDefault();
      lmError && (lmError.style.display = 'none');

      var credential = lmEmail    ? lmEmail.value.trim() : '';
      var password   = lmPassword ? lmPassword.value     : '';

      if (!credential) { showError('{{ __("ui.navbarBasemap.errNoAccount") }}'); return; }
      if (!password)   { showError('{{ __("ui.navbarBasemap.errNoPassword") }}'); return; }

      lmSubmit.disabled    = true;
      lmSubmit.textContent = '{{ __("ui.navbarBasemap.loggingIn") }}';

      try {
        var hashedPw = await sha256(password);

        var res = await fetch(PROXY_BASE + '/proxy/api/login', {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ credential: credential, password: hashedPw })
        });

        var json = await res.json();

        if (res.status === 200) {
          var data = json.data || {};
          var name = data.name || credential;
          var api  = window['loginModal_' + MODAL_ID];
          if (api && api.setLoggedIn) api.setLoggedIn(name);
          closeModal();

        } else if (res.status === 202) {
          var d   = json.data || {};
          var msg = d.firstLogin ? '首次登入需重設密碼！' : '密碼已過期，請重新設定密碼！';
          alert(msg);
          closeModal();
          if (d.changePwToken) window.location.href = '/init-password/' + d.changePwToken;

        } else {
          var errMsg = json.message || '登入失敗，請檢查帳號或密碼';
          if (res.status === 407) errMsg = json.message || '帳號未審核';
          showError(errMsg);
          lmSubmit.disabled    = false;
          lmSubmit.textContent = '{{ __("ui.navbarBasemap.loginBtn") }}';
        }
      } catch (err) {
        console.error('[login] caught error:', err);
        var errMsg = (err && err.name === 'TypeError') ? '網路連線錯誤，請檢查網路狀態' : (err && err.message ? err.message : '發生錯誤，請稍後再試');
        showError(errMsg);
        lmSubmit.disabled    = false;
        lmSubmit.textContent = '{{ __("ui.navbarBasemap.loginBtn") }}';
      }
    });
  }

  // ── Google login ──────────────────────────────────────────
  function handleGoogleCredential(response) {
    if (!response.credential) {
      showError('Google 登入失敗，請稍後再試');
      return;
    }

    lmGoogleBtn.disabled = true;
    lmGoogleBtn.textContent = '登入中...';
    lmError && (lmError.style.display = 'none');

    fetch(PROXY_BASE + '/proxy/api/login/google', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ token: response.credential })
    })
    .then(function (res) {
      return res.json().then(function (json) { return { status: res.status, json: json }; });
    })
    .then(function (result) {
      var status = result.status;
      var json   = result.json;

      if (status === 200) {
        var data = json.data || {};
        var name = data.name || 'Google 用戶';
        var api  = window['loginModal_' + MODAL_ID];
        if (api && api.setLoggedIn) api.setLoggedIn(name);
        closeModal();

      } else if (status === 202) {
        var d = json.data || {};
        alert(d.firstLogin ? '首次登入需重設密碼！' : '密碼已過期，請重新設定密碼！');
        closeModal();
        if (d.changePwToken) window.location.href = '/init-password/' + d.changePwToken;

      } else {
        var errMsg = json.message || 'Google 登入失敗，請稍後再試';
        if (status === 407) errMsg = json.message || '帳號未審核';
        showError(errMsg);
      }
    })
    .catch(function () {
      showError('網路連線錯誤，請檢查網路狀態');
    })
    .finally(function () {
      lmGoogleBtn.disabled = false;
      lmGoogleBtn.innerHTML = GOOGLE_ICON_HTML;
    });
  }

  function initGoogleSignIn() {
    if (!window.google || !GOOGLE_CLIENT_ID) return;

    google.accounts.id.initialize({
      client_id: GOOGLE_CLIENT_ID,
      callback: handleGoogleCredential,
    });

    hiddenGoogleDiv = document.createElement('div');
    hiddenGoogleDiv.style.cssText = 'position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;';
    document.body.appendChild(hiddenGoogleDiv);

    google.accounts.id.renderButton(hiddenGoogleDiv, {
      type: 'standard',
      size: 'large',
    });
  }

  if (lmGoogleBtn) {
    lmGoogleBtn.addEventListener('click', function () {
      if (!window.google) {
        showError('Google 服務載入失敗，請重新整理頁面');
        return;
      }
      if (!hiddenGoogleDiv) {
        showError('Google 服務尚未完成載入，請稍後再試');
        return;
      }
      var googleBtn = hiddenGoogleDiv.querySelector('div[role="button"]');
      if (googleBtn) {
        googleBtn.click();
      } else {
        showError('Google 服務載入失敗，請重新整理頁面');
      }
    });
  }

  if (lmForgot)   lmForgot.addEventListener('click',   function () { closeModal(); alert('忘記密碼功能建置中'); });
  if (lmRegister) lmRegister.addEventListener('click', function () { closeModal(); alert('註冊功能建置中'); });
})();
</script>
