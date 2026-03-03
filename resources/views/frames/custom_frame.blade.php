{{-- resources/views/frames/custom_frame.blade.php --}}

@php
  $frameType = $frame['type'] ?? '';
  $elements  = $frame['elements'] ?? [];

  $layoutMap = [
    // ── 新格式（Vue editor 產生，無底線）──
    'FRAME1_1' => ['grid' => 'cf-grid-1-1', 'type' => 'single',    'cols' => 1],
    'FRAME1_2' => ['grid' => 'cf-grid-1-2', 'type' => 'single',    'cols' => 2],
    'FRAME1_3' => ['grid' => 'cf-grid-1-3', 'type' => 'single',    'cols' => 3],
    'FRAME1_4' => ['grid' => 'cf-grid-1-4', 'type' => 'single',    'cols' => 4],
    'FRAME2_2' => ['grid' => 'cf-grid-2-2', 'type' => 'double',    'cols' => 2],
    'FRAME2_3' => ['grid' => 'cf-grid-2-3', 'type' => 'double',    'cols' => 3],
    'FRAME2_4' => ['grid' => 'cf-grid-2-4', 'type' => 'double',    'cols' => 4],
    'FRAMEA'   => ['grid' => 'cf-grid-A',   'type' => 'composite', 'side' => 'left',  'leftIdxs' => [0],       'rightIdxs' => [1, 2]],
    'FRAMEB'   => ['grid' => 'cf-grid-B',   'type' => 'composite', 'side' => 'right', 'leftIdxs' => [0, 1],    'rightIdxs' => [2]],
    'FRAMEC'   => ['grid' => 'cf-grid-C',   'type' => 'composite', 'side' => 'left',  'leftIdxs' => [0],       'rightIdxs' => [1, 2, 3]],
    'FRAMED'   => ['grid' => 'cf-grid-D',   'type' => 'composite', 'side' => 'right', 'leftIdxs' => [0, 1, 2], 'rightIdxs' => [3]],
    // ── 舊格式（有底線，兼容舊資料）──
    'FRAME_1_1' => ['grid' => 'cf-grid-1-1', 'type' => 'single',    'cols' => 1],
    'FRAME_1_2' => ['grid' => 'cf-grid-1-2', 'type' => 'single',    'cols' => 2],
    'FRAME_1_3' => ['grid' => 'cf-grid-1-3', 'type' => 'single',    'cols' => 3],
    'FRAME_1_4' => ['grid' => 'cf-grid-1-4', 'type' => 'single',    'cols' => 4],
    'FRAME_2_2' => ['grid' => 'cf-grid-2-2', 'type' => 'double',    'cols' => 2],
    'FRAME_2_3' => ['grid' => 'cf-grid-2-3', 'type' => 'double',    'cols' => 3],
    'FRAME_2_4' => ['grid' => 'cf-grid-2-4', 'type' => 'double',    'cols' => 4],
    'FRAME_A'   => ['grid' => 'cf-grid-A',   'type' => 'composite', 'side' => 'left',  'leftIdxs' => [0],       'rightIdxs' => [1, 2]],
    'FRAME_B'   => ['grid' => 'cf-grid-B',   'type' => 'composite', 'side' => 'right', 'leftIdxs' => [0, 1],    'rightIdxs' => [2]],
    'FRAME_C'   => ['grid' => 'cf-grid-C',   'type' => 'composite', 'side' => 'left',  'leftIdxs' => [0],       'rightIdxs' => [1, 2, 3]],
    'FRAME_D'   => ['grid' => 'cf-grid-D',   'type' => 'composite', 'side' => 'right', 'leftIdxs' => [0, 1, 2], 'rightIdxs' => [3]],
  ];

  $layout     = $layoutMap[$frameType] ?? ['grid' => 'cf-grid-1-1', 'type' => 'single', 'cols' => 1];
  $gridClass  = $layout['grid'];
  $layoutType = $layout['type'];

  // ── 動態計算 inline style（單層/雙層用，複合框架另外處理）──
  $inlineGridStyle = '';

  if ($layoutType === 'single') {
    $colWidths = collect($elements)
      ->map(fn($el) => !empty($el['width']) ? $el['width'] : '1fr')
      ->implode(' ');
    $inlineGridStyle = "grid-template-columns: {$colWidths};";

  } elseif ($layoutType === 'double') {
    $cols      = $layout['cols'];
    $row1Elems = array_slice($elements, 0, $cols);
    $colWidths = collect($row1Elems)
      ->map(fn($el) => !empty($el['width']) ? $el['width'] : '1fr')
      ->implode(' ');
    $inlineGridStyle = "grid-template-columns: {$colWidths};";

  } elseif ($layoutType === 'composite') {
    // 複合框架改為 flexbox，左欄寬度從第一個元素取
    $isLeftBig  = $layout['side'] === 'left';
    $leftWidth  = $isLeftBig
      ? (!empty($elements[0]['width']) ? $elements[0]['width'] : '66.7%')
      : (!empty($elements[0]['width']) ? $elements[0]['width'] : '33.3%');
  }

  // ── 共用：render 單一格子的 PHP helper ──
  // （直接 inline 在 blade 中，不需要額外 partial）
