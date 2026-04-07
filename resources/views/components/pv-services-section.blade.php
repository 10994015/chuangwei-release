{{-- resources/views/components/pv-services-section.blade.php --}}
@php
  $data     = $frame['data'] ?? [];
  $services = $data['services'] ?? [];

  $mapService = fn($s) => [
    'id'          => $s['id']          ?? null,
    'title'       => $s['name']        ?? '',
    'source'      => $s['tenantName']  ?? '',
    'description' => $s['description'] ?? '',
    'link'        => $s['link']        ?? '#',
    'image'       => $s['imgSrc']      ?? null,
    'badge'       => (isset($s['labels']) && is_array($s['labels']) && count($s['labels']))
                       ? $s['labels'][0] : null,
    'badgeClass'  => (function() use ($s) {
      $l = $s['labels'][0] ?? '';
      if (in_array($l, ['熱門','hot']))               return 'hot';
      if (in_array($l, ['推薦','recommended']))       return 'recommended';
      if (in_array($l, ['新品','new']))               return 'new';
      return 'default';
    })(),
  ];

  $mappedServices = collect($services)->map($mapService)->values()->toArray();
  $pageSize       = 4;
  $sectionId      = 'pv-svc-' . uniqid();
@endphp

<section class="pv-services-section">
  <div class="pv-svc-container">

    {{-- 標題列 --}}
    <div class="pv-svc-section-header">
      <div class="pv-svc-header-left">
        <h2 class="pv-svc-section-title">{{ __('ui.pvServicesSection.title') }}</h2>
        <p class="pv-svc-section-subtitle">{{ __('ui.pvServicesSection.subtitle') }}</p>
      </div>
      <div class="pv-svc-header-right">
        <a href="/products" class="pv-svc-view-all">{{ __('ui.pvServicesSection.viewMore') }}</a>
        <button class="pv-svc-nav-btn" id="{{ $sectionId }}-prev" aria-label="{{ __('ui.pvServicesSection.prev') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
        </button>
        <button class="pv-svc-nav-btn" id="{{ $sectionId }}-next" aria-label="{{ __('ui.pvServicesSection.next') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </button>
      </div>
    </div>

    {{-- 服務 Grid --}}
    <div class="pv-svc-services-grid" id="{{ $sectionId }}-grid">
      @forelse($mappedServices as $item)
        <div class="pv-svc-service-card">
          <div class="pv-svc-service-image">
            @if($item['image'])
              <img
                src="{{ $item['image'] }}"
                alt="{{ $item['title'] }}"
                class="pv-svc-image"
                loading="lazy"
                onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
              />
              <div class="pv-svc-image-placeholder" style="display:none"><span>{{ __('ui.pvServicesSection.noImage') }}</span></div>
            @else
              <div class="pv-svc-image-placeholder"><span>{{ __('ui.pvServicesSection.noImage') }}</span></div>
            @endif
          </div>
          <div class="pv-svc-service-info">
            @if($item['badge'])
              <span class="pv-svc-badge {{ $item['badgeClass'] }}">{{ $item['badge'] }}</span>
            @endif
            <p class="pv-svc-service-source">{{ $item['source'] }}</p>
            <h3 class="pv-svc-service-title">{{ $item['title'] }}</h3>
            <div class="pv-svc-service-footer">
              @if($item['description'])
                <p class="pv-svc-service-desc">{{ $item['description'] }}</p>
              @endif
              <button class="pv-svc-cart-btn" aria-label="{{ __('ui.pvServicesSection.bookBtn') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      @empty
        <div class="pv-svc-empty">{{ __('ui.pvServicesSection.empty') }}</div>
      @endforelse
    </div>

  </div>
</section>

