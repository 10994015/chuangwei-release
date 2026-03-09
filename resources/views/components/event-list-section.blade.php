{{-- resources/views/components/event-list-section.blade.php --}}
@php
    $data       = $frame['data'] ?? [];

    // ── 分類（從 API 的 eventCategories 組成 tab）────────────────
    $apiCategories = $data['eventCategories'] ?? [];
    $categories    = array_merge(
        [['id' => 'all', 'name' => '全部']],
        array_map(fn($c) => ['id' => $c, 'name' => $c], $apiCategories)
    );

    // ── 活動清單（從 eventList.data）────────────────────────────
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

    $perPage  = 3;

    $tagColors = [
        '#E8572A', '#2563eb', '#27a163', '#c2185b', '#e67e00',
        '#7c3aed', '#0891b2', '#be123c', '#15803d', '#b45309',
    ];
    $getTagColor = function(string $tag) use ($tagColors): string {
        $index = abs(crc32($tag)) % count($tagColors);
        return $tagColors[$index];
    };

    // ── 篩選 ──────────────────────────────────────────────────────
    $selectedCategory = request('category', 'all');
    $filteredEvents   = $selectedCategory === 'all'
        ? $eventsList
        : array_values(array_filter($eventsList, fn($e) => in_array($selectedCategory, $e['tags'] ?? [])));

    // ── 分頁 ──────────────────────────────────────────────────────
    $total       = count($filteredEvents);
    $totalPages  = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
    $currentPage = max(1, min((int) request('page', 1), $totalPages));
    $offset      = ($currentPage - 1) * $perPage;
    $pagedEvents = array_slice($filteredEvents, $offset, $perPage);

    // ── 頁碼按鈕 ─────────────────────────────────────────────────
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

<section class="event-list-section">
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
                                <span class="placeholder-text">活動圖片</span>
                            </div>
                        @endif
                    </div>

                    <div class="event-info">
                        @if (!empty($event['tags']))
                            <div class="event-tags">
                                @foreach ($event['tags'] as $tag)
                                    <span class="event-tag" style="background: {{ $getTagColor($tag) }}; color: #fff;">
                                        {{ $tag }}
                                    </span>
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
                <p>此分類目前沒有活動</p>
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
