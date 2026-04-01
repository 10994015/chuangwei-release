{{-- resources/views/components/pv-news-list-section.blade.php --}}
@php
  $data           = $frame['data'] ?? [];
  $postCategories = $data['postCategories'] ?? [];
  $postList       = $data['postList']       ?? [];
  $pageSize       = $data['pageSize']       ?? 7;

  $rawPosts = $postList['data'] ?? $postList ?? [];

  $mappedPosts = collect($rawPosts)->map(fn($n) => [
    'id'    => $n['id']        ?? null,
    'tag'   => $n['type']      ?? '',
    'title' => $n['title']     ?? '',
    'date'  => isset($n['createdAt']) ? substr($n['createdAt'], 0, 10) : '',
  ])->values()->toArray();

  // 分類列表
  $categories = array_merge(
    [['id' => 'all', 'name' => __('ui.newsListBasemap.catAll')]],
    collect($postCategories)->map(fn($c) => ['id' => $c, 'name' => $c])->toArray()
  );

  $listId = 'pv-news-list-' . uniqid();
@endphp

<section class="pv-news-list-section">
  <div class="pv-nl-container">

    <h2 class="pv-nl-page-title">{{ __('ui.newsListBasemap.title') }}</h2>

    {{-- 分類 Tab --}}
    <div class="pv-nl-filter-bar" id="{{ $listId }}-filters">
      @foreach($categories as $cat)
        <button
          class="pv-nl-filter-btn{{ $cat['id'] === 'all' ? ' active' : '' }}"
          data-cat="{{ $cat['id'] }}"
        >{{ $cat['name'] }}</button>
      @endforeach
    </div>
    <div class="pv-nl-filter-divider"></div>

    {{-- 消息列表 --}}
    <div class="pv-nl-news-list" id="{{ $listId }}-list">
      @forelse($mappedPosts as $news)
        <div
          class="pv-nl-news-item"
          data-tag="{{ $news['tag'] }}"
        >
          <h3 class="pv-nl-news-title">{{ $news['title'] }}</h3>
          <span class="pv-nl-news-date">{{ $news['date'] }}</span>
        </div>
      @empty
        <div class="pv-nl-news-empty">{{ __('ui.newsListBasemap.empty') }}</div>
      @endforelse
    </div>

    {{-- 分頁 --}}
    <div class="pv-nl-pagination" id="{{ $listId }}-pagination"></div>

  </div>
</section>