<style>
.pv-services-section { padding: 3rem 0 4rem; background: transparent; }
.pv-svc-container    { max-width: 1400px; margin: 0 auto; padding: 0 3rem; }
.pv-svc-section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.75rem; }
.pv-svc-header-left  { display: flex; flex-direction: column; gap: 4px; }
.pv-svc-section-title   { font-size: 26px; font-weight: 700; color: var(--frame-heading-color,#222); margin: 0; line-height: 1.2; }
.pv-svc-section-subtitle { font-size: 13px; color: var(--frame-text-muted,#999); margin: 0; }
.pv-svc-header-right { display: flex; align-items: center; gap: 8px; }
.pv-svc-view-all {
  padding: 8px 20px;
  border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 20px;
  font-size: 14px; color: var(--frame-text-color,#444);
  text-decoration: none; white-space: nowrap; transition: all 0.2s;
}
.pv-svc-view-all:hover { border-color: #E8572A; color: #E8572A; }
.pv-svc-nav-btn {
  width: 36px; height: 36px;
  border: 1.5px solid var(--frame-border-color,#ddd); border-radius: 50%;
  background: transparent; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s; color: var(--frame-text-color,#444);
}
.pv-svc-nav-btn svg   { width: 16px; height: 16px; }
.pv-svc-nav-btn:hover { border-color: #E8572A; color: #E8572A; }

.pv-svc-services-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }

.pv-svc-service-card {
  background: var(--frame-card-bg,#fff);
  border: 1px solid var(--frame-border-color,#eee);
  border-radius: 12px; overflow: hidden; cursor: pointer;
  transition: box-shadow 0.2s, transform 0.2s;
}
.pv-svc-service-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }
.pv-svc-service-image { position: relative; width: 100%; aspect-ratio: 4/3; overflow: hidden; background: #f5f5f5; }
.pv-svc-image { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
.pv-svc-service-card:hover .pv-svc-image { transform: scale(1.03); }
.pv-svc-image-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--frame-tag-bg,#f0f0f0); }
.pv-svc-image-placeholder span { font-size: 13px; color: var(--frame-text-muted,#bbb); }
.pv-svc-badge { position: absolute; top: 14px; right: 16px; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #fff; line-height: 1.4; }
.pv-svc-badge.hot         { background: #dc3545; }
.pv-svc-badge.recommended { background: #1a73e8; }
.pv-svc-badge.new         { background: #2ecc71; }
.pv-svc-badge.default     { background: #E8572A; }
.pv-svc-service-info    { position: relative; padding: 14px 16px 16px; }
.pv-svc-service-source  { font-size: 13px; color: var(--frame-text-muted,#999); margin: 0 0 4px; }
.pv-svc-service-title   { font-size: 18px; font-weight: 700; color: var(--frame-text-color,#222); margin: 0 0 8px; line-height: 1.3; }
.pv-svc-service-footer  { display: flex; justify-content: space-between; align-items: flex-end; gap: 8px; margin-top: 10px; }
.pv-svc-service-desc {
  font-size: 13px; color: var(--frame-text-muted,#999); margin: 0; line-height: 1.6; flex: 1;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.pv-svc-cart-btn { flex-shrink: 0; width: 36px; height: 36px; background: transparent; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #E8572A; transition: transform 0.2s; padding: 0; }
.pv-svc-cart-btn svg   { width: 20px; height: 20px; }
.pv-svc-cart-btn:hover { transform: scale(1.15); }
.pv-svc-empty { grid-column: 1/-1; padding: 2rem; text-align: center; color: var(--frame-text-muted,#bbb); font-size: 14px; }

@media (max-width: 1024px) { .pv-svc-services-grid { grid-template-columns: repeat(2,1fr); gap: 16px; } }
@media (max-width: 768px) {
  .pv-services-section     { padding: 2rem 0 2.5rem; }
  .pv-svc-container        { padding: 0 1rem; }
  .pv-svc-section-header   { flex-direction: column; align-items: flex-start; gap: 12px; }
  .pv-svc-header-right     { align-self: flex-end; }
  .pv-svc-services-grid    { grid-template-columns: repeat(2,1fr); gap: 12px; }
  .pv-svc-section-title    { font-size: 20px; }
  .pv-svc-service-title    { font-size: 15px; }
  .pv-svc-service-source   { font-size: 12px; }
}
@media (max-width: 480px) { .pv-svc-services-grid { grid-template-columns: 1fr; } }
</style>

<script>
(function () {
  var id      = '{{ $sectionId }}';
  var grid    = document.getElementById(id + '-grid');
  var btnPrev = document.getElementById(id + '-prev');
  var btnNext = document.getElementById(id + '-next');
  if (!grid || !btnPrev || !btnNext) return;

  var PAGE_SIZE = {{ $pageSize }};
  var cards     = Array.from(grid.querySelectorAll('.pv-svc-service-card'));
  var total     = Math.max(1, Math.ceil(cards.length / PAGE_SIZE));
  var page      = 0;

  function render() {
    var start = page * PAGE_SIZE;
    cards.forEach(function (c, i) {
      c.style.display = (i >= start && i < start + PAGE_SIZE) ? '' : 'none';
    });
  }

  btnPrev.addEventListener('click', function () { page = (page - 1 + total) % total; render(); });
  btnNext.addEventListener('click', function () { page = (page + 1) % total; render(); });

  render();
})();
</script>