@endphp

{{-- ==================== 複合框架 A/B/C/D：Flexbox 左右欄 ==================== --}}
@if($layoutType === 'composite')
  @php
    $leftIdxs  = $layout['leftIdxs'];
    $rightIdxs = $layout['rightIdxs'];
  @endphp
    <div class="cf-frame-container">
        <div class="custom-frame cf-composite">

            {{-- 左欄 --}}
            <div class="cf-composite-left" style="width: {{ $leftWidth }};">
            @foreach($leftIdxs as $idx)
                @php
                $element   = $elements[$idx] ?? [];
                $elType    = $element['type']     ?? '';
                $elValue   = $element['value']    ?? [];
                $elMeta    = $element['metadata'] ?? [];
                $elPadding = $element['padding']  ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
                $pt = $elPadding['top']    ?? 0;
                $pr = $elPadding['right']  ?? 0;
                $pb = $elPadding['bottom'] ?? 0;
                $pl = $elPadding['left']   ?? 0;
                $paddingStyle = "padding: {$pt}px {$pr}px {$pb}px {$pl}px;";
                $color      = $elMeta['color']           ?? null;
                $fontSize   = $elMeta['fontSize']        ?? $elMeta['font_size']        ?? null;
                $fontWeight = $elMeta['fontWeight']      ?? $elMeta['font_weight']      ?? null;
                $textAlign  = $elMeta['textAlign']       ?? $elMeta['text_align']       ?? null;
                $bgColor    = $elMeta['backgroundColor'] ?? $elMeta['background_color'] ?? null;
                $metaStyle  = '';
                if ($color)      $metaStyle .= "color: {$color};";
                if ($fontSize)   $metaStyle .= "font-size: {$fontSize};";
                if ($fontWeight) $metaStyle .= "font-weight: {$fontWeight};";
                if ($textAlign)  $metaStyle .= "text-align: {$textAlign};";
                if ($bgColor)    $metaStyle .= "background-color: {$bgColor};";
                @endphp
                <div class="cf-col" style="{{ $paddingStyle }} {{ $metaStyle }}">
                @if($elType === 'IMG')
                    @include('elements.image_element', ['src' => $elValue['src'] ?? null, 'alt' => $elValue['alt'] ?? '', 'meta' => $elMeta])
                @elseif($elType === 'TEXT')
                    @include('elements.text_element', ['text' => $elValue['text'] ?? '', 'meta' => $elMeta])
                @elseif($elType === 'BUTTON')
                    @include('elements.button_element', ['text' => $elValue['text'] ?? '', 'url' => $elValue['url'] ?? '#', 'meta' => $elMeta])
                @elseif($elType === 'HORIZON_LINE')
                    @include('elements.hline_element', ['meta' => $elMeta])
                @elseif($elType === 'VERTICAL_LINE')
                    @include('elements.vline_element', ['meta' => $elMeta])
                @elseif($elType === 'CAROUSEL_IMG')
                    @include('elements.carousel_element', ['imgs' => $elValue['imgs'] ?? [], 'meta' => $elMeta])
                @elseif($elType === 'GOOGLE_MAP')
                    @include('elements.map_element', ['address' => $elValue['address'] ?? '', 'lat' => $elValue['lat'] ?? null, 'lng' => $elValue['lng'] ?? null, 'zoom' => $elValue['zoom'] ?? 15, 'meta' => $elMeta])
                @elseif($elType === 'ALBUM')
                    @include('elements.album_element', ['data' => $elValue, 'meta' => $elMeta])
                @elseif($elType === 'PRODUCT_CARD')
                    @include('elements.product_card', ['data' => $elValue, 'meta' => $elMeta])
                @elseif($elType === 'SERVICE_CARD')
                    @include('elements.service_card', ['data' => $elValue, 'meta' => $elMeta])
                @elseif($elType === 'EVENT_CARD')
                    @include('elements.event_card', ['data' => $elValue, 'meta' => $elMeta])
                @endif
                </div>
            @endforeach
            </div>

            {{-- 右欄 --}}
            <div class="cf-composite-right">
            @foreach($rightIdxs as $idx)
                @php
                $element   = $elements[$idx] ?? [];
                $elType    = $element['type']     ?? '';
                $elValue   = $element['value']    ?? [];
                $elMeta    = $element['metadata'] ?? [];
                $elPadding = $element['padding']  ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
                $pt = $elPadding['top']    ?? 0;
                $pr = $elPadding['right']  ?? 0;
                $pb = $elPadding['bottom'] ?? 0;
                $pl = $elPadding['left']   ?? 0;
                $paddingStyle = "padding: {$pt}px {$pr}px {$pb}px {$pl}px;";
                $color      = $elMeta['color']           ?? null;
                $fontSize   = $elMeta['fontSize']        ?? $elMeta['font_size']        ?? null;
                $fontWeight = $elMeta['fontWeight']      ?? $elMeta['font_weight']      ?? null;
                $textAlign  = $elMeta['textAlign']       ?? $elMeta['text_align']       ?? null;
                $bgColor    = $elMeta['backgroundColor'] ?? $elMeta['background_color'] ?? null;
                $metaStyle  = '';
                if ($color)      $metaStyle .= "color: {$color};";
                if ($fontSize)   $metaStyle .= "font-size: {$fontSize};";
                if ($fontWeight) $metaStyle .= "font-weight: {$fontWeight};";
                if ($textAlign)  $metaStyle .= "text-align: {$textAlign};";
                if ($bgColor)    $metaStyle .= "background-color: {$bgColor};";
                @endphp
                <div class="cf-col" style="{{ $paddingStyle }} {{ $metaStyle }}">
                @if($elType === 'IMG')
                    @include('elements.image_element', ['src' => $elValue['src'] ?? null, 'alt' => $elValue['alt'] ?? '', 'meta' => $elMeta])
                @elseif($elType === 'TEXT')
                    @include('elements.text_element', ['text' => $elValue['text'] ?? '', 'meta' => $elMeta])
                @elseif($elType === 'BUTTON')
                    @include('elements.button_element', ['text' => $elValue['text'] ?? '', 'url' => $elValue['url'] ?? '#', 'meta' => $elMeta])
                @elseif($elType === 'HORIZON_LINE')
                    @include('elements.hline_element', ['meta' => $elMeta])
                @elseif($elType === 'VERTICAL_LINE')
                    @include('elements.vline_element', ['meta' => $elMeta])
                @elseif($elType === 'CAROUSEL_IMG')
                    @include('elements.carousel_element', ['imgs' => $elValue['imgs'] ?? [], 'meta' => $elMeta])
                @elseif($elType === 'GOOGLE_MAP')
                    @include('elements.map_element', ['address' => $elValue['address'] ?? '', 'lat' => $elValue['lat'] ?? null, 'lng' => $elValue['lng'] ?? null, 'zoom' => $elValue['zoom'] ?? 15, 'meta' => $elMeta])
                @elseif($elType === 'ALBUM')
                    @include('elements.album_element', ['data' => $elValue, 'meta' => $elMeta])
                @elseif($elType === 'PRODUCT_CARD')
                    @include('elements.product_card', ['data' => $elValue, 'meta' => $elMeta])
                @elseif($elType === 'SERVICE_CARD')
                    @include('elements.service_card', ['data' => $elValue, 'meta' => $elMeta])
                @elseif($elType === 'EVENT_CARD')
                    @include('elements.event_card', ['data' => $elValue, 'meta' => $elMeta])
                @endif
                </div>
            @endforeach
            </div>

        </div>
    </div>

