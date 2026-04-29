{{-- resources/views/product/show.blade.php --}}
@extends('layouts.site')

@section('content')
@php
  $name        = $product['nameZhTw']  ?? ($product['name'] ?? '');
  $tenantName  = $product['tenantName'] ?? '';
  $price       = isset($product['price']) ? (float)$product['price'] : 0;
  $priceStr    = 'NT$ ' . number_format($price);
  $description = $product['descriptionZhTw'] ?? ($product['description'] ?? '');
  $imgs        = $product['imgs'] ?? [];
  $mainImg     = !empty($imgs) ? $imgs[0]['url'] : null;
  $specs       = $product['specs'] ?? ($product['specialSlotNumberJson'] ?? []);
  $productId   = $product['id'] ?? '';
  $locale      = request()->query('locale', 'ZH-TW');
  $backUrl     = '/' . request()->query('from', 'home') . '?locale=' . $locale;
@endphp

{{-- Navbar --}}
@if($headerFrame)
  @php $frame = ['data' => $headerFrame['data']]; @endphp
  @if($headerFrame['type'] === 'PV_HEADER')
    <x-pv-navbar :frame="$frame" :slug="$slug" :locales="$locales" />
  @else
    <x-navbar :frame="$frame" :slug="$slug" :locales="$locales" />
  @endif
@endif

<div class="pd-page">
  <div class="pd-container">

    {{-- 返回 --}}
    <a href="{{ $backUrl }}" class="pd-back">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      返回
    </a>

    {{-- 主內容 --}}
    <div class="pd-main">

      {{-- 左：圖片 --}}
      <div class="pd-gallery">
        <div class="pd-main-image" id="pd-main-img-wrap">
          @if($mainImg)
            <img src="{{ $mainImg }}" alt="{{ $name }}" class="pd-main-img" id="pd-main-img" />
          @else
            <div class="pd-img-placeholder">
              <svg viewBox="0 0 80 80" fill="none"><rect x="8" y="14" width="64" height="48" rx="4" stroke="#bbb" stroke-width="3"/><circle cx="28" cy="32" r="7" stroke="#bbb" stroke-width="3"/><path d="M8 50l18-16 14 14 10-10 18 18" stroke="#bbb" stroke-width="3" stroke-linejoin="round"/></svg>
            </div>
          @endif
        </div>

        @if(count($imgs) > 1)
          <div class="pd-thumbnails">
            @foreach($imgs as $i => $img)
              <button
                class="pd-thumb {{ $i === 0 ? 'active' : '' }}"
                data-src="{{ $img['url'] }}"
                onclick="pdSetImg(this)"
              >
                <img src="{{ $img['url'] }}" alt="{{ $name }} {{ $i+1 }}" />
              </button>
            @endforeach
          </div>
        @endif
      </div>

      {{-- 右：資訊 --}}
      <div class="pd-info">

        @if($tenantName)
          <div class="pd-tenant">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $tenantName }}
          </div>
        @endif

        <h1 class="pd-name">{{ $name }}</h1>
        <div class="pd-price" id="pd-price">{{ $priceStr }}</div>

        @if($description)
          <p class="pd-description">{{ $description }}</p>
        @endif

        <hr class="pd-divider" />

        {{-- 規格 --}}
        @if(!empty($specs))
          <div class="pd-field">
            <label class="pd-label">選擇規格</label>
            <select class="pd-select" id="pd-spec">
              @foreach($specs as $spec)
                <option value="{{ $spec }}">{{ $spec }}</option>
              @endforeach
            </select>
          </div>
        @endif

        {{-- 數量 --}}
        <div class="pd-field">
          <label class="pd-label">數量</label>
          <div class="pd-qty">
            <button class="pd-qty-btn" id="pd-qty-minus" onclick="pdChangeQty(-1)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
            <span class="pd-qty-val" id="pd-qty">1</span>
            <button class="pd-qty-btn" id="pd-qty-plus" onclick="pdChangeQty(1)">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
          </div>
        </div>

        <hr class="pd-divider" />

        {{-- 總金額 --}}
        <div class="pd-total-row">
          <span class="pd-total-label">總金額</span>
          <span class="pd-total-price" id="pd-total">{{ $priceStr }}</span>
        </div>

        {{-- 加入購物車 --}}
        <button class="pd-cart-btn" id="pd-cart-btn" onclick="pdAddToCart()">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
          </svg>
          加入購物車
        </button>

      </div>
    </div>

    {{-- 推薦結緣品 --}}
    @if(!empty($recommended))
      <div class="pd-rec-section">
        <h2 class="pd-rec-title">推薦結緣品</h2>
        <div class="pd-rec-grid">
          @foreach($recommended as $rec)
            @php
              $recImg   = !empty($rec['imgs']) ? $rec['imgs'][0]['url'] : null;
              $recPrice = isset($rec['price']) ? 'NT$ ' . number_format((float)$rec['price']) : '';
              $recName  = $rec['nameZhTw'] ?? ($rec['name'] ?? '');
              $recId    = $rec['id'] ?? '';
            @endphp
            <a class="pd-rec-card" href="/product/{{ $recId }}?locale={{ $locale }}&from={{ request()->query('from','home') }}">
              <div class="pd-rec-image">
                @if($recImg)
                  <img src="{{ $recImg }}" alt="{{ $recName }}" loading="lazy" />
                @else
                  <div class="pd-rec-img-placeholder"></div>
                @endif
              </div>
              <div class="pd-rec-info">
                <p class="pd-rec-tenant">{{ $rec['tenantName'] ?? '' }}</p>
                <h3 class="pd-rec-name">{{ $recName }}</h3>
                <div class="pd-rec-footer">
                  <span class="pd-rec-price">{{ $recPrice }}</span>
                  <span class="pd-rec-cart-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                  </span>
                </div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif

  </div>
