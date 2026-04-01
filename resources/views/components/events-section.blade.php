{{-- resources/views/components/events-section.blade.php --}}
@php
    $data      = $frame['data'] ?? [];
    $rawEvents = $data['events'] ?? [];

    $getTagClass = function(string $tag): string {
        if ($tag === '熱門') return 'hot';
        if ($tag === '推薦') return 'recommended';
        if ($tag === '重要') return 'important';
        if ($tag === '祭典') return 'ceremony';
        return 'default';
    };

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
        ];
    }, $rawEvents);

    $displayEvents = array_slice($eventsList, 0, 3);

    $device     = $device ?? 'desktop';
    $templeId   = $templeId ?? '';
    $viewAllUrl = $templeId ? "/site/{$templeId}/events" : '#';
@endphp

<section class="events-section">
    <div class="container">

        {{-- 標題列 --}}
        <div class="section-header">
            <h2 class="section-title">{{ __('ui.eventsBasemap.title') }}</h2>
        </div>

        {{-- 活動 Grid — 固定 3 筆 --}}
        <div class="events-grid">
            @forelse ($displayEvents as $event)
                <div class="event-card">

                    {{-- 圖片區 --}}
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

                    {{-- 資訊區 --}}
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
            @empty
                <div class="empty-state">
                    <p>{{ __('ui.eventsBasemap.empty') }}</p>
                </div>
            @endforelse
        </div>

        {{-- 查看更多 --}}
        @if (!empty($displayEvents))
            <div class="view-more-wrap">
                <a href="{{ $viewAllUrl }}" class="view-more-btn">{{ __('ui.eventsBasemap.viewMore') }}</a>
            </div>
        @endif

    </div>
</section>