{{-- ==================== 單層 / 雙層框架：CSS Grid ==================== --}}
@else
    <div class="cf-frame-container">
    <div class="custom-frame {{ $gridClass }}" style="{{ $inlineGridStyle }}">
        @foreach($elements as $idx => $element)
        @php
            $elType    = $element['type']     ?? '';
            $elValue   = $element['value']    ?? [];
            $elMeta    = $element['metadata'] ?? [];
            $elPadding = $element['padding']  ?? ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
            $pt = $elPadding['top']    ?? 0;
            $pr = $elPadding['right']  ?? 0;
            $pb = $elPadding['bottom'] ?? 0;
            $pl = $elPadding['left']   ?? 0;
            $paddingStyle = "padding: {$pt}px {$pr}px {$pb}px {$pl}px;";
            $color      = $elMeta['color']           ?? null;
            $fontSize   = $elMeta['fontSize']        ?? $elMeta['font_size']        ?? null;
            $fontWeight = $elMeta['fontWeight']      ?? $elMeta['font_weight']      ?? null;
            $textAlign  = $elMeta['textAlign']       ?? $elMeta['text_align']       ?? null;
            $bgColor    = $elMeta['backgroundColor'] ?? $elMeta['background_color'] ?? null;
            $metaStyle  = '';
            if ($color)      $metaStyle .= "color: {$color};";
            if ($fontSize)   $metaStyle .= "font-size: {$fontSize};";
            if ($fontWeight) $metaStyle .= "font-weight: {$fontWeight};";
            if ($textAlign)  $metaStyle .= "text-align: {$textAlign};";
            if ($bgColor)    $metaStyle .= "background-color: {$bgColor};";
        @endphp

        <div class="cf-col" style="{{ $paddingStyle }} {{ $metaStyle }}">

            @if($elType === 'IMG')
            @include('elements.image_element', ['src' => $elValue['src'] ?? null, 'alt' => $elValue['alt'] ?? '', 'meta' => $elMeta])
            @elseif($elType === 'TEXT')
            @include('elements.text_element', ['text' => $elValue['text'] ?? '', 'meta' => $elMeta])
            @elseif($elType === 'BUTTON')
            @include('elements.button_element', ['text' => $elValue['text'] ?? '', 'url' => $elValue['url'] ?? '#', 'meta' => $elMeta])
            @elseif($elType === 'HORIZON_LINE')
            @include('elements.hline_element', ['meta' => $elMeta])
            @elseif($elType === 'VERTICAL_LINE')
            @include('elements.vline_element', ['meta' => $elMeta])
            @elseif($elType === 'CAROUSEL_IMG')
            @include('elements.carousel_element', ['imgs' => $elValue['imgs'] ?? [], 'meta' => $elMeta])
            @elseif($elType === 'GOOGLE_MAP')
            @include('elements.map_element', ['address' => $elValue['address'] ?? '', 'lat' => $elValue['lat'] ?? null, 'lng' => $elValue['lng'] ?? null, 'zoom' => $elValue['zoom'] ?? 15, 'meta' => $elMeta])
            @elseif($elType === 'ALBUM')
            @include('elements.album_element', ['data' => $elValue, 'meta' => $elMeta])
            @elseif($elType === 'PRODUCT_CARD')
            @include('elements.product_card', ['data' => $elValue, 'meta' => $elMeta])
            @elseif($elType === 'SERVICE_CARD')
            @include('elements.service_card', ['data' => $elValue, 'meta' => $elMeta])
            @elseif($elType === 'EVENT_CARD')
            @include('elements.event_card', ['data' => $elValue, 'meta' => $elMeta])
            @endif

        </div>
        @endforeach
    </div>
    </div>
