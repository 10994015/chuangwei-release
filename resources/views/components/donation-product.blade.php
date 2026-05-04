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
    // ── 呼叫 /api/product/temple/donation ────────────────────
    $donationApiData = [];
    try {
        $host      = request()->getHost();
        $parts     = explode('.', $host);
        $isLocal   = $host === 'localhost' || str_ends_with($host, '.localhost');
        $subdomain = (count($parts) >= 3) ? $parts[0]
                   : ((count($parts) === 2 && $parts[1] === 'localhost') ? $parts[0] : '');
        if ($isLocal && $subdomain) $subdomain = env('API_SUBDOMAIN', $subdomain);
        $apiBase   = $subdomain
            ? rtrim('https://' . $subdomain . '.' . config('api.base_domain'), '/')
            : rtrim(config('api.base_url', env('API_BASE_URL', '')), '/');


        $donationRes = \Illuminate\Support\Facades\Http::withOptions(['cookies' => false])
            ->withHeaders(['Cookie' => request()->header('Cookie', '')])
            ->get($apiBase . '/api/product/temple/donation', [
                'page'     => 1,
                'pageSize' => 20,
            ]);



        if ($donationRes->status() === 200) {
            $donationApiData = $donationRes->json('data.data') ?? [];
        }
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('[donation-product] API error: ' . $e->getMessage());
    }
    // ─────────────────────────────────────────────────────────

    // 從 API 建立捐款項目下拉 & 各商品圖片 map
    $itemImagesMap = []; // { id => [url, ...] }
    if (!empty($donationApiData)) {
        $items = array_map(fn($d) => [
            'label' => $d['nameZhTw'] ?? '',
            'value' => $d['id']       ?? '',
        ], $donationApiData);

        $itemMinPriceMap = []; // { id => minPrice }
        foreach ($donationApiData as $d) {
            $id      = $d['id'] ?? '';
            $cover   = $d['coverImg'] ?? null;
            $imgUrls = array_values(array_filter(
                array_map(fn($img) => $img['url'] ?? null, $d['imgs'] ?? [])
            ));
            $allImgs = $cover ? array_unique(array_merge([$cover], $imgUrls)) : $imgUrls;
            $itemImagesMap[$id] = array_values($allImgs);

            $mp = null;
            foreach ($d['skus'] ?? [] as $sku) {
                $p = isset($sku['price']) ? (float)$sku['price'] : null;
                if ($p !== null && ($mp === null || $p < $mp)) $mp = $p;
            }
            $itemMinPriceMap[$id] = $mp;
        }

        // 預設圖片 & 最低金額取第一筆
        $firstId  = $donationApiData[0]['id'] ?? '';
        $images   = $itemImagesMap[$firstId] ?? [];
        $minPrice = null;
        foreach ($donationApiData[0]['skus'] ?? [] as $sku) {
            $p = isset($sku['price']) ? (float)$sku['price'] : null;
            if ($p !== null && ($minPrice === null || $p < $minPrice)) $minPrice = $p;
        }
        if ($minPrice !== null) $minAmount = (int)$minPrice;
    }

    // 確保 images 最多取 3 張縮圖用
    $imageList = array_values((array) $images);
@endphp

<div
    class="donation-product-basemap"
    x-data="{
        selectedThumb: 0,
        selectedItem: '{{ $items[0]['value'] ?? '' }}',
        donationAmount: null,
        amountError: '',
        minAmount: {{ $minAmount }},
        images: {{ json_encode(array_map(fn($img) => is_array($img) ? ($img['src'] ?? null) : $img, $imageList)) }},
        itemImagesMap: {{ json_encode($itemImagesMap) }},
        itemMinPriceMap: {{ json_encode($itemMinPriceMap ?? []) }},

        onItemChange() {
            this.images = this.itemImagesMap[this.selectedItem] ?? [];
            this.selectedThumb = 0;
            this.thumbOffset = 0;
            const mp = this.itemMinPriceMap[this.selectedItem];
            if (mp != null) this.minAmount = mp;
        },
        get mainImage() {
            return this.images[this.selectedThumb] ?? null
        },
        thumbOffset: 0,
        get visibleThumbs() {
            return this.images.slice(this.thumbOffset, this.thumbOffset + 3);
        },
        get canThumbPrev() { return this.thumbOffset > 0; },
        get canThumbNext() { return this.thumbOffset + 3 < this.images.length; },
        thumbPrev() { if (this.canThumbPrev) this.thumbOffset--; },
        thumbNext() { if (this.canThumbNext) this.thumbOffset++; },
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
            <div class="thumbnail-row-wrapper" x-show="images.length > 0">
                <button class="thumb-nav-btn thumb-prev" @click="thumbPrev()" :disabled="!canThumbPrev">&#8249;</button>
                <div class="thumbnail-row">
                    <template x-for="(img, index) in visibleThumbs" :key="thumbOffset + index">
                        <div
                            class="thumbnail-item"
                            :class="{ active: selectedThumb === thumbOffset + index }"
                            @click="selectedThumb = thumbOffset + index"
                        >
                            <img :src="img" :alt="`縮圖 ${thumbOffset + index + 1}`" class="thumb-img" />
                        </div>
                    </template>
                </div>
                <button class="thumb-nav-btn thumb-next" @click="thumbNext()" :disabled="!canThumbNext">&#8250;</button>
            </div>
        </div>

        {{-- 右側：捐款表單 --}}
        <div class="form-section">

            {{-- 捐款項目 --}}
            <div class="form-group">
                <label class="form-label">捐款項目</label>
                <select class="form-select" x-model="selectedItem" @change="onItemChange()">
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
