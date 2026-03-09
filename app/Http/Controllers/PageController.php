<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    private function getWebsiteSettings(string $templeId = ''): array
    {
        $params   = $templeId ? ['tenantId' => $templeId] : [];
        $response = Http::get(config('api.base_url') . "/api/web-site/", $params);

        if ($response->failed()) return [];

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : [];
    }

    private function getPageContent(string $templeId = '', string $slug = 'home', string $locale = 'ZH-TW'): ?array
    {
        $params = ['locale' => $locale];
        if ($templeId) $params['tenantId'] = $templeId;

        $url      = config('api.base_url') . "/api/web-site/page/{$slug}";
        $response = Http::get($url, $params);

        Log::debug('getPageContent', [
            'url'        => $url,
            'params'     => $params,
            'status'     => $response->status(),
            'body'       => $response->body(),
        ]);

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
