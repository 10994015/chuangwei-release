{{-- resources/views/components/bright-lamp-detail.blade.php --}}
{{--
  對應 BrightLampDetailPage.vue
  接收變數：$frame（完整 frame 物件）
  實際神位資料由前端打 API 動態載入（AJAX）
  此 Blade 只輸出 HTML 骨架，JS 負責查詢與渲染
--}}

@php
  $data = $frame['data'] ?? [];

  $host      = request()->getHost();
  $parts     = explode('.', $host);
  $subdomain = (count($parts) >= 3) ? $parts[0]
             : ((count($parts) === 2 && $parts[1] === 'localhost') ? $parts[0] : '');
  $apiBase   = $subdomain
      ? rtrim('https://' . $subdomain . '.' . config('api.base_domain'), '/')
      : rtrim(config('api.base_url', env('API_BASE_URL', '')), '/');
@endphp

<div class="bld-page">

  {{-- 頂部導航 --}}
  <div class="bld-nav">
    <button class="bld-back-btn" onclick="window.__blGoBack && window.__blGoBack()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      {{ __('ui.brightLampDetail.backBtn') }}
    </button>
    <h1 class="bld-title" id="bld-page-title">{{ __('ui.brightLampDetail.title') }}</h1>
    <div class="bld-nav-placeholder"></div>
  </div>

  {{-- 搜尋列 --}}
  <div class="bld-search-bar">

    <div class="bld-search-field">
      <label class="bld-search-label" for="bld-filter-lamp-no">{{ __('ui.brightLampDetail.lampNoLabel') }}</label>
      <input id="bld-filter-lamp-no" class="bld-search-input" type="text" placeholder="{{ __('ui.brightLampDetail.lampNoPlaceholder') }}" />
    </div>

    <div class="bld-search-field">
      <label class="bld-search-label" for="bld-filter-name">{{ __('ui.brightLampDetail.nameLabel') }}</label>
      <input id="bld-filter-name" class="bld-search-input" type="text" placeholder="{{ __('ui.brightLampDetail.namePlaceholder') }}" />
    </div>

    <div class="bld-search-field">
      <label class="bld-search-label" for="bld-filter-phone">{{ __('ui.brightLampDetail.phoneLabel') }}</label>
      <input id="bld-filter-phone" class="bld-search-input" type="tel" placeholder="{{ __('ui.brightLampDetail.phonePlaceholder') }}" />
    </div>

    <button class="bld-search-btn" id="bld-do-search">{{ __('ui.brightLampDetail.search') }}</button>
  </div>

  {{-- 神位 Grid（由 JS 動態填入） --}}
  <div class="bld-grid" id="bld-lamp-grid">
    {{-- 初始佔位（loading 時顯示） --}}
    <div class="bld-loading" id="bld-loading">{{ __('ui.brightLampDetail.loading') }}</div>
  </div>

  {{-- 空狀態 --}}
  <div class="bld-empty" id="bld-empty" style="display: none;">
    <p>{{ __('ui.brightLampDetail.empty') }}</p>
  </div>

  {{-- 頁碼 --}}
  <div class="bld-pagination" id="bld-pagination"></div>

</div>

{{-- 燈位預訂 Modal --}}
<div class="bld-modal-overlay" id="bld-modal" style="display:none" role="dialog" aria-modal="true">
  <div class="bld-modal">
    <div class="bld-modal-header">
      <span class="bld-modal-title">燈位預訂</span>
      <button class="bld-modal-close" id="bld-modal-close" aria-label="關閉">&#10005;</button>
    </div>
    <div class="bld-modal-body">
      <div class="bld-modal-row" id="bld-mrow-lamp">
        <span class="bld-modal-label">燈種名稱</span>
        <span class="bld-modal-value" id="bld-mval-lamp"></span>
      </div>
      <div class="bld-modal-row" id="bld-mrow-pillar" style="display:none">
        <span class="bld-modal-label">燈柱位置</span>
        <span class="bld-modal-value" id="bld-mval-pillar"></span>
      </div>
      <div class="bld-modal-row">
        <span class="bld-modal-label">燈位編號</span>
        <span class="bld-modal-value" id="bld-mval-slot-no"></span>
      </div>
      <div class="bld-modal-row" id="bld-mrow-price" style="display:none">
        <span class="bld-modal-label">緣金</span>
        <span class="bld-modal-value bld-modal-price" id="bld-mval-price"></span>
      </div>
      <div class="bld-modal-row" id="bld-mrow-date" style="display:none">
        <span class="bld-modal-label">起訖日</span>
        <span class="bld-modal-value" id="bld-mval-date"></span>
      </div>
    </div>
    <div class="bld-modal-footer">
      <button class="bld-modal-cart-btn" id="bld-modal-cart-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        加入購物車
      </button>
    </div>
  </div>
