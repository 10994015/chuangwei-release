{{-- resources/views/components/bright-lamp.blade.php --}}
@php
  $data         = $frame['data'] ?? [];
  $bgImgSrc     = $data['bgImgSrc']     ?? '/images/bright-light/bg.png';
  $mainImgSrc   = $data['mainImgSrc']   ?? '/images/bright-light/main.jpg';
  $borderOption = $data['borderOption'] ?? 'border';
  $lampTypes    = $data['lampTypes']    ?? [
    ['value' => 'bright', 'label' => __('ui.brightLampBasemap.lampBright')],
    ['value' => 'peace',  'label' => __('ui.brightLampBasemap.lampPeace')],
    ['value' => 'wealth', 'label' => __('ui.brightLampBasemap.lampWealth')],
    ['value' => 'wisdom', 'label' => __('ui.brightLampBasemap.lampWisdom')],
  ];

  $borderSrc = match($borderOption) {
    'border' => '/images/bright-light/border.png',
    default  => '/images/bright-light/border.png',
  };
@endphp

<div class="bright-lamp-wrapper" id="bright-lamp-root"
  data-bg="{{ $bgImgSrc }}"
  data-main="{{ $mainImgSrc }}"
  data-border="{{ $borderSrc }}"
>

  {{-- 搜尋首頁 --}}
  <section class="bright-lamp-section" id="bright-lamp-search">

    {{-- 全幅場景 --}}
    <div class="bl-scene">
      <img class="bl-scene__bg" src="{{ $bgImgSrc }}" alt="" aria-hidden="true" />
      <img class="bl-scene__main" src="{{ $mainImgSrc }}" alt="主神像" />
    </div>

    {{-- 搜尋面板 --}}
    <div class="bl-panel-wrap">
      <div class="bl-panel">
        <img class="bl-panel__border" src="{{ $borderSrc }}" alt="" aria-hidden="true" />

        <div class="bl-panel__body">

          {{-- 上排：燈別 + 搜尋模式 --}}
          <div class="bl-panel__selects">
            <div class="bl-select-wrapper">
              <select class="bl-panel__select" id="bl-lamp-type">
                <option value="">{{ __('ui.brightLampBasemap.selectLampType') }}</option>
                @foreach($lampTypes as $opt)
                  <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                @endforeach
              </select>
              <span class="bl-select-arrow">&#9662;</span>
            </div>

            <div class="bl-select-wrapper">
              <select class="bl-panel__select" id="bl-search-mode">
                <option value="name-phone">{{ __('ui.brightLampBasemap.searchByNamePhone') }}</option>
                <option value="lamp-no">{{ __('ui.brightLampBasemap.searchByLampNo') }}</option>
              </select>
              <span class="bl-select-arrow">&#9662;</span>
            </div>
          </div>

          {{-- 欄位區 --}}
          <div class="bl-panel__fields-wrap">

            {{-- 模式一：姓名電話 --}}
            <div class="bl-panel__fields" id="bl-fields-name-phone">
              <div class="bl-field-group">
                <label class="bl-field-label" for="bl-input-name">{{ __('ui.brightLampBasemap.labelName') }}</label>
                <input id="bl-input-name" class="bl-panel__input" type="text" placeholder="{{ __('ui.brightLampBasemap.placeholderName') }}" />
              </div>
              <div class="bl-field-group">
                <label class="bl-field-label" for="bl-input-phone">{{ __('ui.brightLampBasemap.labelPhone') }}</label>
                <div class="bl-input-with-btn">
                  <input id="bl-input-phone" class="bl-panel__input" type="tel" placeholder="{{ __('ui.brightLampBasemap.placeholderPhone') }}" />
                  <button class="bl-panel__btn" id="bl-search-btn">{{ __('ui.brightLampBasemap.search') }}</button>
                </div>
              </div>
            </div>

            {{-- 模式二：燈位編號 --}}
            <div class="bl-panel__fields" id="bl-fields-lamp-no" style="display: none;">
              <div class="bl-field-group">
                <label class="bl-field-label" for="bl-input-lamp-no">{{ __('ui.brightLampBasemap.labelLampNo') }}</label>
                <div class="bl-input-with-btn">
                  <input id="bl-input-lamp-no" class="bl-panel__input" type="text" placeholder="{{ __('ui.brightLampBasemap.placeholderLampNo') }}" />
                  <button class="bl-panel__btn" id="bl-search-btn-no">{{ __('ui.brightLampBasemap.search') }}</button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

  </section>

  {{-- 神位列表頁（搜尋後切換顯示） --}}
  <div id="bright-lamp-detail" style="display: none;">
    @include('components.bright-lamp-detail', ['frame' => $frame])
  </div>

</div>

<style>
/* ==================== 外層 ==================== */
.bright-lamp-wrapper { width: 100%; }

/* ==================== 場景 ==================== */
.bright-lamp-section {
  position: relative;
  width: 100%;
  min-height: 640px;
  display: flex;
  flex-direction: column;
  align-items: center;
  overflow: hidden;
}

.bl-scene {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 640px;
}

.bl-scene__bg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;
  display: block;
}

.bl-scene__main {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  height: 85%;
  width: auto;
  max-width: 36%;
  object-fit: contain;
  object-position: top center;
  display: block;
  z-index: 1;
}

