<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    /**
     * 取得網站設定（SEO、字型、Meta Pixel 等）
     * GET /api/tenant/{tid}/web-site/
     */
    private function getWebsiteSettings(string $templeId): array
    {
        $cacheKey = "website_settings:{$templeId}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($templeId) {
            $response = Http::get(config('api.base_url') . "/api/tenant/{$templeId}/web-site/");

            if ($response->failed()) return [];

            $result = $response->json();

            return ($result['statusCode'] === 200 && isset($result['data']))
                ? $result['data']
                : [];
        });
    }

    /**
     * 取得頁面內容
     * GET /api/tenant/{tid}/web-site/draft-page/{slug}
     */
    private function getPageContent(string $templeId, string $slug, string $locale): ?array
    {
        $cacheKey = "page:{$templeId}:{$slug}:{$locale}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($templeId, $slug, $locale) {
            $response = Http::get(
                config('api.base_url') . "/api/tenant/{$templeId}/web-site/draft-page/{$slug}",
                ['locale' => $locale]
            );

            if ($response->failed()) return null;

            $result = $response->json();

            return ($result['statusCode'] === 200 && isset($result['data']))
                ? $result['data']
                : null;
        });
    }

    /**
     * 顯示頁面
     */
    public function show(string $templeId, string $slug = 'home')
    {
        $locale = request()->query('locale', 'ZH-TW');

        // 同時取得網站設定和頁面內容
        $settings = $this->getWebsiteSettings($templeId);
        $basemaps = $this->getPageContent($templeId, $slug, $locale);

        if (!$basemaps) {
            abort(404);
        }

        return view('page', compact('basemaps', 'settings', 'templeId', 'slug'));
    }

    /**
     * 清除指定宮廟的快取（發布時呼叫）
     */
    public function clearCache(string $templeId)
    {
        // 清網站設定快取
        Cache::forget("website_settings:{$templeId}");

        // 清所有頁面快取（常見的 slug）
        $slugs   = ['home', 'about-us', 'products', 'events', 'news', 'album', 'donation', 'light'];
        $locales = ['ZH-TW', 'EN'];

        foreach ($slugs as $slug) {
            foreach ($locales as $locale) {
                Cache::forget("page:{$templeId}:{$slug}:{$locale}");
            }
        }

        return response()->json(['ok' => true]);
    }
}
