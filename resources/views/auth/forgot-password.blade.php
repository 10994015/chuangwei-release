{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('ui.forgotPasswordPage.title') }}</title>
  @vite(['resources/css/app.css', 'resources/css/components.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-page">
  <div class="auth-card">

    <div class="auth-card-header">
      <img src="/images/icon.png" alt="Logo" class="auth-logo" onerror="this.style.display='none'" />
      <h1 class="auth-card-title">{{ __('ui.forgotPasswordPage.title') }}</h1>
      <p class="auth-card-desc">{{ __('ui.forgotPasswordPage.desc') }}</p>
    </div>

    <form id="forgot-form" novalidate>
      <div class="auth-field">
        <label class="auth-label">{{ __('ui.forgotPasswordPage.labelEmail') }}</label>
        <input id="forgot-email" type="email" class="auth-input" placeholder="{{ __('ui.forgotPasswordPage.placeholderEmail') }}" autocomplete="email" />
      </div>

      <div class="auth-error" id="forgot-error" style="display:none">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <span id="forgot-error-text"></span>
      </div>
      <div class="auth-success" id="forgot-success" style="display:none">
        <svg viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;flex-shrink:0"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span id="forgot-success-text"></span>
      </div>

      <button type="submit" class="auth-btn-submit" id="forgot-submit">{{ __('ui.forgotPasswordPage.submitBtn') }}</button>
    </form>

    <p class="auth-switch">
      <a href="/login?locale={{ request('locale','ZH-TW') }}" class="auth-switch-link">← {{ __('ui.forgotPasswordPage.backToLogin') }}</a>
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
.auth-card-header { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 28px; }
.auth-logo { height: 48px; object-fit: contain; }
.auth-card-title { font-size: 22px; font-weight: 700; color: #1f2937; margin: 0; }
.auth-card-desc { font-size: 13px; color: #6b7280; margin: 0; text-align: center; line-height: 1.6; }
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
  display: flex; align-items: flex-start; gap: 8px;
  padding: 10px 12px; background: #f0fdf4; border: 1px solid #bbf7d0;
  border-radius: 8px; color: #16a34a; font-size: 13px; margin-bottom: 14px; line-height: 1.6;
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

  var form        = document.getElementById('forgot-form');
  var submitBtn   = document.getElementById('forgot-submit');
  var errorBox    = document.getElementById('forgot-error');
  var errorText   = document.getElementById('forgot-error-text');
  var successBox  = document.getElementById('forgot-success');
  var successText = document.getElementById('forgot-success-text');

  function showError(msg)   { errorText.textContent = msg; errorBox.style.display = 'flex'; successBox.style.display = 'none'; }
  function showSuccess(msg) { successText.textContent = msg; successBox.style.display = 'flex'; errorBox.style.display = 'none'; }
  function hideAll()        { errorBox.style.display = 'none'; successBox.style.display = 'none'; }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      hideAll();

      var email = document.getElementById('forgot-email').value.trim();
      if (!email) { showError('{{ __("ui.forgotPasswordPage.errNoEmail") }}'); return; }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('{{ __("ui.forgotPasswordPage.errEmailFormat") }}'); return; }

      submitBtn.disabled    = true;
      submitBtn.textContent = '{{ __("ui.forgotPasswordPage.submittingBtn") }}';

      try {
        var res = await fetch(PROXY_BASE + '/proxy/api/user/change-password', {
          method: 'POST', credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email })
        });
        var json = await res.json();

        if (res.status === 200 || res.status === 202) {
          showSuccess('{{ __("ui.forgotPasswordPage.successMsg") }}');
          submitBtn.disabled    = true;
          submitBtn.textContent = '{{ __("ui.forgotPasswordPage.submitBtn") }}';
        } else {
          showError(json.message || '{{ __("ui.forgotPasswordPage.errFailed") }}');
          submitBtn.disabled    = false;
          submitBtn.textContent = '{{ __("ui.forgotPasswordPage.submitBtn") }}';
        }
      } catch (err) {
        console.error(err);
        showError('網路連線錯誤，請檢查網路狀態');
        submitBtn.disabled    = false;
        submitBtn.textContent = '{{ __("ui.forgotPasswordPage.submitBtn") }}';
      }
    });
  }
})();
</script>
</body>
</html>