</div>

<style>
/* ==================== 整體頁面 ==================== */
.bld-page {
  width: 100%;
  background: #fff;
  font-family: -apple-system, BlinkMacSystemFont, 'Microsoft JhengHei', sans-serif;
}

/* ==================== 頂部導航 ==================== */
.bld-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 32px;
  border-bottom: 1px solid #e5e5e5;
}

.bld-back-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: none;
  border: none;
  font-size: 14px;
  color: #8b6f47;
  cursor: pointer;
  padding: 6px 0;
  transition: color 0.2s;
  white-space: nowrap;
}
.bld-back-btn:hover { color: #5e4525; }
.bld-back-btn svg { flex-shrink: 0; }

.bld-title {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin: 0;
  text-align: center;
}

.bld-nav-placeholder { flex: 0 0 auto; min-width: 80px; }

/* ==================== 搜尋列 ==================== */
.bld-search-bar {
  display: flex;
  align-items: flex-end;
  gap: 16px;
  padding: 20px 32px;
  border-bottom: 1px solid #e5e5e5;
  flex-wrap: wrap;
  max-width: 1000px;
  margin: 0 auto;
  width: 100%;
  box-sizing: border-box;
}

.bld-search-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
  min-width: 120px;
}

.bld-search-label {
  font-size: 13px;
  font-weight: 500;
  color: #555;
  white-space: nowrap;
}

.bld-select-wrapper { position: relative; }

