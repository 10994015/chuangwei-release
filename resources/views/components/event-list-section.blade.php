{{-- resources/views/components/event-list-section.blade.php --}}
@php
    $data       = $frame['data'] ?? [];

    $apiCategories = $data['eventCategories'] ?? [];
    $categories    = array_merge(
        [['id' => 'all', 'name' => __('ui.eventListBasemap.catAll')]],
        array_map(fn($c) => ['id' => $c, 'name' => $c], $apiCategories)
    );

    $rawEvents  = $data['eventList']['data'] ?? [];
    $eventsList = array_map(function($item) {
        $startAt  = $item['startAt'] ?? null;
        $endAt    = $item['endAt']   ?? null;
        $date     = $startAt ? date('Y-m-d', strtotime($startAt)) : '';
        $timeFrom = $startAt ? date('H:i', strtotime($startAt)) : '';
        $timeTo   = $endAt   ? date('H:i', strtotime($endAt))   : '';
        $time     = $timeFrom && $timeTo ? "{$timeFrom} - {$timeTo}" : $timeFrom;

        return [
            'id'       => $item['id']       ?? null,
            'title'    => $item['name']     ?? '',
            'date'     => $date,
            'time'     => $time,
            'location' => $item['location'] ?? '',
            'tags'     => $item['labels']   ?? [],
            'image'    => $item['imgSrc']   ?? null,
            'category' => !empty($item['labels']) ? $item['labels'][0] : '',
        ];
    }, $rawEvents);

    $perPage = 9;

    $getTagClass = function(string $tag): string {
        if ($tag === '熱門') return 'hot';
        if ($tag === '推薦') return 'recommended';
        return 'default';
    };
    // 對應 Vue 版 getTagClass：hot=#dc3545, recommended=#1a73e8, default=#95a5a6

    $selectedCategory = request('category', 'all');
    $keyword          = trim(request('keyword', ''));
    $dateFrom         = request('date_from', '');
    $dateTo           = request('date_to', '');

    $filteredEvents = $eventsList;

    // 分類過濾
    if ($selectedCategory !== 'all') {
        $filteredEvents = array_values(array_filter($filteredEvents, fn($e) => in_array($selectedCategory, $e['tags'] ?? [])));
    }
    // 關鍵字過濾
    if ($keyword !== '') {
        $filteredEvents = array_values(array_filter($filteredEvents, fn($e) => mb_stripos($e['title'], $keyword) !== false));
    }
    // 開始日期過濾
    if ($dateFrom !== '') {
        $filteredEvents = array_values(array_filter($filteredEvents, fn($e) => $e['date'] >= $dateFrom));
    }
    // 結束日期過濾
    if ($dateTo !== '') {
        $filteredEvents = array_values(array_filter($filteredEvents, fn($e) => $e['date'] <= $dateTo));
    }

    $total       = count($filteredEvents);
    $totalPages  = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $perPage;
    $pagedEvents = array_slice($filteredEvents, $offset, $perPage);

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

    $queryBase = array_filter(request()->except(['page']));
@endphp

