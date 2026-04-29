{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('ui.navbarBasemap.registerNow') }}</title>
  @vite(['resources/css/app.css', 'resources/css/components.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-header">
      <img src="/images/icon.png" alt="Logo" class="auth-logo" onerror="this.style.display='none'" />
      <h1 class="auth-card-title">{{ __('ui.registerPage.title') }}</h1>
    </div>

    <form id="register-form" novalidate>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.registerPage.labelCredential') }}</label>
        <input id="reg-credential" type="text" class="auth-input" placeholder="{{ __('ui.registerPage.placeholderCredential') }}" autocomplete="username" />
      </div>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.registerPage.labelName') }}</label>
        <input id="reg-name" type="text" class="auth-input" placeholder="{{ __('ui.registerPage.placeholderName') }}" />
      </div>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.registerPage.labelEmail') }}</label>
        <input id="reg-email" type="email" class="auth-input" placeholder="{{ __('ui.registerPage.placeholderEmail') }}" autocomplete="email" />
      </div>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.registerPage.labelPassword') }}</label>
        <input id="reg-password" type="password" class="auth-input" placeholder="{{ __('ui.registerPage.placeholderPassword') }}" autocomplete="new-password" />
      </div>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.registerPage.labelConfirmPassword') }}</label>
        <input id="reg-confirm" type="password" class="auth-input" placeholder="{{ __('ui.registerPage.placeholderConfirmPassword') }}" autocomplete="new-password" />
      </div>

      <div class="auth-error" id="reg-error" style="display:none">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="reg-error-text"></span>
      </div>
      <div class="auth-success" id="reg-success" style="display:none">
        <span id="reg-success-text"></span>
      </div>

      <button type="submit" class="auth-btn-submit" id="reg-submit">{{ __('ui.registerPage.submitBtn') }}</button>
    </form>

    <p class="auth-switch">
      {{ __('ui.registerPage.hasAccount') }}
      <a href="/login?locale={{ request('locale','ZH-TW') }}" class="auth-switch-link">{{ __('ui.registerPage.backToLogin') }}</a>
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
.auth-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.auth-label { font-size: 13px; font-weight: 500; color: #374151; }
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
.auth-success {
  padding: 10px 12px; background: #f0fdf4; border: 1px solid #bbf7d0;
  border-radius: 8px; color: #16a34a; font-size: 13px; margin-bottom: 14px;
}
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
  var LOCALE     = '{{ request("locale", "ZH-TW") }}';

  var form        = document.getElementById('register-form');
  var submitBtn   = document.getElementById('reg-submit');
  var errorBox    = document.getElementById('reg-error');
  var errorText   = document.getElementById('reg-error-text');
  var successBox  = document.getElementById('reg-success');
  var successText = document.getElementById('reg-success-text');

  function showError(msg)   { errorText.textContent = msg; errorBox.style.display = 'flex'; successBox.style.display = 'none'; }
  function showSuccess(msg) { successText.textContent = msg; successBox.style.display = 'block'; errorBox.style.display = 'none'; }
  function hideAll()        { errorBox.style.display = 'none'; successBox.style.display = 'none'; }

  async function sha256(msg) {
    var buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(msg));
    return Array.from(new Uint8Array(buf)).map(function(b){ return b.toString(16).padStart(2,'0'); }).join('');
  }

  function validate(credential, name, email, password, confirm) {
    if (!credential) { showError('{{ __("ui.registerPage.errNoCredential") }}'); return false; }
    if (!name)       { showError('{{ __("ui.registerPage.errNoName") }}');       return false; }
    if (!email)      { showError('{{ __("ui.registerPage.errNoEmail") }}');      return false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('{{ __("ui.registerPage.errEmailFormat") }}'); return false; }
    if (!password)   { showError('{{ __("ui.registerPage.errNoPassword") }}');   return false; }
    if (password.length < 8 || password.length > 20) { showError('{{ __("ui.registerPage.errPasswordLength") }}'); return false; }
    if (!/[A-Z]/.test(password)) { showError('{{ __("ui.registerPage.errPasswordUpper") }}'); return false; }
    if (!/[a-z]/.test(password)) { showError('{{ __("ui.registerPage.errPasswordLower") }}'); return false; }
    if (!/[0-9]/.test(password)) { showError('{{ __("ui.registerPage.errPasswordNumber") }}'); return false; }
    if (!/[^A-Za-z0-9]/.test(password)) { showError('{{ __("ui.registerPage.errPasswordSpecial") }}'); return false; }
    if (password !== confirm) { showError('{{ __("ui.registerPage.errPasswordMismatch") }}'); return false; }
    return true;
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      hideAll();

      var credential = document.getElementById('reg-credential').value.trim();
      var name       = document.getElementById('reg-name').value.trim();
      var email      = document.getElementById('reg-email').value.trim();
      var password   = document.getElementById('reg-password').value;
      var confirm    = document.getElementById('reg-confirm').value;

      if (!validate(credential, name, email, password, confirm)) return;

      submitBtn.disabled    = true;
      submitBtn.textContent = '{{ __("ui.registerPage.submittingBtn") }}';

      try {
        var hashedPw = await sha256(password);
        var res = await fetch(PROXY_BASE + '/proxy/api/register', {
          method: 'POST', credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ credential: credential, name: name, email: email, password: hashedPw })
        });
        var json = await res.json();

        if (res.status === 200 || res.status === 201) {
          showSuccess('{{ __("ui.registerPage.successMsg") }}');
          setTimeout(function () {
            window.location.href = '/login?locale=' + LOCALE;
          }, 1500);
        } else {
          showError(json.message || '{{ __("ui.registerPage.errFailed") }}');
          submitBtn.disabled    = false;
          submitBtn.textContent = '{{ __("ui.registerPage.submitBtn") }}';
        }
      } catch (err) {
        console.error(err);
        showError('網路連線錯誤，請檢查網路狀態');
        submitBtn.disabled    = false;
        submitBtn.textContent = '{{ __("ui.registerPage.submitBtn") }}';
      }
    });
  }
})();
</script>
</body>
</html>
