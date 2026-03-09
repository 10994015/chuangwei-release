<?php
// app/Helpers/FrameHelper.php
// 在 composer.json autoload.files 加入，或在 AppServiceProvider boot() 用 require_once 載入
// "autoload": { "files": ["app/Helpers/FrameHelper.php"] }

namespace App\Helpers;

class FrameHelper
{
    // ==================== 文字色主題 Presets（同 Vue THEME_PRESETS）====================
    public static array $themePresets = [
        'light' => [
            '--frame-text-color'     => '#333333',
            '--frame-text-secondary' => '#666666',
            '--frame-text-muted'     => '#999999',
            '--frame-link-color'     => '#8b6f47',
            '--frame-heading-color'  => '#222222',
            '--frame-tag-bg'         => 'rgba(0,0,0,0.06)',
            '--frame-card-bg'        => '#ffffff',
            '--frame-border-color'   => '#e5e5e5',
        ],
        'dark' => [
            '--frame-text-color'     => '#f0f0f0',
            '--frame-text-secondary' => 'rgba(255,255,255,0.72)',
            '--frame-text-muted'     => 'rgba(255,255,255,0.45)',
            '--frame-link-color'     => '#f5d9b0',
            '--frame-heading-color'  => '#ffffff',
            '--frame-tag-bg'         => 'rgba(255,255,255,0.15)',
            '--frame-card-bg'        => 'rgba(255,255,255,0.08)',
            '--frame-border-color'   => 'rgba(255,255,255,0.15)',
        ],
        'sepia' => [
            '--frame-text-color'     => '#4a3728',
            '--frame-text-secondary' => '#7a6050',
            '--frame-text-muted'     => '#a08878',
            '--frame-link-color'     => '#8b4513',
            '--frame-heading-color'  => '#3a2818',
            '--frame-tag-bg'         => 'rgba(139,111,71,0.12)',
            '--frame-card-bg'        => '#fdf6ee',
            '--frame-border-color'   => '#d4b896',
        ],
    ];

    /**
     * 從 frame.data 解析文字色主題，回傳 CSS 變數字串
     * 供系統框架 / 自訂框架 wrapper div 的 style 屬性使用
     *
     * @param  array  $frameData  frame['data']
     * @return string  e.g. "--frame-text-color: #333; --frame-link-color: #8b6f47; ..."
     */
    public static function resolveTextThemeCssVars(array $frameData): string
    {
        $theme = $frameData['textTheme'] ?? 'light';

        if ($theme === 'custom') {
            $color = $frameData['textColor'] ?? '#333333';
            $hex   = ltrim($color, '#');
            // 不足 6 碼時 fallback
            if (strlen($hex) < 6) {
                $vars = self::$themePresets['light'];
            } else {
                $r   = hexdec(substr($hex, 0, 2));
                $g   = hexdec(substr($hex, 2, 2));
                $b   = hexdec(substr($hex, 4, 2));
                $lum = ($r * 299 + $g * 587 + $b * 114) / 1000;
                $dark = $lum < 128;
                $vars = [
                    '--frame-text-color'     => $color,
                    '--frame-text-secondary' => $dark ? 'rgba(255,255,255,0.65)' : 'rgba(0,0,0,0.55)',
                    '--frame-text-muted'     => $dark ? 'rgba(255,255,255,0.4)'  : 'rgba(0,0,0,0.38)',
                    '--frame-link-color'     => $color,
                    '--frame-heading-color'  => $color,
                    '--frame-tag-bg'         => $dark ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.07)',
                    '--frame-card-bg'        => $dark ? 'rgba(255,255,255,0.08)' : '#ffffff',
                    '--frame-border-color'   => $dark ? 'rgba(255,255,255,0.18)' : 'rgba(0,0,0,0.1)',
                ];
            }
        } else {
            $vars = self::$themePresets[$theme] ?? self::$themePresets['light'];
        }

        return implode('; ', array_map(
            fn($k, $v) => "{$k}: {$v}",
            array_keys($vars),
            array_values($vars)
        ));
    }

    /**
     * 回傳 FOOTER 的 wrapper inline style 字串
     * background + --footer-text-color CSS 變數
     */
    public static function resolveFooterStyle(array $frameData): string
    {
        $bg   = $frameData['footerBgColor']   ?? '#2d2d2d';
        $text = $frameData['footerTextColor'] ?? '#ffffff';
        return "background: {$bg}; --footer-text-color: {$text};";
    }

    /**
     * 回傳 INDEX_DONATION 的 wrapper inline style 字串
     * background（支援漸層）+ --donation-text-color CSS 變數
     */
    public static function resolveDonationStyle(array $frameData): string
    {
        $bg   = $frameData['donationBgColor']   ?? 'linear-gradient(135deg, #8b7355 0%, #a0826d 100%)';
        $text = $frameData['donationTextColor'] ?? '#ffffff';
        return "background: {$bg}; --donation-text-color: {$text};";
    }

    /**
     * 解析單一 element 的樣式變數
     * 供 custom_frame.blade.php 使用，避免在 Blade 內宣告函式導致重複宣告錯誤
     */
    public static function resolveElementVars(array $element): array
    {
        $meta    = $element['metadata'] ?? [];
        $padding = $element['padding']  ?? ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20];

        $pt = $padding['top']    ?? 20;
        $pr = $padding['right']  ?? 20;
        $pb = $padding['bottom'] ?? 20;
        $pl = $padding['left']   ?? 20;

        $color      = $meta['color']           ?? null;
        $fontSize   = $meta['fontSize']        ?? $meta['font_size']        ?? null;
        $fontWeight = $meta['fontWeight']      ?? $meta['font_weight']      ?? null;
        $textAlign  = $meta['textAlign']       ?? $meta['text_align']       ?? null;
        $bgColor    = $meta['backgroundColor'] ?? $meta['background_color'] ?? null;

        $metaStyle = '';
        if ($color)      $metaStyle .= "color: {$color};";
        if ($fontSize)   $metaStyle .= "font-size: {$fontSize};";
        if ($fontWeight) $metaStyle .= "font-weight: {$fontWeight};";
        if ($textAlign)  $metaStyle .= "text-align: {$textAlign};";
        if ($bgColor)    $metaStyle .= "background-color: {$bgColor};";

        return [
            'type'         => $element['type']  ?? '',
            'value'        => $element['value'] ?? [],
            'meta'         => $meta,
            'paddingStyle' => "padding: {$pt}px {$pr}px {$pb}px {$pl}px;",
            'metaStyle'    => $metaStyle,
        ];
    }
}