<section class="event-list-section">
    <div class="container">

        {{-- 搜尋區 --}}
        <form class="el-search-bar" method="GET" action="">
            <input type="hidden" name="locale" value="{{ request('locale', 'ZH-TW') }}" />
            <div class="el-search-row">
                <div class="el-search-field">
                    <label class="el-search-label">{{ __('ui.eventListBasemap.labelKeyword') }}</label>
                    <div class="el-search-input-wrap">
                        <svg class="el-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" name="keyword" class="el-search-input" placeholder="{{ __('ui.eventListBasemap.keywordPlaceholder') }}" value="{{ $keyword }}" />
                    </div>
                </div>
                <div class="el-search-field">
                    <label class="el-search-label">{{ __('ui.eventListBasemap.labelCategory') }}</label>
                    <select name="category" class="el-search-select">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat['id'] }}" {{ $selectedCategory === $cat['id'] ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="el-search-field">
                    <label class="el-search-label">{{ __('ui.eventListBasemap.labelDateFrom') }}</label>
                    <input type="date" name="date_from" class="el-search-input" value="{{ $dateFrom }}" />
                </div>
                <div class="el-search-field">
                    <label class="el-search-label">{{ __('ui.eventListBasemap.labelDateTo') }}</label>
                    <input type="date" name="date_to" class="el-search-input" value="{{ $dateTo }}" />
                </div>
            </div>
            <div class="el-search-actions">
                <button type="submit" class="el-search-btn">{{ __('ui.eventListBasemap.searchBtn') }}</button>
                <a href="{{ url()->current() }}?locale={{ request('locale', 'ZH-TW') }}" class="el-reset-btn">{{ __('ui.eventListBasemap.resetBtn') }}</a>
            </div>
        </form>

        <hr class="divider" />

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

        {{-- 活動 Grid --}}
        <div class="events-grid">
            @foreach ($pagedEvents as $event)
                <div class="event-card">
                    <div class="event-image">
                        @if (!empty($event['image']))
                            <img src="{{ $event['image'] }}" alt="{{ $event['title'] }}" class="image" />
                        @else
                            <div class="image-placeholder">
                                <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="placeholder-icon">
                                    <rect x="8" y="14" width="64" height="48" rx="4" stroke="#bbb" stroke-width="3"/>
                                    <circle cx="28" cy="32" r="7" stroke="#bbb" stroke-width="3"/>
                                    <path d="M8 50l18-16 14 14 10-10 18 18" stroke="#bbb" stroke-width="3" stroke-linejoin="round"/>
                                </svg>
                                <span class="placeholder-text">{{ __('ui.eventsBasemap.imagePlaceholder') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="event-info">
                        @if (!empty($event['tags']))
                            <div class="event-tags">
                                @foreach ($event['tags'] as $tag)
                                    <span class="event-tag {{ $getTagClass($tag) }}">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="tags-placeholder"></div>
                        @endif

                        <h3 class="event-title">{{ $event['title'] }}</h3>

                        <div class="event-details">
                            <div class="event-detail">
                                <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <span>{{ $event['date'] }}</span>
                            </div>
                            <div class="event-detail">
                                <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span>{{ $event['time'] }}</span>
                            </div>
                            <div class="event-detail">
                                <svg class="detail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>{{ $event['location'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 無資料 --}}
        @if (count($filteredEvents) === 0)
            <div class="empty-state">
                <p>{{ __('ui.eventListBasemap.empty') }}</p>
            </div>
        @endif

        {{-- 頁碼 --}}
        @if ($totalPages > 1)
            <div class="pagination">
                @if ($currentPage <= 1)
                    <span class="page-btn page-nav disabled">{{ __('ui.eventListBasemap.prev') }}</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage - 1])) }}"
                       class="page-btn page-nav">{{ __('ui.eventListBasemap.prev') }}</a>
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
                    <span class="page-btn page-nav disabled">{{ __('ui.eventListBasemap.next') }}</span>
                @else
                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($queryBase, ['category' => $selectedCategory, 'page' => $currentPage + 1])) }}"
                       class="page-btn page-nav">{{ __('ui.eventListBasemap.next') }}</a>
                @endif
            </div>
        @endif

    </div>
</section>

<style>
.el-search-bar { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px 24px 16px; margin-bottom: 24px; }
.el-search-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 14px; }
.el-search-field { display: flex; flex-direction: column; gap: 6px; }
.el-search-label { font-size: 13px; font-weight: 500; color: #374151; }
.el-search-input-wrap { position: relative; }
.el-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; color: #9ca3af; pointer-events: none; }
.el-search-input-wrap .el-search-input { padding-left: 32px; }
.el-search-input {
    width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 8px;
    font-size: 14px; color: #374151; box-sizing: border-box; outline: none;
    transition: border-color 0.2s; background: #fff;
}
.el-search-input:focus { border-color: #E8572A; box-shadow: 0 0 0 3px rgba(232,87,42,0.1); }
.el-search-select {
    width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 8px;
    font-size: 14px; color: #374151; box-sizing: border-box; outline: none;
    background: #fff; cursor: pointer; transition: border-color 0.2s;
}
.el-search-select:focus { border-color: #E8572A; }
.el-search-actions { display: flex; gap: 10px; justify-content: flex-end; }
.el-search-btn {
    padding: 9px 24px; background: #E8572A; color: #fff; border: none;
    border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.2s;
}
.el-search-btn:hover { background: #d14a1f; }
.el-reset-btn {
    padding: 9px 20px; background: #f5f5f5; color: #555; border: 1px solid #ddd;
    border-radius: 8px; font-size: 14px; text-decoration: none; transition: background 0.2s;
}
.el-reset-btn:hover { background: #eee; }
@media (max-width: 1024px) {
    .el-search-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .el-search-row { grid-template-columns: 1fr; }
    .el-search-bar { padding: 16px; }
}
</style>
