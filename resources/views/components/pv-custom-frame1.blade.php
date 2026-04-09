{{-- resources/views/components/pv-custom-frame1.blade.php --}}
@php
  $data  = $frame['data'] ?? [];
  $items = $data['items'] ?? [];

  $DEFAULT_ITEMS = [
    ['title' => '宮廟地圖',     'description' => '整合全台宮廟資訊，提供地理搜尋、神明分類、活動查詢等多元曝光管道。', 'image' => null],
    ['title' => '靈籤司',       'description' => '解籤後智能推薦宮廟，將線上求籤信眾精準導流至實地參拜。',              'image' => null],
    ['title' => '主平台服務',   'description' => '匯聚宮廟完整資訊，成為信眾探索文化、查詢活動的一站式入口。',          'image' => null],
    ['title' => '宮廟網站建置', 'description' => '協助建置專屬數位門戶，提供客製化功能與獨立經營數位社群能力。',        'image' => null],
  ];

  $getItem = function(int $idx) use ($items, $DEFAULT_ITEMS): array {
    $src = $items[$idx] ?? [];
    $def = $DEFAULT_ITEMS[$idx];
    return array_merge($def, array_filter($src, fn($v) => $v !== null && $v !== ''));
  };

  $item0 = $getItem(0);
  $item1 = $getItem(1);
  $item2 = $getItem(2);
  $item3 = $getItem(3);

  // 根據 device 取 padding
  $deviceKey = match(request()->query('device', 'desktop')) {
    'tablet' => 'tablet',
    'mobile' => 'phone',
    default  => 'pc',
  };

  $padStyle = function(array $item) use ($deviceKey): string {
    $p = $item['padding'][$deviceKey] ?? null;
    if (!$p) return '';
    $t = $p['top'] ?? 0; $r = $p['right'] ?? 0; $b = $p['bottom'] ?? 0; $l = $p['left'] ?? 0;
    if (!$t && !$r && !$b && !$l) return '';
    return "padding:{$t}px {$r}px {$b}px {$l}px;";
  };

  $imgStyle = function(array $item): string {
    $s = '';
    if (!empty($item['imageWidth']))                  $s .= "width:{$item['imageWidth']}px;";
    if (!empty($item['imageHeight']))                 $s .= "height:{$item['imageHeight']}px;";
    if (isset($item['imageBorderRadius']))            $s .= "border-radius:{$item['imageBorderRadius']}px;";
    if (!empty($item['imageBorderWidth'])) {
      $bc = $item['imageBorderColor'] ?? '#000000';
      $s .= "border:{$item['imageBorderWidth']}px solid {$bc};";
    }
    return $s;
  };
@endphp

<section class="pv-cf1">
  <div class="pv-cf1-container">
    <div class="pv-cf1-grid">

      {{-- 左側主區 --}}
      <div class="pv-cf1-left">

        {{-- Hero 卡片 (item 0) --}}
        <div class="pv-cf1-card pv-cf1-hero" style="{{ $padStyle($item0) }}">
          <div class="pv-cf1-hero-text">
            <h3 class="pv-cf1-title">{!! $item0['title'] !!}</h3>
            <div class="pv-cf1-desc">{!! $item0['description'] !!}</div>
          </div>
          <div class="pv-cf1-hero-img" style="{{ $imgStyle($item0) }}">
            @if(!empty($item0['image']))
              <img src="{{ $item0['image'] }}" alt="{{ $item0['title'] }}" class="pv-cf1-img" />
            @else
              <div class="pv-cf1-img-placeholder"><span></span></div>
            @endif
          </div>
        </div>

        {{-- 底部兩卡片 (items 2, 3) --}}
        <div class="pv-cf1-bottom-row">
          <div class="pv-cf1-card pv-cf1-bottom-card" style="{{ $padStyle($item2) }}">
            <h3 class="pv-cf1-title">{!! $item2['title'] !!}</h3>
            <div class="pv-cf1-desc">{!! $item2['description'] !!}</div>
          </div>
          <div class="pv-cf1-card pv-cf1-bottom-card" style="{{ $padStyle($item3) }}">
            <h3 class="pv-cf1-title">{!! $item3['title'] !!}</h3>
            <div class="pv-cf1-desc">{!! $item3['description'] !!}</div>
          </div>
        </div>

      </div>

      {{-- 右側高卡片 (item 1) --}}
      <div class="pv-cf1-card pv-cf1-side" style="{{ $padStyle($item1) }}">
        <div class="pv-cf1-side-text">
          <h3 class="pv-cf1-title">{!! $item1['title'] !!}</h3>
          <div class="pv-cf1-desc">{!! $item1['description'] !!}</div>
        </div>
        <div class="pv-cf1-side-img" style="{{ $imgStyle($item1) }}">
          @if(!empty($item1['image']))
            <img src="{{ $item1['image'] }}" alt="{{ $item1['title'] }}" class="pv-cf1-img" />
          @else
            <div class="pv-cf1-img-placeholder pv-cf1-img-placeholder--side"><span></span></div>
          @endif
        </div>
      </div>

    </div>
  </div>
