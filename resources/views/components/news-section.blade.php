{{-- resources/views/components/news-section.blade.php --}}
@php
    $data     = $frame['data'] ?? [];
    $rawPosts = $data['posts'] ?? [];

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
            <h2 class="section-title">{{ __('ui.newsBasemap.title') }}</h2>
            @if (!empty($newsList))
                <a href="{{ $viewAllUrl }}" class="view-all">{{ __('ui.newsBasemap.viewAll') }}</a>
            @endif
        </div>

        <div class="news-list">
            @forelse ($newsList as $news)
                <div class="news-item">
                    <div class="news-tag notice">{{ $news['tag'] }}</div>
                    <div class="news-content">
                        <h3 class="news-title">{{ $news['title'] }}</h3>
                        <p class="news-description">{{ $news['description'] }}</p>
                    </div>
                    <div class="news-date">{{ $news['date'] }}</div>
                </div>
            @empty
                <div class="empty-state">
                    <p>{{ __('ui.newsBasemap.empty') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
