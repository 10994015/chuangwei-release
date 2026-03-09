{{-- resources/views/components/news-section.blade.php --}}
@php
    $data     = $frame['data'] ?? [];
    $rawPosts = $data['posts'] ?? [];

    $tagColors   = ['#E8572A','#2563eb','#27a163','#c2185b','#e67e00','#7c3aed','#0891b2','#be123c','#15803d','#b45309'];
    $getTagColor = fn(string $tag): string => $tagColors[abs(crc32($tag)) % count($tagColors)];

    $newsList = array_map(fn($item) => [
        'id'          => $item['id']      ?? null,
        'tag'         => $item['type']    ?? '',
        'title'       => $item['title']   ?? '',
        'description' => $item['content'] ?? '',
        'date'        => isset($item['createdAt'])
                            ? date('Y-m-d', strtotime($item['createdAt']))
                            : '',
    ], $rawPosts);

    $device     = $device ?? 'desktop';
    $templeId   = $templeId ?? '';
    $viewAllUrl = $templeId ? "/site/{$templeId}/news" : '#';
@endphp

<section class="news-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">最新消息</h2>
            @if (!empty($newsList))
                <a href="{{ $viewAllUrl }}" class="view-all">查看所有消息 ›</a>
            @endif
        </div>

        <div class="news-list">
            @forelse ($newsList as $news)
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
            @empty
            <div class="empty-state">
                <p>目前尚無消息</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
