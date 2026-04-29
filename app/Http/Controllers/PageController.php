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

    private function getLocales(): array
    {
        $response = Http::get($this->buildBaseUrl() . "/api/web-site/locale");

        if ($response->failed()) return [];

        $result = $response->json();

        return ($result['statusCode'] === 200 && !empty($result['data']))
            ? $result['data']
            : [];
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
            'params' => ['locale' => $locale],
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if ($response->failed()) return null;

        $result = $response->json();

        if ($result['statusCode'] !== 200 || !isset($result['data'])) return null;

        return $result['data'];
    }

    /**
     * 取得所有頁面清單，回傳第一個 slug
     */
    private function getFirstSlug(string $locale): string
    {
        $url      = $this->buildBaseUrl() . "/api/web-site/page/home";
        $response = Http::get($url, ['locale' => $locale]);

        if ($response->failed()) return 'home';

        $result = $response->json();

        if ($result['statusCode'] !== 200 || empty($result['data'])) return 'home';

        $contentJson = $result['data']['contentJson'] ?? [];

        foreach ($contentJson as $section) {
            $bgType = $section['bgType'] ?? '';
            foreach ($section['frames'] ?? [] as $frame) {
                if (in_array($bgType, ['HEADER', 'PV_HEADER']) || in_array($frame['type'] ?? '', ['PV_HEADER'])) {
                    $firstSlug = $frame['data']['tabs'][0]['slug'] ?? null;
                    if ($firstSlug) return $firstSlug;
                }
            }
        }

        return 'home';
    }

    /**
     * 根目錄：自動跳到後端第一個 slug
     */
    public function home()
    {
        $locale    = request()->query('locale', 'ZH-TW');
        $firstSlug = $this->getFirstSlug($locale);

        return redirect("/{$firstSlug}?locale={$locale}");
    }

    private function renderPage(string $slug): \Illuminate\View\View
    {
        $locale   = request()->query('locale', 'ZH-TW');
        $settings = $this->getWebsiteSettings();
        $locales  = $this->getLocales();
        $pageData = $this->getPageContent($slug, $locale);

        if (!$pageData) abort(404);

        $basemaps = $pageData['contentJson'] ?? null;

        if (!$basemaps) abort(404);

        $pageMeta = [
            'seoTitle'       => $pageData['seoTitle']       ?? null,
            'seoDescription' => $pageData['seoDescription'] ?? null,
            'seoKeywords'    => $pageData['seoKeywords']    ?? null,
        ];

        $headerFrame = null;
        $footerFrame = null;

        $headerTypes = ['HEADER', 'PV_HEADER'];
        $footerTypes = ['FOOTER', 'PV_FOOTER'];

        foreach ($basemaps as $section) {
            $bgType = $section['bgType'] ?? '';

            if (in_array($bgType, $headerTypes) && $headerFrame === null) {
                $headerFrame = $section['frames'][0]['data'] ?? null;
            }

            if (in_array($bgType, $footerTypes) && $footerFrame === null) {
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

        $templeId = '';

        return view('page', compact('basemaps', 'settings', 'pageMeta', 'templeId', 'slug', 'footerData', 'locales'));
    }

    public function show(string $slug = 'home')
    {
        return $this->renderPage($slug);
    }

    public function productDetail(string $id)
    {
        $locale   = request()->query('locale', 'ZH-TW');
        $settings = $this->getWebsiteSettings();
        $locales  = $this->getLocales();
        $apiBase  = $this->buildBaseUrl();

        // 商品詳情
        $productRes = Http::withOptions(['cookies' => false])
            ->withHeaders(['Cookie' => request()->header('Cookie', '')])
            ->get($apiBase . "/api/product/all/{$id}");

        if ($productRes->failed() || $productRes->json('statusCode') !== 200) {
            abort(404);
        }

        $product = $productRes->json('data');

        // 推薦商品（同類型，排除自己，取 5 筆）
        $recRes = Http::withOptions(['cookies' => false])
            ->get($apiBase . '/api/product/all', [
                'pageSize' => 6,
                'page'     => 1,
            ]);

        $recRaw = ($recRes->ok() ? $recRes->json('data.data') : []) ?? [];
        $recommended = array_values(
            array_filter($recRaw, fn($p) => ($p['id'] ?? '') !== $id)
        );
        $recommended = array_slice($recommended, 0, 5);

        // 取得 Header / Footer frame（從 home 頁）
        $homeData    = $this->getPageContent('home', $locale);
        $basemaps    = $homeData['contentJson'] ?? [];
        $headerFrame = null;
        $footerFrame = null;

        foreach ($basemaps as $section) {
            $bgType = $section['bgType'] ?? '';
            if (in_array($bgType, ['HEADER', 'PV_HEADER']) && $headerFrame === null) {
                $headerFrame = ['type' => $bgType, 'data' => $section['frames'][0]['data'] ?? []];
            }
            if (in_array($bgType, ['FOOTER', 'PV_FOOTER']) && $footerFrame === null) {
                $footerFrame = ['type' => $bgType, 'data' => $section['frames'][0]['data'] ?? []];
            }
        }

        $slug     = request()->query('from', 'home');
        $pageMeta = ['seoTitle' => ($product['nameZhTw'] ?? '') . ' - ' . ($settings['seoTitle'] ?? '')];

        return view('product.show', compact(
            'product', 'recommended', 'headerFrame', 'footerFrame',
            'settings', 'locales', 'locale', 'slug', 'pageMeta'
        ));
    }

    public function templeProductDetail(string $id)
    {
        $locale   = request()->query('locale', 'ZH-TW');
        $settings = $this->getWebsiteSettings();
        $locales  = $this->getLocales();
        $apiBase  = $this->buildBaseUrl();

        // 商品詳情
        $productRes = Http::withOptions(['cookies' => false])
            ->withHeaders(['Cookie' => request()->header('Cookie', '')])
            ->get($apiBase . "/api/product/temple/{$id}");

        if ($productRes->failed() || $productRes->json('statusCode') !== 200) {
            abort(404);
        }

        $product = $productRes->json('data');

        // 推薦商品（同頁列表，排除自己，取 5 筆）
        $recRes = Http::withOptions(['cookies' => false])
            ->withHeaders(['Cookie' => request()->header('Cookie', '')])
            ->get($apiBase . '/api/product/temple', ['pageSize' => 6, 'page' => 1]);

        $recRaw      = ($recRes->ok() ? $recRes->json('data.data') : []) ?? [];
        $recommended = array_slice(
            array_values(array_filter($recRaw, fn($p) => ($p['id'] ?? '') !== $id)),
            0, 5
        );

        // Header / Footer（從 home 頁）
        $homeData    = $this->getPageContent('home', $locale);
        $basemaps    = $homeData['contentJson'] ?? [];
        $headerFrame = null;
        $footerFrame = null;

        foreach ($basemaps as $section) {
            $bgType = $section['bgType'] ?? '';
            if (in_array($bgType, ['HEADER', 'PV_HEADER']) && $headerFrame === null) {
                $headerFrame = ['type' => $bgType, 'data' => $section['frames'][0]['data'] ?? []];
            }
            if (in_array($bgType, ['FOOTER', 'PV_FOOTER']) && $footerFrame === null) {
                $footerFrame = ['type' => $bgType, 'data' => $section['frames'][0]['data'] ?? []];
            }
        }

        $slug     = request()->query('from', 'home');
        $pageMeta = ['seoTitle' => ($product['nameZhTw'] ?? '') . ' - ' . ($settings['seoTitle'] ?? '')];

        return view('product.show', compact(
            'product', 'recommended', 'headerFrame', 'footerFrame',
            'settings', 'locales', 'locale', 'slug', 'pageMeta'
        ));
    }

    public function clearCache()
    {
        return response()->json(['ok' => true]);
    }
}
