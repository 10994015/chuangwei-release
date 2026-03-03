{{-- resources/views/components/footer.blade.php --}}
@props([
    'tenantName'    => null,
    'tenantPhone'   => null,
    'tenantAddress' => null,
    'tenantEmail'   => null,
    'brandName'     => null,
    'copyright'     => null,
    'columns'       => [
        ['最新消息', '慶典活動', '商品與服務'],
        ['捐款護持', '關於我們', '集影牆'],
    ],
    'device'        => 'desktop',
])

@php
    $displayName      = $tenantName ?? $brandName ?? '宮廟名稱';
    $displayPhone     = $tenantPhone ?? null;
    $displayAddress   = $tenantAddress ?? null;
    $displayEmail     = $tenantEmail ?? null;
    $displayCopyright = $copyright ?? ('Copyright © ' . date('Y') . ' ' . $displayName . ' | 宮廟');
@endphp

<footer class="footer device-{{ $device }}">
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
                            <h4 class="footer-heading">{{ $item }}</h4>
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
