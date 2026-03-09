{{-- resources/views/components/footer.blade.php --}}
@php
    $tenantName    = $tenantName    ?? null;
    $tenantPhone   = $tenantPhone   ?? null;
    $tenantAddress = $tenantAddress ?? null;
    $tenantEmail   = $tenantEmail   ?? null;
    $brandName     = $brandName     ?? null;
    $copyright     = $copyright     ?? null;
    $columns       = $columns       ?? [
        [
            ['name' => '最新消息', 'slug' => 'news'],
            ['name' => '慶典活動', 'slug' => 'events'],
            ['name' => '商品與服務', 'slug' => 'products'],
        ],
        [
            ['name' => '捐款護持', 'slug' => 'donate'],
            ['name' => '關於我們', 'slug' => 'about-us'],
            ['name' => '集影牆',   'slug' => 'gallery'],
        ],
    ];
    $device        = $device        ?? 'desktop';
    $tid           = $templeId      ?? '';

    $displayName      = $tenantName ?? $brandName ?? '宮廟名稱';
    $displayPhone     = $tenantPhone;
    $displayAddress   = $tenantAddress;
    $displayEmail     = $tenantEmail;
    $displayCopyright = $copyright ?? ('Copyright © ' . date('Y') . ' ' . $displayName . ' | 宮廟');

    // 對應 Vue 的 footerStyle：
    // 優先用直接傳入的 $footerBgColor / $footerTextColor，
    // 其次從 $frame['data'] 讀（跟 page.blade.php 邏輯一致）
    $frameData       = $frame['data'] ?? [];
    $footerBgColor   = $footerBgColor   ?? $frameData['footerBgColor']   ?? null;
    $footerTextColor = $footerTextColor ?? $frameData['footerTextColor'] ?? null;

    // 組 inline style，直接注入到 <footer> 元素本身
    $footerInlineStyle = '';
    if ($footerBgColor)   $footerInlineStyle .= "background: {$footerBgColor}; ";
    if ($footerTextColor) $footerInlineStyle .= "--footer-text-color: {$footerTextColor}; ";
@endphp

<footer
    class="footer device-{{ $device }}"
    @if ($footerInlineStyle) style="{{ $footerInlineStyle }}" @endif
>
    <div class="footer-container">
        <div class="footer-content">

            {{-- 品牌名稱 --}}
            <div class="footer-column brand-column">
                <h3 class="footer-title">{{ $displayName }}</h3>
            </div>

            {{-- 連結欄 --}}
            <div class="footer-links-wrapper">
                @foreach ($columns as $column)
                    <div class="footer-column">
                        @foreach ($column as $item)
                            @php
                                $itemName = is_array($item) ? ($item['name'] ?? '') : $item;
                                $itemSlug = is_array($item) ? ($item['slug'] ?? '') : '';
                                $itemUrl  = ($tid && $itemSlug) ? "/site/{$tid}/{$itemSlug}" : '#';
                            @endphp
                            <a href="{{ $itemUrl }}" class="footer-heading">{{ $itemName }}</a>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- 聯絡資訊 --}}
            <div class="footer-column contact-column">
                @if ($displayPhone)
                    <p class="footer-contact">📞 {{ $displayPhone }}</p>
                @endif
                @if ($displayAddress)
                    <p class="footer-contact">📍 {{ $displayAddress }}</p>
                @endif
                @if ($displayEmail)
                    <p class="footer-contact">✉️ {{ $displayEmail }}</p>
                @endif
            </div>

        </div>

        <div class="footer-bottom">
            <p>{{ $displayCopyright }}</p>
        </div>
    </div>
</footer>
