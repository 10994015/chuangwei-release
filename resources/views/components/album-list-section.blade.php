{{-- resources/views/components/album-list-section.blade.php --}}
@php
    $data          = $frame['data'] ?? [];

    // ── 分類（從 API 的 albumCategories 組成 tab）────────────────
    $apiCategories = $data['albumCategories'] ?? [];
    $categories    = array_merge(
        [['label' => '全部', 'value' => 'all']],
        array_map(fn($c) => ['label' => $c, 'value' => $c], $apiCategories)
    );

    // ── 相簿清單（從 albumList.data）─────────────────────────────
    $rawAlbums = $data['albumList']['data'] ?? [];
    $albums    = array_map(fn($item) => [
        'id'          => $item['id']          ?? null,
        'coverImage'  => $item['imgSrc']      ?? null,
        'tag'         => $item['category']    ?? '',
        'title'       => $item['title']       ?? '',
        'description' => $item['description'] ?? '',
        'date'        => isset($item['createdAt'])
                            ? date('Y-m-d', strtotime($item['createdAt']))
                            : '',
    ], $rawAlbums);

    $perPage = 3;
    // ── 篩選 ─────────────────────────────────────────────────────
    $activeCategory = request('category', 'all');
    $filteredAlbums = $activeCategory === 'all'
        ? $albums
        : array_values(array_filter($albums, fn($a) => ($a['tag'] ?? '') === $activeCategory));

    // ── 分頁 ─────────────────────────────────────────────────────
    $total       = count($filteredAlbums);
    $totalPages  = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $perPage;
    $pagedAlbums = array_slice($filteredAlbums, $offset, $perPage);

    // ── 頁碼 ─────────────────────────────────────────────────────
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

    $queryBase = array_filter(request()->except(['page', 'category']));
@endphp

<section class="album-list-section">
    <div class="container">

        {{-- 分類 Tab --}}
        <div class="filter-bar">
            @foreach ($categories as $cat)
                <a
                    href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $cat['value'], 'page' => 1])) }}"
                    class="filter-btn {{ $activeCategory == $cat['value'] ? 'active' : '' }}"
                >{{ $cat['label'] }}</a>
            @endforeach
        </div>

        <hr class="divider" />

        {{-- 相簿 Grid --}}
        <div class="album-grid">
            @foreach ($pagedAlbums as $album)
                <div class="album-card">
                    <div class="album-cover-wrap">
                        <div class="album-cover">
                            @if (!empty($album['coverImage']))
                                <img src="{{ $album['coverImage'] }}" alt="{{ $album['title'] }}" class="cover-image" />
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
                @if ($currentPage <= 1)
                    <span class="page-btn page-nav disabled">上一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $activeCategory, 'page' => $currentPage - 1])) }}"
                       class="page-btn page-nav">上一頁</a>
                @endif

                @foreach ($pageNumbers as $page)
                    @if ($page === '...')
                        <span class="page-ellipsis">...</span>
                    @else
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $activeCategory, 'page' => $page])) }}"
                           class="page-btn {{ $currentPage == $page ? 'active' : '' }}">{{ $page }}</a>
                    @endif
                @endforeach

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