.bld-search-select {
  width: 100%;
  padding: 9px 32px 9px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
  color: #444;
  background: #fff;
  appearance: none;
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s;
}
.bld-search-select:focus { border-color: #8b6f47; }

.bld-select-arrow {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 11px;
  color: #888;
  pointer-events: none;
}

.bld-search-input {
  width: 100%;
  padding: 9px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
  color: #333;
  background: #fff;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.bld-search-input:focus { border-color: #8b6f47; }
.bld-search-input::placeholder { color: #bbb; }

.bld-search-btn {
  padding: 9px 28px;
  background: #7a5c38;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.2s;
  align-self: flex-end;
  flex-shrink: 0;
}
.bld-search-btn:hover  { background: #5e4525; }
.bld-search-btn:active { background: #4a3318; }

/* ==================== 神位 Grid ==================== */
.bld-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 312px));
  gap: 0;
  padding: 16px 32px 32px;
  background: #fff;
  max-width: 1600px;
  margin: 0 auto;
  width: 100%;
  box-sizing: border-box;
}

/* ==================== 載入中 / 空狀態 ==================== */
.bld-loading {
  grid-column: 1 / -1;
  padding: 80px 20px;
  text-align: center;
  color: #999;
  font-size: 15px;
}

.bld-empty {
  padding: 80px 20px;
  text-align: center;
  color: #999;
  font-size: 15px;
}

/* ==================== 神位格 ==================== */
.bld-cell {
  position: relative;
  aspect-ratio: 3 / 4;
  width: 100%;
  padding: 6px;
  background: #2a1508;
  box-sizing: border-box;
  cursor: pointer;
  transition: filter 0.2s;
}
.bld-cell:hover { filter: brightness(1.08); }

/* 木框外框 */
.bld-outer-frame {
  width: 100%;
  height: 100%;
  position: relative;
  box-sizing: border-box;
  border: 12px solid #5a2d0c;
  border-radius: 4px;
  box-shadow:
    inset 0 0 0 2px #c87820,
    inset 0 0 0 5px #3d1a06,
    0 0 0 1px #3d1a06;
}

.bld-outer-frame::before,
.bld-outer-frame::after {
  content: '';
  position: absolute;
  z-index: 5;
}
.bld-outer-frame::before {
  bottom: -10px; left: -10px;
  width: 32px; height: 32px;
  clip-path: polygon(0 100%, 100% 100%, 0 0);
  background: linear-gradient(135deg, #e89830 0%, #8b4a10 50%, #5a2d0c 100%);
}
.bld-outer-frame::after {
  bottom: -10px; right: -10px;
  width: 32px; height: 32px;
  clip-path: polygon(100% 100%, 0 100%, 100% 0);
  background: linear-gradient(225deg, #e89830 0%, #8b4a10 50%, #5a2d0c 100%);
}

/* 內框 */
.bld-inner {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
  background: #1a0a04;
  border-radius: 2px;
}
.bld-inner::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 14px;
  background: linear-gradient(180deg, #5a3010 0%, #2a1404 100%);
  z-index: 4;
  border-bottom: 2px solid #8b5a20;
}

/* 空位可點擊樣式 */
.bld-cell--available:hover { filter: brightness(1.2); outline: 2px solid #c87820; }
.bld-cell--loading { opacity: 0.5; pointer-events: none; cursor: wait; }

/* 燈位編號（置中） */
.bld-slot-no {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  font-weight: 900;
  color: #f5d080;
  letter-spacing: 2px;
  z-index: 6;
  pointer-events: none;
  text-shadow: 0 1px 6px rgba(0,0,0,0.9), 0 0 12px rgba(0,0,0,0.7);
}

/* 神明圖片 */
.bld-god-img {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 28%;
  width: 100%;
  height: 72%;
  object-fit: cover;
  object-position: center top;
  display: block;
  z-index: 1;
}

/* 空位圖片 */
.bld-empty-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: fill;
  display: block;
  z-index: 1;
}

/* 名牌區 */
.bld-nameplate {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 28%;
  z-index: 3;
  background: linear-gradient(180deg, #0a0402 0%, #100604 100%);
  padding: 10px 10px 12px;
  text-align: center;
  border-top: 2px solid #8b5a20;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.bld-name {
  font-size: 17px;
  font-weight: 700;
  color: #f5d080;
  letter-spacing: 4px;
}

.bld-wish {
  font-size: 13px;
  color: #c8901c;
  letter-spacing: 2px;
  line-height: 1.5;
}

/* ==================== Modal ==================== */
.bld-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9000;
  padding: 20px;
}

.bld-modal {
  background: #fff;
  border-radius: 12px;
  width: 100%;
  max-width: 420px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.bld-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px;
  background: #8b6f47;
}

.bld-modal-title {
  font-size: 17px;
  font-weight: 700;
  color: #fff;
}

.bld-modal-close {
  background: none;
  border: none;
  color: #fff;
  font-size: 18px;
  cursor: pointer;
  padding: 0;
  line-height: 1;
  opacity: 0.85;
  transition: opacity 0.2s;
}
.bld-modal-close:hover { opacity: 1; }

.bld-modal-body {
  padding: 8px 24px 0;
}

.bld-modal-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 0;
  border-bottom: 1px solid #f0ebe4;
}
.bld-modal-row:last-child { border-bottom: none; }

.bld-modal-label {
  font-size: 14px;
  color: #777;
}

.bld-modal-value {
  font-size: 14px;
  color: #333;
  font-weight: 500;
  text-align: right;
}

.bld-modal-price {
  color: #c0392b;
  font-size: 16px;
  font-weight: 700;
}

.bld-modal-footer {
  padding: 20px 24px 24px;
}

.bld-modal-cart-btn {
  width: 100%;
  padding: 14px;
  background: #8b6f47;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.bld-modal-cart-btn:hover  { background: #7a5c38; }
.bld-modal-cart-btn:active { background: #5e4525; }
.bld-modal-cart-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* ==================== 頁碼 ==================== */
.bld-pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 16px 32px 32px;
  flex-wrap: wrap;
}

.bld-page-btn {
  width: 36px; height: 36px;
  border: 1.5px solid #8b5a20;
  border-radius: 50%;
  background: transparent;
  color: #7a5c38;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
}
.bld-page-btn:hover:not([disabled]) { background: #8b5a20; color: #fff; }
.bld-page-btn.active { background: #7a5c38; border-color: #7a5c38; color: #fff; font-weight: 700; }
.bld-page-btn[disabled] { opacity: 0.35; cursor: default; pointer-events: none; }

/* ==================== 響應式 ==================== */
@media (max-width: 1024px) {
  .bld-grid { grid-template-columns: repeat(3, 1fr); padding: 12px 20px 24px; }
  .bld-nav  { padding: 14px 20px; }
  .bld-title { font-size: 18px; }
  .bld-search-bar { padding: 16px 20px; gap: 12px; }
  .bld-name { font-size: 13px; }
  .bld-wish { font-size: 11px; }
}

@media (max-width: 768px) {
  .bld-nav { padding: 12px 16px; }
  .bld-title { font-size: 15px; }
  .bld-back-btn { font-size: 13px; }
  .bld-nav-placeholder { min-width: 60px; }

  .bld-search-bar {
    padding: 12px 16px;
    gap: 8px;
    max-width: 100%;
  }
  .bld-search-field {
    flex: 1 1 calc(50% - 8px);
    min-width: 0;
  }
  .bld-search-btn {
    flex: 1 1 100%;
    text-align: center;
  }

  .bld-grid { grid-template-columns: repeat(2, 1fr); padding: 8px 12px 20px; }
  .bld-cell { padding: 4px; }
  .bld-name { font-size: 11px; letter-spacing: 1px; }
  .bld-wish { font-size: 10px; }
  .bld-nameplate { padding: 5px 6px 7px; gap: 3px; }
  .bld-outer-frame { border-width: 6px; }
}
</style>

<script>
(function () {
  var BLD_API_BASE     = '{{ $apiBase }}';
  var currentLampId    = '';
  var currentLampLabel = '';
  var currentApiPage   = 1;
  var API_PAGE_SIZE    = 20;
  var pendingSlot      = null;

  /* ── 從搜尋頁跳入時呼叫 ── */
  window.__blGoDetail = function (params) {
    params = params || {};

    var title = document.getElementById('bld-page-title');
    if (title && params.lampTypeLabel) title.textContent = params.lampTypeLabel;

    currentLampLabel = params.lampTypeLabel || '';
    currentLampId    = params.lampTypeId    || '';

    if (params.name)   document.getElementById('bld-filter-name').value    = params.name;
    if (params.phone)  document.getElementById('bld-filter-phone').value   = params.phone;
    if (params.lampNo) document.getElementById('bld-filter-lamp-no').value = params.lampNo;

    fetchAndRender(1);
  };

  document.getElementById('bld-do-search').addEventListener('click', applyFilter);

  /* ── 篩選：重置頁碼後打 API ── */
  function applyFilter() {
    currentApiPage = 1;
    fetchAndRender(1);
  }

  /* ── 呼叫 API（帶篩選參數 + 分頁） ── */
  function fetchAndRender(page) {
    if (!currentLampId) { showEmpty(); return; }

    currentApiPage = page;

    var grid  = document.getElementById('bld-lamp-grid');
    var empty = document.getElementById('bld-empty');
    grid.innerHTML = '<div class="bld-loading">{{ __("ui.brightLampDetail.loading") }}</div>';
    empty.style.display = 'none';
    document.getElementById('bld-pagination').innerHTML = '';

    var name   = document.getElementById('bld-filter-name').value.trim();
    var phone  = document.getElementById('bld-filter-phone').value.trim();
    var lampNo = document.getElementById('bld-filter-lamp-no').value.trim();

    var params = 'page=' + page + '&pageSize=' + API_PAGE_SIZE;
    if (name)   params += '&prayerUserName='  + encodeURIComponent(name);
    if (phone)  params += '&prayerUserPhone=' + encodeURIComponent(phone);
    if (lampNo) params += '&slotNumber='      + encodeURIComponent(lampNo);

    fetch(BLD_API_BASE + '/api/product/temple/lamp/' + currentLampId + '/slot?' + params, {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.statusCode === 200) {
          var slots      = res.data.data       || [];
          var totalPages = res.data.totalPages || 1;
          if (slots.length === 0) {
            showEmpty();
          } else {
            renderSlots(slots);
            renderPagination(page, totalPages);
          }
        } else {
          showEmpty();
        }
      })
      .catch(function () { showEmpty(); });
  }

  function showEmpty() {
    document.getElementById('bld-lamp-grid').innerHTML = '';
    document.getElementById('bld-empty').style.display = '';
    document.getElementById('bld-pagination').innerHTML = '';
  }

  function renderPagination(page, totalPages) {
    var pager = document.getElementById('bld-pagination');
    pager.innerHTML = '';
    if (totalPages <= 1) return;

    function makeBtn(label, targetPage, isActive, isDisabled) {
      var btn = document.createElement('button');
      btn.className = 'bld-page-btn' + (isActive ? ' active' : '');
      btn.textContent = label;
      if (isDisabled) btn.disabled = true;
      btn.addEventListener('click', function () {
        fetchAndRender(targetPage);
        document.getElementById('bld-lamp-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
      return btn;
    }

    pager.appendChild(makeBtn('‹', page - 1, false, page <= 1));
    for (var i = 1; i <= totalPages; i++) {
      pager.appendChild(makeBtn(String(i), i, i === page, false));
    }
    pager.appendChild(makeBtn('›', page + 1, false, page >= totalPages));
  }

  /* ── 渲染燈位格 ── */
  function renderSlots(slots) {
    var grid = document.getElementById('bld-lamp-grid');
    grid.innerHTML = '';
    document.getElementById('bld-empty').style.display = 'none';

    slots.forEach(function (slot) {
      var cell       = document.createElement('div');
      cell.className = 'bld-cell';

      var outerFrame       = document.createElement('div');
      outerFrame.className = 'bld-outer-frame';

      var inner       = document.createElement('div');
      inner.className = 'bld-inner';

      var slotNo = document.createElement('div');
      slotNo.className   = 'bld-slot-no';
      slotNo.textContent = slot.slotNumber || '';
      inner.appendChild(slotNo);

      var isOccupied = slot.status !== 'OPEN';

      if (isOccupied) {
        var img       = document.createElement('img');
        img.className = 'bld-god-img';
        img.src       = slot.productSkuImg || '/images/bright-light/god.jpg';
        img.alt       = slot.prayerUserName || '神明';

        var nameplate       = document.createElement('div');
        nameplate.className = 'bld-nameplate';

        var nameEl       = document.createElement('div');
        nameEl.className = 'bld-name';
        nameEl.textContent = slot.prayerUserName || '';

        var wishEl       = document.createElement('div');
        wishEl.className = 'bld-wish';
        wishEl.textContent = slot.prayer || '';

        nameplate.appendChild(nameEl);
        nameplate.appendChild(wishEl);
        inner.appendChild(img);
        inner.appendChild(nameplate);
      } else {
        var emptyImg = document.createElement('img');
        emptyImg.className = 'bld-empty-img';
        emptyImg.src = '/images/bright-light/empty.png';
        emptyImg.alt = '';
        emptyImg.setAttribute('aria-hidden', 'true');
        inner.appendChild(emptyImg);

        /* 空位可點擊 → 開燈位預訂 Modal */
        cell.classList.add('bld-cell--available');
        cell.addEventListener('click', (function (s) {
          return function () { openSlotModal(s); };
        })(slot));
      }

      outerFrame.appendChild(inner);
      cell.appendChild(outerFrame);
      grid.appendChild(cell);
    });
  }

  /* ── Modal 開關 ── */
  var modalOverlay = document.getElementById('bld-modal');
  var modalCartBtn = document.getElementById('bld-modal-cart-btn');

  function openSlotModal(slot) {
    pendingSlot = slot;
    console.log('[bright-lamp-detail] slot data:', JSON.stringify(slot, null, 2));

    // 同時 log slot-id API 回傳
    fetch(BLD_API_BASE + '/api/product/all/lamp/' + slot.productId + '/slot-id?slotNumber=' + slot.slotNumber, {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r){ return r.json(); })
    .then(function(res){ console.log('[bright-lamp-detail] slot-id API response:', JSON.stringify(res, null, 2)); })
    .catch(function(e){ console.log('[bright-lamp-detail] slot-id API error:', e); });

    // 燈種名稱
    document.getElementById('bld-mval-lamp').textContent = currentLampLabel || '—';

    // 燈柱位置（有才顯示）
    var pillarVal = slot.pillarNo || slot.pillarName || slot.pillarPosition || '';
    var pillarRow = document.getElementById('bld-mrow-pillar');
    if (pillarVal) {
      document.getElementById('bld-mval-pillar').textContent = pillarVal;
      pillarRow.style.display = '';
    } else {
      pillarRow.style.display = 'none';
    }

    // 燈位編號
    document.getElementById('bld-mval-slot-no').textContent = slot.slotNumber || '—';

    // 緣金（有才顯示）
    var price = slot.price || slot.skuPrice || slot.amount || '';
    var priceRow = document.getElementById('bld-mrow-price');
    if (price !== '' && price !== null && price !== undefined) {
      document.getElementById('bld-mval-price').textContent = 'NT$ ' + Number(price).toLocaleString();
      priceRow.style.display = '';
    } else {
      priceRow.style.display = 'none';
    }

    // 起訖日（有才顯示）
    var start = slot.startDate || slot.startAt || '';
    var end   = slot.endDate   || slot.endAt   || '';
    var dateRow = document.getElementById('bld-mrow-date');
    if (start || end) {
      document.getElementById('bld-mval-date').textContent = start && end ? start + ' ～ ' + end : (start || end);
      dateRow.style.display = '';
    } else {
      dateRow.style.display = 'none';
    }

    modalCartBtn.disabled = false;
    modalCartBtn.textContent = '';
    modalCartBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>加入購物車';
    modalOverlay.style.display = 'flex';
  }

  function closeModal() {
    modalOverlay.style.display = 'none';
    pendingSlot = null;
  }

  document.getElementById('bld-modal-close').addEventListener('click', closeModal);
  modalOverlay.addEventListener('click', function (e) {
    if (e.target === modalOverlay) closeModal();
  });

  /* ── Modal 加入購物車 → 取 lampSlotId → 送 API ── */
  modalCartBtn.addEventListener('click', function () {
    if (!pendingSlot) return;
    var slot = pendingSlot;

    modalCartBtn.disabled = true;
    modalCartBtn.textContent = '加入中…';

    var productId  = slot.productId;
    var slotNumber = slot.slotNumber;
    var skuId      = slot.isSpecialSlot ? slot.specialLampSkuId : slot.chooseLampSkuId;

    fetch(BLD_API_BASE + '/api/product/all/lamp/' + productId + '/slot-id?slotNumber=' + slotNumber, {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.statusCode !== 200) throw new Error(res.message || '取得燈位失敗');
        var lampSlotId = res.data && res.data.lampSlotId;
        if (!lampSlotId) throw new Error('無法取得燈位 ID，請稍後再試');

        return fetch(BLD_API_BASE + '/api/frontend/cart/item', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          body: JSON.stringify({ items: [{
            productId:    productId,
            productSkuId: skuId || null,
            lampSlotId:   lampSlotId,
            quantity:     1,
            isSelected:   true
          }] })
        });
      })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.statusCode === 200) {
          closeModal();
          alert('已成功加入購物車');
        } else {
          throw new Error(res.message || '加入購物車失敗');
        }
      })
      .catch(function (err) {
        alert(err.message || '操作失敗，請稍後再試');
        modalCartBtn.disabled = false;
        modalCartBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>加入購物車';
      });
  });

})();
</script>