<style>
.pv-news-list-section {
  padding: 3rem 0 5rem;
  background: transparent;
  min-height: 60vh;
}
.pv-nl-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 3rem;
}
.pv-nl-page-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--frame-heading-color, #222);
  text-align: center;
  margin: 0 0 2.5rem;
}
.pv-nl-filter-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  flex-wrap: wrap;
}
.pv-nl-filter-btn {
  padding: 10px 20px;
  border: none;
  border-bottom: 2px solid transparent;
  background: transparent;
  font-size: 15px;
  color: var(--frame-text-secondary, #666);
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
  margin-bottom: -1px;
}
.pv-nl-filter-btn:hover  { color: var(--frame-text-color, #333); }
.pv-nl-filter-btn.active { color: #E8572A; border-bottom-color: #E8572A; font-weight: 500; }
.pv-nl-filter-divider {
  border-top: 1px solid var(--frame-border-color, #e5e5e5);
  margin-bottom: 2rem;
}
.pv-nl-news-list  { display: flex; flex-direction: column; margin-bottom: 3rem; }
.pv-nl-news-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.1rem 0;
  border-bottom: 1px solid var(--frame-border-color, #f0f0f0);
  cursor: pointer;
  transition: all 0.15s;
}
.pv-nl-news-item:first-child { border-top: 1px solid var(--frame-border-color, #f0f0f0); }
.pv-nl-news-item:hover .pv-nl-news-title { color: #E8572A; }
.pv-nl-news-item.hidden { display: none; }
.pv-nl-news-title {
  flex: 1;
  font-size: 16px;
  font-weight: 700;
  color: var(--frame-text-color, #333);
  margin: 0;
  transition: color 0.2s;
  padding-right: 2rem;
}
.pv-nl-news-date {
  flex-shrink: 0;
  font-size: 14px;
  color: var(--frame-text-muted, #aaa);
  white-space: nowrap;
}
.pv-nl-news-empty {
  padding: 3rem 0;
  text-align: center;
  font-size: 15px;
  color: var(--frame-text-muted, #bbb);
}
.pv-nl-pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-top: 2rem;
  flex-wrap: wrap;
}
.pv-nl-page-btn {
  min-width: 38px;
  height: 38px;
  padding: 0 10px;
  border: 1.5px solid var(--frame-border-color, #ddd);
  border-radius: 20px;
  background: transparent;
  font-size: 14px;
  color: var(--frame-text-secondary, #555);
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.pv-nl-page-btn:hover:not(.disabled):not(.active) { border-color: #E8572A; color: #E8572A; }
.pv-nl-page-btn.active   { background: #E8572A; border-color: #E8572A; color: #fff; font-weight: 600; }
.pv-nl-page-btn.disabled { opacity: 0.4; cursor: default; pointer-events: none; }
.pv-nl-page-nav  { padding: 0 16px; font-size: 13px; }
.pv-nl-page-ellipsis {
  min-width: 38px;
  height: 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  color: var(--frame-text-muted, #bbb);
  letter-spacing: 2px;
}

@media (min-width: 769px) and (max-width: 1024px) {
  .pv-nl-container  { padding: 0 1.5rem; }
  .pv-nl-page-title { font-size: 24px; }
  .pv-nl-filter-btn { font-size: 14px; padding: 8px 16px; }
  .pv-nl-news-title { font-size: 15px; }
}
@media (max-width: 768px) {
  .pv-news-list-section { padding: 2rem 0 3rem; }
  .pv-nl-container  { padding: 0 1rem; }
  .pv-nl-page-title { font-size: 22px; margin-bottom: 1.5rem; }
  .pv-nl-filter-bar { justify-content: flex-start; overflow-x: auto; flex-wrap: nowrap; padding-bottom: 2px; }
  .pv-nl-filter-btn { font-size: 13px; padding: 8px 14px; }
  .pv-nl-news-item  { flex-direction: column; align-items: flex-start; gap: 4px; padding: 0.9rem 0; }
  .pv-nl-news-title { font-size: 14px; padding-right: 0; }
  .pv-nl-news-date  { font-size: 12px; }
  .pv-nl-page-btn   { min-width: 34px; height: 34px; font-size: 13px; }
  .pv-nl-page-nav   { padding: 0 12px; }
}
</style>

<script>
var __nlPrev = '{{ __("ui.newsListBasemap.prev") }}';
var __nlNext = '{{ __("ui.newsListBasemap.next") }}';
(function () {
  var id         = '{{ $listId }}';
  var filterBar  = document.getElementById(id + '-filters');
  var listEl     = document.getElementById(id + '-list');
  var pagination = document.getElementById(id + '-pagination');
  if (!filterBar || !listEl) return;

  var PAGE_SIZE   = {{ $pageSize }};
  var allItems    = Array.from(listEl.querySelectorAll('.pv-nl-news-item'));
  var currentCat  = 'all';
  var currentPage = 1;

  function getFiltered() {
    if (currentCat === 'all') return allItems;
    return allItems.filter(function (el) {
      return el.dataset.tag === currentCat;
    });
  }

  function getTotalPages(filtered) {
    return Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
  }

  function render() {
    var filtered   = getFiltered();
    var totalPages = getTotalPages(filtered);
    if (currentPage > totalPages) currentPage = 1;

    var start = (currentPage - 1) * PAGE_SIZE;
    var end   = start + PAGE_SIZE;

    // 顯示/隱藏
    allItems.forEach(function (el) { el.classList.add('hidden'); });
    filtered.slice(start, end).forEach(function (el) { el.classList.remove('hidden'); });

    // 分頁按鈕
    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    pagination.innerHTML = '';
    if (totalPages <= 1) return;

    var pages = buildPageNumbers(currentPage, totalPages);

    // 上一頁
    var prev = makeBtn(__nlPrev, 'pv-nl-page-btn pv-nl-page-nav' + (currentPage === 1 ? ' disabled' : ''));
    prev.addEventListener('click', function () { if (currentPage > 1) { currentPage--; render(); } });
    pagination.appendChild(prev);

    pages.forEach(function (p) {
      if (p === '...') {
        var ell = document.createElement('span');
        ell.className = 'pv-nl-page-ellipsis';
        ell.textContent = '...';
        pagination.appendChild(ell);
      } else {
        var btn = makeBtn(p, 'pv-nl-page-btn' + (p === currentPage ? ' active' : ''));
        btn.addEventListener('click', function () { currentPage = p; render(); });
        pagination.appendChild(btn);
      }
    });

    // 下一頁
    var next = makeBtn(__nlNext, 'pv-nl-page-btn pv-nl-page-nav' + (currentPage === totalPages ? ' disabled' : ''));
    next.addEventListener('click', function () { if (currentPage < totalPages) { currentPage++; render(); } });
    pagination.appendChild(next);
  }

  function makeBtn(text, cls) {
    var btn = document.createElement('button');
    btn.className   = cls;
    btn.textContent = text;
    return btn;
  }

  function buildPageNumbers(cur, total) {
    if (total <= 7) {
      return Array.from({ length: total }, function (_, i) { return i + 1; });
    }
    if (cur <= 4)          return [1, 2, 3, 4, 5, '...', total];
    if (cur >= total - 3)  return [1, '...', total-4, total-3, total-2, total-1, total];
    return [1, '...', cur-1, cur, cur+1, '...', total];
  }

  // 分類篩選
  filterBar.addEventListener('click', function (e) {
    var btn = e.target.closest('.pv-nl-filter-btn');
    if (!btn) return;
    filterBar.querySelectorAll('.pv-nl-filter-btn').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
    currentCat  = btn.dataset.cat;
    currentPage = 1;
    render();
  });

  render();
})();
</script>
