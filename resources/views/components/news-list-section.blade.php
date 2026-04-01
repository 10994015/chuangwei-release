{{-- resources/views/components/news-list-section.blade.php --}}
@php
    $data          = $frame['data'] ?? [];

    $apiCategories = $data['postCategories'] ?? [];
    $categories    = array_merge(
        [['id' => 'all', 'name' => __('ui.newsListBasemap.catAll')]],
        array_map(fn($c) => ['id' => $c, 'name' => $c], $apiCategories)
    );

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

    $pageSize = 5;

    $selectedCategory = request('category', 'all');
    $filteredNews     = $selectedCategory === 'all'
        ? $newsList
        : array_values(array_filter($newsList, fn($n) => ($n['tag'] ?? '') === $selectedCategory));

    $total       = count($filteredNews);
    $totalPages  = $pageSize > 0 ? (int) ceil($total / $pageSize) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $pageSize;
    $pagedNews   = array_slice($filteredNews, $offset, $pageSize);

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
                    <div class="news-tag notice">{{ $news['tag'] }}</div>
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
                <p>{{ __('ui.newsListBasemap.empty') }}</p>
            </div>
        @endif

        {{-- 頁碼 --}}
        @if ($totalPages > 1)
            <div class="pagination">
                @if ($currentPage <= 1)
                    <span class="page-btn page-nav disabled">{{ __('ui.newsListBasemap.prev') }}</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage - 1])) }}"
                       class="page-btn page-nav">{{ __('ui.newsListBasemap.prev') }}</a>
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
                    <span class="page-btn page-nav disabled">{{ __('ui.newsListBasemap.next') }}</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage + 1])) }}"
                       class="page-btn page-nav">{{ __('ui.newsListBasemap.next') }}</a>
                @endif
            </div>
        @endif

    </div>
</section>
