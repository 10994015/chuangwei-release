{{-- resources/views/components/pv-news-section.blade.php --}}
@php
  $data  = $frame['data'] ?? [];
  $posts = $data['posts'] ?? [];

  $mappedPosts = collect($posts)->map(fn($n) => [
    'id'    => $n['id']        ?? null,
    'tag'   => $n['type']      ?? '祈福活動',
    'title' => $n['title']     ?? '',
    'date'  => isset($n['createdAt']) ? substr($n['createdAt'], 0, 10) : '',
  ])->values()->toArray();
@endphp

<section class="pv-news-section">
  <div class="pv-news-container">

    <div class="pv-section-header">
      <div class="pv-header-left">
        <h2 class="pv-section-title">{{ __('ui.newsBasemap.title') }}</h2>
        <p class="pv-section-subtitle">{{ __('ui.newsBasemap.subtitle') }}</p>
      </div>
      <a href="/news" class="pv-view-all">{{ __('ui.newsBasemap.viewAllShort') }}</a>
    </div>

    <div class="pv-news-list">
      @forelse($mappedPosts as $news)
        <div class="pv-news-item">
          <div class="pv-news-tag">{{ $news['tag'] }}</div>
          <div class="pv-news-title">{{ $news['title'] }}</div>
          <div class="pv-news-date">{{ $news['date'] }}</div>
        </div>
      @empty
        <div class="pv-news-empty">{{ __('ui.newsBasemap.empty') }}</div>
      @endforelse
    </div>

  </div>
</section>

<style>
.pv-news-section {
  padding: 3.5rem 0;
  background: transparent;
}
.pv-news-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 3rem;
}
.pv-section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 2rem;
  padding-bottom: 1.25rem;
  border-bottom: 1px solid var(--frame-border-color, #e5e5e5);
}
.pv-header-left { display: flex; flex-direction: column; gap: 4px; }
.pv-section-title {
  font-size: 26px;
  font-weight: 700;
  color: var(--frame-heading-color, #222);
  margin: 0;
  line-height: 1.2;
}
.pv-section-subtitle {
  font-size: 13px;
  color: var(--frame-text-muted, #999);
  margin: 0;
}
.pv-view-all {
  padding: 8px 20px;
  border: 1.5px solid var(--frame-border-color, #ddd);
  border-radius: 20px;
  font-size: 14px;
  color: var(--frame-text-color, #444);
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s;
  flex-shrink: 0;
}
.pv-view-all:hover { border-color: #E8572A; color: #E8572A; }
.pv-news-list { display: flex; flex-direction: column; }
.pv-news-item {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.1rem 0;
  border-bottom: 1px solid var(--frame-border-color, #f0f0f0);
  cursor: pointer;
  transition: background 0.15s;
}
.pv-news-item:first-child { border-top: 1px solid var(--frame-border-color, #f0f0f0); }
.pv-news-item:hover .pv-news-title { color: #E8572A; }
.pv-news-tag {
  flex-shrink: 0;
  padding: 5px 14px;
  border-radius: 20px;
  background: #E8572A;
  color: #fff;
  font-size: 13px;
  font-weight: 500;
  white-space: nowrap;
}
.pv-news-title {
  flex: 1;
  font-size: 16px;
  font-weight: 500;
  color: var(--frame-text-color, #333);
  transition: color 0.2s;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.pv-news-date {
  flex-shrink: 0;
  font-size: 14px;
  color: var(--frame-text-muted, #aaa);
  white-space: nowrap;
}
.pv-news-empty {
  padding: 2rem 0;
  text-align: center;
  color: var(--frame-text-muted, #aaa);
  font-size: 14px;
}

@media (min-width: 769px) and (max-width: 1024px) {
  .pv-news-section    { padding: 2.5rem 0; }
  .pv-news-container  { padding: 0 1.5rem; }
  .pv-section-title   { font-size: 22px; }
  .pv-news-title      { font-size: 15px; }
}
@media (max-width: 768px) {
  .pv-news-section   { padding: 2rem 0; }
  .pv-news-container { padding: 0 1rem; }
  .pv-section-header { flex-direction: column; align-items: flex-start; gap: 12px; }
  .pv-section-title  { font-size: 20px; }
  .pv-news-item      { flex-wrap: wrap; gap: 0.5rem 1rem; }
  .pv-news-title     { font-size: 14px; flex-basis: 100%; order: 2; }
  .pv-news-tag       { order: 1; font-size: 12px; padding: 4px 10px; }
  .pv-news-date      { order: 3; font-size: 12px; flex-basis: 100%; }
}
</style>