</div>

{{-- Footer --}}
@if($footerFrame)
  @php $frame = ['data' => $footerFrame['data']]; @endphp
  @if($footerFrame['type'] === 'PV_FOOTER')
    <x-pv-footer :frame="$frame" :locales="$locales" />
  @else
    <x-footer :frame="$frame" />
  @endif
@endif

<style>
.pd-page { background: #fff; min-height: 70vh; padding: 2rem 0 5rem; }
.pd-container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }

.pd-back {
  display: inline-flex; align-items: center; gap: 6px;
  color: #666; font-size: 14px; text-decoration: none;
  margin-bottom: 1.5rem; transition: color 0.2s;
}
.pd-back:hover { color: #E8572A; }

/* ── 主內容 ── */
.pd-main { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start; margin-bottom: 4rem; }

/* ── 圖片 ── */
.pd-gallery {}
.pd-main-image {
  width: 100%; aspect-ratio: 4/3; border-radius: 12px; overflow: hidden;
  background: #f5f5f5; margin-bottom: 12px;
}
.pd-main-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: opacity 0.2s; }
.pd-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
.pd-img-placeholder svg { width: 64px; height: 64px; }

.pd-thumbnails { display: flex; gap: 8px; flex-wrap: wrap; }
.pd-thumb {
  width: 72px; height: 72px; border-radius: 8px; overflow: hidden;
  border: 2px solid transparent; cursor: pointer; padding: 0;
  background: #f0f0f0; transition: border-color 0.2s;
  flex-shrink: 0;
}
.pd-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pd-thumb.active { border-color: #E8572A; }
.pd-thumb:hover  { border-color: #E8572A; }

/* ── 資訊 ── */
.pd-info {}
.pd-tenant { display: flex; align-items: center; gap: 5px; font-size: 14px; color: #888; margin-bottom: 10px; }
.pd-name { font-size: 28px; font-weight: 700; color: #1f2937; margin: 0 0 12px; line-height: 1.3; }
.pd-price { font-size: 28px; font-weight: 700; color: #E8572A; margin-bottom: 16px; }
.pd-description { font-size: 14px; color: #555; line-height: 1.8; margin-bottom: 0; }
.pd-divider { border: none; border-top: 1px solid #eee; margin: 20px 0; }

.pd-field { margin-bottom: 20px; }
.pd-label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px; }
.pd-select {
  width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px;
  font-size: 14px; color: #374151; background: #fff; cursor: pointer;
  appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 14px center;
  padding-right: 36px; outline: none; transition: border-color 0.2s;
}
.pd-select:focus { border-color: #E8572A; box-shadow: 0 0 0 3px rgba(232,87,42,0.1); }

.pd-qty { display: flex; align-items: center; gap: 0; }
.pd-qty-btn {
  width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid #d1d5db;
  background: transparent; display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #374151; transition: all 0.2s;
}
.pd-qty-btn:hover { border-color: #E8572A; color: #E8572A; }
.pd-qty-val { min-width: 56px; text-align: center; font-size: 16px; font-weight: 600; color: #1f2937; }

.pd-total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.pd-total-label { font-size: 16px; font-weight: 600; color: #374151; }
.pd-total-price { font-size: 28px; font-weight: 700; color: #E8572A; }

.pd-cart-btn {
  width: 100%; padding: 14px; border: none; border-radius: 8px;
  background: #E8572A; color: #fff; font-size: 16px; font-weight: 600;
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
  transition: background 0.2s;
}
.pd-cart-btn:hover { background: #d04a20; }
.pd-cart-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── 推薦 ── */
.pd-rec-section { border-top: 1px solid #eee; padding-top: 2.5rem; }
.pd-rec-title { font-size: 20px; font-weight: 700; color: #1f2937; margin: 0 0 1.5rem; }
.pd-rec-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
.pd-rec-card { text-decoration: none; display: block; border-radius: 12px; overflow: hidden; border: 1px solid #eee; background: #fff; transition: box-shadow 0.2s, transform 0.2s; }
.pd-rec-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.09); transform: translateY(-2px); }
.pd-rec-image { width: 100%; aspect-ratio: 4/3; overflow: hidden; background: #f5f5f5; }
.pd-rec-image img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
.pd-rec-card:hover .pd-rec-image img { transform: scale(1.04); }
.pd-rec-img-placeholder { width: 100%; height: 100%; background: #f0f0f0; }
.pd-rec-info { padding: 10px 12px 12px; }
.pd-rec-tenant { font-size: 11px; color: #999; margin: 0 0 3px; }
.pd-rec-name { font-size: 14px; font-weight: 700; color: #222; margin: 0 0 8px; line-height: 1.3; }
.pd-rec-footer { display: flex; justify-content: space-between; align-items: center; }
.pd-rec-price { font-size: 13px; font-weight: 500; color: #E8572A; }
.pd-rec-cart-icon { color: #E8572A; display: flex; }

@media (max-width: 1024px) {
  .pd-rec-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
  .pd-container { padding: 0 1rem; }
  .pd-main { grid-template-columns: 1fr; gap: 1.5rem; }
  .pd-name { font-size: 22px; }
  .pd-price, .pd-total-price { font-size: 22px; }
  .pd-rec-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<script>
var PD_UNIT_PRICE = {{ $price }};
var pdQty = 1;

function pdSetImg(btn) {
  var src = btn.dataset.src;
  var mainImg = document.getElementById('pd-main-img');
  if (mainImg) {
    mainImg.style.opacity = '0';
    setTimeout(function() {
      mainImg.src = src;
      mainImg.style.opacity = '1';
    }, 150);
  }
  document.querySelectorAll('.pd-thumb').forEach(function(t) { t.classList.remove('active'); });
  btn.classList.add('active');
}

function pdChangeQty(delta) {
  pdQty = Math.max(1, pdQty + delta);
  document.getElementById('pd-qty').textContent = pdQty;
  var total = PD_UNIT_PRICE * pdQty;
  document.getElementById('pd-total').textContent = 'NT$ ' + total.toLocaleString();
}

function pdAddToCart() {
  var btn  = document.getElementById('pd-cart-btn');
  var spec = document.getElementById('pd-spec');
  // TODO: 串接購物車 API
  btn.disabled = true;
  btn.textContent = '已加入購物車 ✓';
  setTimeout(function() {
    btn.disabled = false;
    btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> 加入購物車';
  }, 2000);
}
</script>

@endsection
