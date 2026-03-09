<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PageController extends Controller
{
    /**
     * 從設定檔解析 templeId（正式環境留空，由 API 從 hostname 判斷）
     */
    private function resolveTempleId(): string
    {
        return config('api.tenant_id', '');
    }

    /**
     * 取得網站設定
     * GET /api/web-site/
     */
    private function getWebsiteSettings(string $templeId): array
    {
        $params   = $templeId ? ['tenantId' => $templeId] : [];
        $response = Http::get(config('api.base_url') . "/api/web-site/", $params);

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
        $params = ['locale' => $locale];
        if ($templeId) $params['tenantId'] = $templeId;

        $response = Http::get(
            config('api.base_url') . "/api/web-site/page/{$slug}",
            $params
        );

        if ($response->failed()) return null;

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : null;
    }

    /**
     * 共用渲染邏輯
     */
    private function renderPage(string $templeId, string $slug): \Illuminate\View\View
    {
        $locale   = request()->query('locale', 'ZH-TW');
        $settings = $this->getWebsiteSettings($templeId);
        $basemaps = $this->getPageContent($templeId, $slug, $locale);

        if (!$basemaps) abort(404);

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
     * 正式路由：GET /{slug}
     * templeId 由 config/env 或 hostname 決定
     */
    public function show(string $slug = 'home')
    {
        return $this->renderPage($this->resolveTempleId(), $slug);
    }

    /**
     * 開發路由：GET /site/{templeId}/{slug?}
     */
    public function showWithTempleId(string $templeId, string $slug = 'home')
    {
        return $this->renderPage($templeId, $slug);
    }

    /**
     * 清除快取（發布時呼叫）
     */
    public function clearCache(string $templeId)
    {
        return response()->json(['ok' => true]);
    }
}
