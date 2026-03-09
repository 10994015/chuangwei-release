<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    /**
     * 取得網站設定（SEO、字型、Meta Pixel 等）
     * GET /api/tenant/{tid}/web-site/
     */
    private function getWebsiteSettings(string $templeId): array
    {
        $url      = config('api.base_url') . "/api/web-site/";
        $response = Http::get($url, ['tenantId' => $templeId]);

        if ($response->failed()) return [];

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : [];
    }

    /**
     * 取得頁面內容
     * GET /api/web-site/page/{slug}
     */
    private function getPageContent(string $templeId, string $slug, string $locale): ?array
    {
        $response = Http::get(
            config('api.base_url') . "/api/web-site/page/{$slug}",
            ['tenantId' => $templeId, 'locale' => $locale]
        );

        if ($response->failed()) return null;

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : null;
    }

    /**
     * 顯示頁面
     */
    public function show(string $templeId, string $slug = 'home')
    {
        $locale   = request()->query('locale', 'ZH-TW');
        $settings = $this->getWebsiteSettings($templeId);
        $basemaps = $this->getPageContent($templeId, $slug, $locale);
        if (!$basemaps) abort(404);

        // ── 從 basemaps 解析 header / footer ──────────────────────────
        $headerFrame = null;
        $footerFrame = null;

        foreach ($basemaps as $section) {
            if (($section['bgType'] ?? '') === 'HEADER') {
                $headerFrame = $section['frames'][0]['data'] ?? null;
            }
            if (($section['bgType'] ?? '') === 'FOOTER') {
                $footerFrame = $section['frames'][0]['data'] ?? null;
            }
        }

        // ── tabs 拆成兩欄給 footer columns ────────────────────────────
        $tabs    = $headerFrame['tabs'] ?? [];
        $half    = (int) ceil(count($tabs) / 2);
        $columns = [
            array_slice($tabs, 0, $half),
            array_slice($tabs, $half),
        ];

        $footerData = [
            'tenantName'    => $footerFrame['tenantName']    ?? null,
            'tenantPhone'   => $footerFrame['tenantPhone']   ?? null,
            'tenantAddress' => $footerFrame['tenantAddress'] ?? null,
            'tenantEmail'   => $footerFrame['tenantEmail']   ?? null,
            'columns'       => $columns,
        ];

        return view('page', compact('basemaps', 'settings', 'templeId', 'slug', 'footerData'));
    }

    /**
     * 清除指定宮廟的快取（發布時呼叫）
     * 開發中暫時保留介面，之後加快取時再實作
     */
    public function clearCache(string $templeId)
    {
        return response()->json(['ok' => true]);
    }
}
