{{-- resources/views/components/donation-product.blade.php --}}
@props([
    'images'     => [],
    'items'      => [
        ['label' => '一般捐款', 'value' => 'general'],
        ['label' => '助建基金', 'value' => 'fund'],
        ['label' => '慈善專案', 'value' => 'charity'],
        ['label' => '祭典活動', 'value' => 'festival'],
    ],
    'minAmount'  => 100,
    'hasReceipt' => true,
    'device'     => 'desktop',
])

@php
    // 確保 images 最多取 3 張縮圖用
    $imageList = array_values((array) $images);
@endphp

<div
    class="donation-product-basemap device-{{ $device }}"
    x-data="{
        selectedThumb: 0,
        selectedItem: '{{ $items[0]['value'] ?? 'general' }}',
        donationAmount: null,
        amountError: '',
        minAmount: {{ $minAmount }},
        images: {{ json_encode(array_map(fn($img) => is_array($img) ? ($img['src'] ?? null) : $img, $imageList)) }},

        get mainImage() {
            return this.images[this.selectedThumb] ?? null
        },
        get thumbnails() {
            return [0, 1, 2].map(i => this.images[i] ?? null)
        },
        get formattedTotal() {
            if (!this.donationAmount || this.donationAmount <= 0) return '0'
            return Number(this.donationAmount).toLocaleString()
        },
        get canDonate() {
            return this.donationAmount &&
                   this.donationAmount >= this.minAmount &&
                   !this.amountError
        },
        validateAmount() {
            if (!this.donationAmount) { this.amountError = ''; return }
            this.amountError = this.donationAmount < this.minAmount
                ? `最低捐款金額為 NT$${this.minAmount}`
                : ''
        },
        handleDonate() {
            if (!this.canDonate) return
            console.log('捐款:', { item: this.selectedItem, amount: this.donationAmount })
        }
    }"
>
    <div class="donation-container">

        {{-- 左側：圖片區 --}}
        <div class="image-section">
            {{-- 主圖 --}}
            <div class="main-image">
                <template x-if="mainImage">
                    <img :src="mainImage" alt="捐款商品主圖" class="main-img" />
                </template>
                <div x-show="!mainImage" class="image-placeholder main-placeholder"></div>
            </div>

            {{-- 縮圖列 --}}
            <div class="thumbnail-row">
                <template x-for="(img, index) in thumbnails" :key="index">
                    <div
                        class="thumbnail-item"
                        :class="{ active: selectedThumb === index }"
                        @click="selectedThumb = index"
                    >
                        <template x-if="img">
                            <img :src="img" :alt="`縮圖 ${index + 1}`" class="thumb-img" />
                        </template>
                        <div x-show="!img" class="image-placeholder"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- 右側：捐款表單 --}}
        <div class="form-section">

            {{-- 捐款項目 --}}
            <div class="form-group">
                <label class="form-label">捐款項目</label>
                <select class="form-select" x-model="selectedItem">
                    @foreach ($items as $item)
                        <option value="{{ $item['value'] }}">{{ $item['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 電子捐款收據 --}}
            @if ($hasReceipt)
                <div class="receipt-group">
                    <p class="receipt-hint">購買此規格將提供</p>
                    <div class="receipt-badge">電子捐款收據</div>
                </div>
            @endif

            {{-- 捐款金額 --}}
            <div class="form-group">
                <label class="form-label">捐款金額</label>
                <div class="amount-input-wrapper">
                    <span class="currency-label">NT$</span>
                    <input
                        type="number"
                        class="amount-input"
                        x-model.number="donationAmount"
                        placeholder="請輸入捐款金額（最低 NT${{ $minAmount }}）"
                        min="{{ $minAmount }}"
                        @input="validateAmount"
                    />
                </div>
                <p x-show="amountError" x-text="amountError" class="amount-error"></p>
            </div>

            {{-- 總計 --}}
            <div class="total-row">
                <span class="total-label">總計</span>
                <span class="total-amount">NT$ <span x-text="formattedTotal"></span></span>
            </div>

            {{-- 立即捐款按鈕 --}}
            <button
                class="donate-btn"
                :disabled="!canDonate"
                @click="handleDonate"
            >
                立即捐款
            </button>

        </div>
    </div>
</div>