@endif

<style>
/* ==================== 共用 ==================== */
.cf-col { box-sizing: border-box; overflow: hidden; }

/* ==================== 單層 / 雙層：CSS Grid ==================== */
.custom-frame { display: grid; width: 100%; gap: 0; }

/* fallback（實際欄寬由 inline style 覆蓋） */
.cf-grid-1-1 { grid-template-columns: 1fr; }
.cf-grid-1-2 { grid-template-columns: repeat(2, 1fr); }
.cf-grid-1-3 { grid-template-columns: repeat(3, 1fr); }
.cf-grid-1-4 { grid-template-columns: repeat(4, 1fr); }
.cf-grid-2-2 { grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, auto); }
.cf-grid-2-3 { grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, auto); }
.cf-grid-2-4 { grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(2, auto); }

/* ==================== 複合框架 A/B/C/D：Flexbox 左右欄 ==================== */
.cf-composite {
  display: flex;
  width: 100%;
  align-items: stretch;
}

/* 左欄：寬度由 inline style 控制，內容垂直排列 */
.cf-composite-left {
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
}

/* 左欄只有一格時，撐滿整體高度（對應 Vue .composite-col--left .composite-cell:only-child { flex: 1 }） */
.cf-composite-left .cf-col:only-child {
  flex: 1;
}

/* 右欄：填滿剩餘寬度，各格高度獨立 */
.cf-composite-right {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
}

/* ✅ 右欄各格 height: auto，高度彼此獨立，不互相影響 */
.cf-composite-right .cf-col {
  height: auto;
}
.cf-frame-container {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}
/* ==================== RWD ==================== */
@media (max-width: 768px) {
  /* 單層/雙層 Grid 手機版：單欄 */
  .cf-grid-1-2, .cf-grid-1-3, .cf-grid-1-4,
  .cf-grid-2-2, .cf-grid-2-3, .cf-grid-2-4 {
    grid-template-columns: 1fr !important;
    grid-template-rows: unset !important;
  }

  /* 複合框架手機版：改直向排列 */
  .cf-composite {
    flex-direction: column;
  }

  .cf-composite-left {
    width: 100% !important;
  }
}
</style>