</section>

<style>
.pv-cf1 { padding: 3rem 0 4rem; background: transparent; }
.pv-cf1-container { max-width: 1400px; margin: 0 auto; padding: 0 3rem; }

.pv-cf1-grid {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 20px;
  align-items: stretch;
}

.pv-cf1-left { display: flex; flex-direction: column; gap: 20px; }

.pv-cf1-card {
  background: var(--frame-card-bg, #fff);
  border: 1px solid var(--frame-border-color, #eee);
  border-radius: 16px;
  overflow: hidden;
}

/* Hero 卡片：左文右圖 */
.pv-cf1-hero {
  display: flex;
  align-items: center;
  padding: 2rem 2.5rem;
  gap: 2rem;
  flex: 1;
}
.pv-cf1-hero-text { flex: 1; min-width: 0; }
.pv-cf1-hero-img  { flex-shrink: 0; width: 260px; height: 200px; overflow: hidden; }

/* 底部兩卡並排 */
.pv-cf1-bottom-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.pv-cf1-bottom-card { padding: 2rem; }

/* 右側高卡片 */
.pv-cf1-side { display: flex; flex-direction: column; }
.pv-cf1-side-text { padding: 1.5rem 1.5rem 1rem; }
.pv-cf1-side-img  { flex: 1; min-height: 200px; overflow: hidden; }

/* 文字 */
.pv-cf1-title {
  font-size: 18px; font-weight: 700;
  color: var(--frame-heading-color, #222);
  margin: 0 0 10px; line-height: 1.3;
}
.pv-cf1-desc {
  font-size: 14px; color: var(--frame-text-muted, #666);
  margin: 0; line-height: 1.7;
}

/* 圖片 */
.pv-cf1-img { width: 100%; height: 100%; object-fit: contain; display: block; }
.pv-cf1-img-placeholder {
  width: 100%; height: 100%; min-height: 160px;
  display: flex; align-items: center; justify-content: center;
  background: var(--frame-tag-bg, #f5f5f5); border-radius: 8px;
}
.pv-cf1-img-placeholder span { font-size: 12px; color: var(--frame-text-muted, #bbb); text-align: center; padding: 0 12px; }
.pv-cf1-img-placeholder--side { border-radius: 0 0 16px 16px; min-height: 200px; }

/* 響應式 - 平板 */
@media (max-width: 1024px) {
  .pv-cf1-container { padding: 0 1.5rem; }
  .pv-cf1-grid { grid-template-columns: 1fr 220px; gap: 16px; }
  .pv-cf1-hero { flex-direction: column; align-items: flex-start; }
  .pv-cf1-hero .pv-cf1-hero-img { width: 100%; height: 180px; }
}

/* 響應式 - 手機 */
@media (max-width: 768px) {
  .pv-cf1 { padding: 2rem 0 2.5rem; }
  .pv-cf1-container { padding: 0 1rem; }
  .pv-cf1-grid { grid-template-columns: 1fr; }
  .pv-cf1-hero { flex-direction: column; align-items: flex-start; padding: 1.5rem; }
  .pv-cf1-hero .pv-cf1-hero-img { width: 100%; height: 180px; }
  .pv-cf1-bottom-row { grid-template-columns: 1fr; }
  .pv-cf1-side { flex-direction: row; align-items: center; }
  .pv-cf1-side .pv-cf1-side-text { flex: 1; }
  .pv-cf1-side .pv-cf1-side-img  { width: 120px; height: 120px; flex: none; }
  .pv-cf1-title { font-size: 16px; }
  .pv-cf1-desc  { font-size: 13px; }
}
</style>
