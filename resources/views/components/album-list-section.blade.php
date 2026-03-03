{{-- resources/views/components/album-list-section.blade.php --}}
@props([
    'albumList'       => [],
    'albumCategories' => [],
    'device'          => 'desktop',
    'perPage'         => 3,
])

@php
    // ==================== 安全取得陣列 ====================
    $mockAlbums = [
        ['id' => 1, 'coverImage' => null, 'tag' => '法會活動', 'date' => '2024-08-22', 'title' => '中元普渡法會',   'description' => '2024年中元普渡法會活動紀錄'],
        ['id' => 2, 'coverImage' => null, 'tag' => '慶典紀錄', 'date' => '2024-03-19', 'title' => '觀音佛誕慶典',   'description' => '觀音佛誕慶祝活動'],
        ['id' => 3, 'coverImage' => null, 'tag' => '法會活動', 'date' => '2024-02-10', 'title' => '新春祈福法會',   'description' => '農曆新年祈福法會活動照片'],
        ['id' => 4, 'coverImage' => null, 'tag' => '建築風光', 'date' => '2024-01-05', 'title' => '廟宇建築之美',   'description' => '本廟建築細節與風光記錄'],
        ['id' => 5, 'coverImage' => null, 'tag' => '志工服務', 'date' => '2023-12-25', 'title' => '歲末志工活動',   'description' => '年底志工服務暨感恩活動'],
        ['id' => 6, 'coverImage' => null, 'tag' => '慶典紀錄', 'date' => '2023-11-15', 'title' => '建廟週年慶典',   'description' => '建廟週年慶典精彩花絮'],
    ];

    // 支援 { data: [...] } 分頁物件 或 純陣列
    if (is_array($albumList) && isset($albumList['data']) && is_array($albumList['data'])) {
        $albums = count($albumList['data']) > 0 ? $albumList['data'] : $mockAlbums;
    } elseif (is_array($albumList) && count($albumList) > 0) {
        $albums = $albumList;
    } else {
        $albums = $mockAlbums;
    }

    // ==================== 分類 Tab ====================
    $baseCategory = [['label' => '全部', 'value' => 'all']];

    if (count($albumCategories) > 0) {
        $catItems = array_map(fn($c) => [
            'label' => $c['label'] ?? $c['name'] ?? $c,
            'value' => $c['value'] ?? $c['id'] ?? $c,
        ], $albumCategories);
    } else {
        // 從資料自動抽取 tag
        $tags = array_unique(array_filter(array_column($albums, 'tag')));
        $catItems = array_map(fn($t) => ['label' => $t, 'value' => $t], array_values($tags));
    }

    $categories = array_merge($baseCategory, $catItems);

    // ==================== 篩選（由 query string 或預設 all）====================
    $activeCategory = request('category', 'all');

    $filteredAlbums = $activeCategory === 'all'
        ? $albums
        : array_values(array_filter($albums, fn($a) => ($a['tag'] ?? '') === $activeCategory));

    // ==================== 分頁 ====================
    $total       = count($filteredAlbums);
    $totalPages  = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $perPage;
    $pagedAlbums = array_slice($filteredAlbums, $offset, $perPage);

    // 頁碼按鈕（含省略號邏輯）
    $pageNumbers = [];
    if ($totalPages <= 7) {
        $pageNumbers = range(1, $totalPages);
    } elseif ($currentPage <= 4) {
        $pageNumbers = [1, 2, 3, 4, 5, '...', $totalPages];
    } elseif ($currentPage >= $totalPages - 3) {
        $pageNumbers = [1, '...', $totalPages-4, $totalPages-3, $totalPages-2, $totalPages-1, $totalPages];
    } else {
        $pageNumbers = [1, '...', $currentPage-1, $currentPage, $currentPage+1, '...', $totalPages];
    }

    // 保留其他 query string，方便分頁 / 分類切換時合併
    $queryBase = array_filter(request()->except(['page', 'category']));
@endphp

<section class="album-list-section device-{{ $device }}">
    <div class="container">

        {{-- 分類 Tab --}}
        <div class="filter-bar">
            @foreach ($categories as $cat)
                <a
                    href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $cat['value'], 'page' => 1])) }}"
                    class="filter-btn {{ $activeCategory == $cat['value'] ? 'active' : '' }}"
                >
                    {{ $cat['label'] }}
                </a>
            @endforeach
        </div>

        {{-- 分隔線 --}}
        <hr class="divider" />

        {{-- 相簿 Grid --}}
        <div class="album-grid">
            @foreach ($pagedAlbums as $album)
                <div class="album-card">
                    {{-- 封面圖 --}}
                    <div class="album-cover-wrap">
                        <div class="album-cover">
                            @if (!empty($album['coverImage']))
                                <img
                                    src="{{ $album['coverImage'] }}"
                                    alt="{{ $album['title'] }}"
                                    class="cover-image"
                                />
                            @else
                                <div class="cover-placeholder">
                                    <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="4" y="8" width="48" height="36" rx="3" stroke="#bbb" stroke-width="2.5"/>
                                        <circle cx="18" cy="22" r="4" stroke="#bbb" stroke-width="2.5"/>
                                        <path d="M4 36l13-13 9 10 7-8 12 13" stroke="#bbb" stroke-width="2.5" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="placeholder-text">相簿封面</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 資訊 --}}
                    <div class="album-info">
                        <div class="meta-row">
                            @if (!empty($album['tag']))
                                <span class="tag">{{ $album['tag'] }}</span>
                            @endif
                            @if (!empty($album['date']))
                                <span class="date">{{ $album['date'] }}</span>
                            @endif
                        </div>
                        <h3 class="album-title">{{ $album['title'] }}</h3>
                        @if (!empty($album['description']))
                            <p class="album-description">{{ $album['description'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 無資料 --}}
        @if (count($filteredAlbums) === 0)
            <div class="empty-state">
                <p>此分類目前沒有相簿</p>
            </div>
        @endif

        {{-- 頁碼 --}}
        @if ($totalPages > 1)
            <div class="pagination">
                {{-- 上一頁 --}}
                @if ($currentPage <= 1)
                    <span class="page-btn page-nav disabled">上一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $activeCategory, 'page' => $currentPage - 1])) }}"
                       class="page-btn page-nav">上一頁</a>
                @endif

                {{-- 頁碼按鈕 --}}
                @foreach ($pageNumbers as $page)
                    @if ($page === '...')
                        <span class="page-ellipsis">...</span>
                    @else
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $activeCategory, 'page' => $page])) }}"
                           class="page-btn {{ $currentPage == $page ? 'active' : '' }}">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- 下一頁 --}}
                @if ($currentPage >= $totalPages)
                    <span class="page-btn page-nav disabled">下一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $activeCategory, 'page' => $currentPage + 1])) }}"
                       class="page-btn page-nav">下一頁</a>
                @endif
            </div>
        @endif

    </div>
</section>