/* ==================== 搜尋面板 ==================== */
.bl-panel-wrap {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 500px;
  padding: 0 16px 0;
  display: flex;
  justify-content: center;
  z-index: 10;
}

.bl-panel { position: relative; width: 100%; }

.bl-panel__border {
  position: absolute;
  inset: -10px -16px;
  width: calc(100% + 32px);
  height: calc(100% + 20px);
  object-fit: fill;
  pointer-events: none;
  z-index: 1;
}

.bl-panel__body {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.96);
  border-radius: 4px;
  padding: 24px 28px 28px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* ==================== 下拉列 ==================== */
.bl-panel__selects { display: flex; gap: 12px; }

.bl-select-wrapper { position: relative; flex: 1; }

.bl-panel__select {
  width: 100%;
  padding: 10px 36px 10px 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
  color: #444;
  background: #fff;
  appearance: none;
  cursor: pointer;
  outline: none;
  transition: border-color 0.2s;
}
.bl-panel__select:focus { border-color: #8b6f47; }

.bl-select-arrow {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
  color: #888;
  pointer-events: none;
}

/* ==================== 欄位切換容器 ==================== */
.bl-panel__fields-wrap { min-height: 148px; }

.bl-panel__fields { display: flex; flex-direction: column; gap: 16px; }

.bl-field-group { display: flex; flex-direction: column; gap: 6px; }

.bl-field-label { font-size: 13px; font-weight: 500; color: #555; }

.bl-panel__input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
  color: #333;
  background: #fff;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.bl-panel__input:focus { border-color: #8b6f47; }
.bl-panel__input::placeholder { color: #bbb; }

.bl-input-with-btn { display: flex; gap: 8px; }
.bl-input-with-btn .bl-panel__input { flex: 1; }

.bl-panel__btn {
  padding: 10px 24px;
  background: #7a5c38;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.2s;
}
.bl-panel__btn:hover  { background: #5e4525; }
.bl-panel__btn:active { background: #4a3318; }

/* ==================== 平板 RWD（1024px）==================== */
@media (max-width: 1024px) {
  .bl-scene__main { max-width: 42%; height: 78%; }
  .bl-panel-wrap  { max-width: 460px; }
  .bl-panel__body { padding: 20px 24px 24px; }
}

/* ==================== 手機 RWD（768px）==================== */
@media (max-width: 768px) {
  .bright-lamp-section {
    min-height: 0;
    flex-direction: column;
  }

  .bl-scene {
    min-height: 0;
    height: 320px;
    flex-shrink: 0;
  }

  .bl-scene__main {
    height: 100%;
    max-width: 60%;
    object-position: top center;
  }

  .bl-panel-wrap {
    position: relative;
    bottom: auto;
    left: auto;
    transform: none;
    max-width: 100%;
    padding: 0;
    width: 100%;
  }

  .bl-panel__border { display: none; }

  .bl-panel__body {
    padding: 20px 16px 24px;
    gap: 14px;
    border-radius: 0;
    border-top: 3px solid #8b6f47;
    background: rgba(255, 255, 255, 0.98);
  }

  .bl-panel__selects { flex-direction: column; gap: 8px; }

  .bl-panel__btn { padding: 10px 16px; }

  .bl-panel__fields-wrap { min-height: 130px; }
}
</style>

<script>
(function () {
  var modeSelect    = document.getElementById('bl-search-mode');
  var fieldsNamePh  = document.getElementById('bl-fields-name-phone');
  var fieldsLampNo  = document.getElementById('bl-fields-lamp-no');
  var searchSection = document.getElementById('bright-lamp-search');
  var detailSection = document.getElementById('bright-lamp-detail');

  function switchMode() {
    if (modeSelect.value === 'lamp-no') {
      fieldsNamePh.style.display = 'none';
      fieldsLampNo.style.display = '';
    } else {
      fieldsNamePh.style.display = '';
      fieldsLampNo.style.display = 'none';
    }
  }

  function goToDetail() {
    searchSection.style.display = 'none';
    detailSection.style.display = '';

    var lampTypeSelect = document.getElementById('bl-lamp-type');
    var lampTypeLabel  = lampTypeSelect.options[lampTypeSelect.selectedIndex]?.text || '光明燈';
    var name    = document.getElementById('bl-input-name')?.value    || '';
    var phone   = document.getElementById('bl-input-phone')?.value   || '';
    var lampNo  = document.getElementById('bl-input-lamp-no')?.value || '';

    if (window.__blGoDetail) {
      window.__blGoDetail({ lampTypeLabel: lampTypeLabel, name: name, phone: phone, lampNo: lampNo });
    } else {
      setTimeout(function () {
        window.__blGoDetail && window.__blGoDetail({ lampTypeLabel: lampTypeLabel, name: name, phone: phone, lampNo: lampNo });
      }, 50);
    }
  }

  function goBack() {
    detailSection.style.display = 'none';
    searchSection.style.display = '';
  }

  modeSelect.addEventListener('change', switchMode);

  document.getElementById('bl-search-btn').addEventListener('click', goToDetail);
  document.getElementById('bl-search-btn-no').addEventListener('click', goToDetail);

  window.__blGoBack = goBack;
})();
</script>
