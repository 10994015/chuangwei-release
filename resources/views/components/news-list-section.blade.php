{{-- resources/views/components/news-list-section.blade.php --}}
@php
    $data          = $frame['data'] ?? [];

    // ── 分類（從 API 的 postCategories 組成 tab）─────────────────
    $apiCategories = $data['postCategories'] ?? [];
    $categories    = array_merge(
        [['id' => 'all', 'name' => '全部']],
        array_map(fn($c) => ['id' => $c, 'name' => $c], $apiCategories)
    );

    // ── 消息清單（從 postList.data）──────────────────────────────
    $rawNews  = $data['postList']['data'] ?? [];
    $newsList = array_map(fn($item) => [
        'id'          => $item['id']        ?? null,
        'tag'         => $item['type']      ?? '',
        'title'       => $item['title']     ?? '',
        'description' => $item['content']   ?? '',
        'date'        => isset($item['createdAt'])
                            ? date('Y-m-d', strtotime($item['createdAt']))
                            : '',
    ], $rawNews);

    // ── tag 顏色（hash 自動產生）────────────────────────────────
    $tagColors   = ['#E8572A','#2563eb','#27a163','#c2185b','#e67e00','#7c3aed','#0891b2','#be123c','#15803d','#b45309'];
    $getTagColor = fn(string $tag): string => $tagColors[abs(crc32($tag)) % count($tagColors)];

    $pageSize = 5;

    // ── 篩選 ─────────────────────────────────────────────────────
    $selectedCategory = request('category', 'all');
    $filteredNews     = $selectedCategory === 'all'
        ? $newsList
        : array_values(array_filter($newsList, fn($n) => ($n['tag'] ?? '') === $selectedCategory));

    // ── 分頁 ─────────────────────────────────────────────────────
    $total       = count($filteredNews);
    $totalPages  = $pageSize > 0 ? (int) ceil($total / $pageSize) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $pageSize;
    $pagedNews   = array_slice($filteredNews, $offset, $pageSize);

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

<section class="news-list-section">
    <div class="container">

        {{-- 分類 Tab --}}
        <div class="filter-bar">
            @foreach ($categories as $cat)
                <a
                    href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $cat['id'], 'page' => 1])) }}"
                    class="filter-btn {{ $selectedCategory === $cat['id'] ? 'active' : '' }}"
                >{{ $cat['name'] }}</a>
            @endforeach
        </div>

        <hr class="divider" />

        {{-- 消息列表 --}}
        <div class="news-list">
            @foreach ($pagedNews as $news)
                <div class="news-item">
                    <div class="news-tag"
                         style="background: {{ $getTagColor($news['tag']) }}; color: #fff;">
                        {{ $news['tag'] }}
                    </div>
                    <div class="news-content">
                        <h3 class="news-title">{{ $news['title'] }}</h3>
                        <p class="news-description">{{ $news['description'] }}</p>
                    </div>
                    <div class="news-date">{{ $news['date'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- 無資料 --}}
        @if (count($filteredNews) === 0)
            <div class="empty-state">
                <p>此分類目前沒有消息</p>
            </div>
        @endif

        {{-- 頁碼 --}}
        @if ($totalPages > 1)
            <div class="pagination">
                @if ($currentPage <= 1)
                    <span class="page-btn page-nav disabled">上一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage - 1])) }}"
                       class="page-btn page-nav">上一頁</a>
                @endif

                @foreach ($pageNumbers as $page)
                    @if ($page === '...')
                        <span class="page-ellipsis">...</span>
                    @else
                        <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $page])) }}"
                           class="page-btn {{ $currentPage == $page ? 'active' : '' }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($currentPage >= $totalPages)
                    <span class="page-btn page-nav disabled">下一頁</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage + 1])) }}"
                       class="page-btn page-nav">下一頁</a>
                @endif
            </div>
        @endif

    </div>
</section>
