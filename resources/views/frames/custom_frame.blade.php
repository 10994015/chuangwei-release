{{-- resources/views/frames/custom_frame.blade.php --}}
@php
  use App\Helpers\FrameHelper;

  $frameType = $frame['type'] ?? '';
  $frameData = $frame['data'] ?? [];
  $elements  = $frame['elements'] ?? [];

  $themeCssVars = FrameHelper::resolveTextThemeCssVars($frameData);

  $layoutMap = [
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
  $deviceKey    = FrameHelper::resolveDeviceKey();
  $responsiveCss = '';

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
    $isLeftBig = $layout['side'] === 'left';
    $leftWidth = $isLeftBig
      ? (!empty($elements[0]['width']) ? $elements[0]['width'] : '66.7%')
      : (!empty($elements[0]['width']) ? $elements[0]['width'] : '33.3%');
  }
@endphp

{{-- ==================== 複合框架 A/B/C/D ==================== --}}
@if($layoutType === 'composite')
  @php
    $leftIdxs  = $layout['leftIdxs'];
    $rightIdxs = $layout['rightIdxs'];
  @endphp
  <div class="cf-frame-container" style="{{ $themeCssVars }}">
    <div class="custom-frame cf-composite">

      <div class="cf-composite-left" style="width: {{ $leftWidth }};">
        @foreach($leftIdxs as $idx)
          @php
            $el      = FrameHelper::resolveElementVars($elements[$idx] ?? [], $deviceKey);
            $padClass = 'cfp-' . md5(uniqid('', true));
            $responsiveCss .= FrameHelper::responsivePaddingCss($padClass, $el['rawPadding'], 0);
          @endphp
          <div class="cf-col {{ $padClass }}" style="{{ $el['metaStyle'] }}">
            @include('frames._element', ['el' => $el])
          </div>
        @endforeach
      </div>

      <div class="cf-composite-right">
        @foreach($rightIdxs as $idx)
          @php
            $el      = FrameHelper::resolveElementVars($elements[$idx] ?? [], $deviceKey);
            $padClass = 'cfp-' . md5(uniqid('', true));
            $responsiveCss .= FrameHelper::responsivePaddingCss($padClass, $el['rawPadding'], 0);
          @endphp
          <div class="cf-col {{ $padClass }}" style="{{ $el['metaStyle'] }}">
            @include('frames._element', ['el' => $el])
          </div>
        @endforeach
      </div>

    </div>
  </div>

{{-- ==================== 單層 / 雙層框架 ==================== --}}
@else
  <div class="cf-frame-container" style="{{ $themeCssVars }}">
    <div class="custom-frame {{ $gridClass }}" style="{{ $inlineGridStyle }}">
      @foreach($elements as $element)
        @php
          $el       = FrameHelper::resolveElementVars($element, $deviceKey);
          $padClass = 'cfp-' . md5(uniqid('', true));
          $responsiveCss .= FrameHelper::responsivePaddingCss($padClass, $el['rawPadding'], 0);
        @endphp
        <div class="cf-col {{ $padClass }}" style="{{ $el['metaStyle'] }}">
          @include('frames._element', ['el' => $el])
        </div>
      @endforeach
    </div>
  </div>
@endif

<style>
{!! $responsiveCss !!}
.cf-col { box-sizing: border-box; overflow: hidden; }
.cf-frame-container { max-width: 1200px; margin: 0 auto; width: 100%; }
.custom-frame { display: grid; width: 100%; gap: 0; }
.cf-grid-1-1 { grid-template-columns: 1fr; }
.cf-grid-1-2 { grid-template-columns: repeat(2, 1fr); }
.cf-grid-1-3 { grid-template-columns: repeat(3, 1fr); }
.cf-grid-1-4 { grid-template-columns: repeat(4, 1fr); }
.cf-grid-2-2 { grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, auto); }
.cf-grid-2-3 { grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, auto); }
.cf-grid-2-4 { grid-template-columns: repeat(4, 1fr); grid-template-rows: repeat(2, auto); }
.cf-composite { display: flex; width: 100%; align-items: stretch; }
.cf-composite-left { flex-shrink: 0; display: flex; flex-direction: column; }
.cf-composite-left .cf-col:only-child { flex: 1; }
.cf-composite-right { flex: 1; min-width: 0; display: flex; flex-direction: column; }
.cf-composite-right .cf-col { height: auto; }

@media (min-width: 769px) and (max-width: 1024px) {
  .cf-grid-1-2, .cf-grid-1-3, .cf-grid-1-4 { grid-template-columns: repeat(2, 1fr) !important; }
  .cf-grid-2-4 { grid-template-columns: repeat(2, 1fr) !important; grid-template-rows: unset !important; }
}
@media (max-width: 768px) {
  .cf-grid-1-2, .cf-grid-1-3, .cf-grid-1-4 { grid-template-columns: 1fr !important; }
  .cf-grid-2-2, .cf-grid-2-3, .cf-grid-2-4 { grid-template-columns: 1fr !important; grid-template-rows: unset !important; }
  .cf-composite { flex-direction: column; }
  .cf-composite-left { width: 100% !important; }
}
</style>
