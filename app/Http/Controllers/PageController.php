<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    private function resolveSubdomain(): string
    {
        $host  = request()->getHost();
        $parts = explode('.', $host);

        if (count($parts) < 2) return '';
        if (filter_var($host, FILTER_VALIDATE_IP)) return '';

        // fk.localhost
        if (count($parts) === 2 && $parts[1] === 'localhost') return $parts[0];

        // fk.angkeinfo.com
        if (count($parts) >= 3) return $parts[0];

        return '';
    }

    private function buildBaseUrl(): string
    {
        $subdomain = $this->resolveSubdomain();

        return $subdomain
            ? "https://{$subdomain}." . config('api.base_domain')
            : config('api.base_url');
    }

    private function getWebsiteSettings(): array
    {
        $response = Http::get($this->buildBaseUrl() . "/api/web-site/");

        if ($response->failed()) return [];

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : [];
    }

    private function getPageContent(string $slug, string $locale): ?array
    {
        $url      = $this->buildBaseUrl() . "/api/web-site/page/{$slug}";
        $response = Http::get($url, ['locale' => $locale]);

        Log::debug('getPageContent', [
            'url'    => $url,
            'status' => $response->status(),
        ]);

        if ($response->failed()) return null;

        $result = $response->json();

        return ($result['statusCode'] === 200 && isset($result['data']))
            ? $result['data']
            : null;
    }

    private function renderPage(string $slug): \Illuminate\View\View
    {
        $locale   = request()->query('locale', 'ZH-TW');
        $settings = $this->getWebsiteSettings();
        $basemaps = $this->getPageContent($slug, $locale);

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

        $templeId = ''; // 正式環境不需要，保留供 view 相容

        return view('page', compact('basemaps', 'settings', 'templeId', 'slug', 'footerData'));
    }

    public function show(string $slug = 'home')
    {
        return $this->renderPage($slug);
    }

    public function clearCache()
    {
        return response()->json(['ok' => true]);
    }
}
