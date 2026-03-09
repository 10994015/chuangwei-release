<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * 從當前 request hostname 取得 subdomain
     * fk.angkeinfo.com → fk
     * 127.0.0.1 / localhost → 空字串
     */
    private function resolveSubdomain(): string
    {
        $host  = request()->getHost(); // e.g. fk.angkeinfo.com / fk.localhost
        $parts = explode('.', $host);

        // 只有一段（localhost、IP）→ 無 subdomain
        if (count($parts) < 2) return '';

        // fk.localhost 或 fk.angkeinfo.com → 取第一段
        // 純 angkeinfo.com（兩段但無 subdomain）→ 空字串
        // 判斷：第一段不是純 IP，且後面還有內容
        $first = $parts[0];

        // 純 IP（全數字加點）→ 無 subdomain
        if (filter_var($host, FILTER_VALIDATE_IP)) return '';

        // fk.localhost → 有 subdomain
        if (count($parts) === 2 && $parts[1] === 'localhost') return $first;

        // fk.angkeinfo.com（三段以上）→ 有 subdomain
        if (count($parts) >= 3) return $first;

        // angkeinfo.com（兩段，非 localhost）→ 無 subdomain
        return '';
    }

    private function getWebsiteSettings(string $templeId = ''): array
    {
        $subdomain = $this->resolveSubdomain();
        $baseUrl   = $subdomain
            ? "https://{$subdomain}." . config('api.base_domain')
            : config('api.base_url');

        $params   = $templeId ? ['tenantId' => $templeId] : [];
        $response = Http::get($baseUrl . "/api/web-site/", $params);

        if ($response->failed()) return [];

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : [];
    }

    private function getPageContent(string $templeId = '', string $slug = 'home', string $locale = 'ZH-TW'): ?array
    {
        $subdomain = $this->resolveSubdomain();
        $baseUrl   = $subdomain
            ? "https://{$subdomain}." . config('api.base_domain')
            : config('api.base_url');

        $params = ['locale' => $locale];
        if ($templeId) $params['tenantId'] = $templeId;

        $url      = $baseUrl . "/api/web-site/page/{$slug}";
        $response = Http::get($url, $params);

        // Log::debug('getPageContent', [
        //     'url'    => $url,
        //     'params' => $params,
        //     'status' => $response->status(),
        // ]);

        if ($response->failed()) return null;

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : null;
    }

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

    public function show(string $slug = 'home')
    {
        return $this->renderPage('', $slug);
    }

    public function showWithTempleId(string $templeId, string $slug = 'home')
    {
        return $this->renderPage($templeId, $slug);
    }

    public function clearCache(string $templeId)
    {
        return response()->json(['ok' => true]);
    }
}
