<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// ── Set-Cookie rewriter ────────────────────────────────────
// localhost: strip Domain (cookies don't support subdomain sharing on localhost)
// real domain: rewrite Domain to parent domain so all subdomains share the session
$rewriteSetCookie = function (string $cookieValue): string {
    $host    = request()->getHost(); // e.g. home.angkeinfo.com or localhost
    $isLocal = $host === 'localhost' || str_ends_with($host, '.localhost');
    $isHttps = str_starts_with(config('app.url', ''), 'https');

    if ($isLocal) {
        // strip Domain entirely — bind cookie to current origin only
        $cookieValue = preg_replace('/;\s*Domain=[^;]*/i', '', $cookieValue);
    } else {
        // rewrite Domain to parent domain so xxx.domain.com shares with ooo.domain.com
        $parts      = explode('.', $host);
        $parentDomain = count($parts) >= 2
            ? '.' . implode('.', array_slice($parts, -2))
            : '.' . $host;
        $cookieValue = preg_replace('/;\s*Domain=[^;]*/i', '; Domain=' . $parentDomain, $cookieValue);
        if (false === strpos($cookieValue, 'Domain=')) {
            $cookieValue .= '; Domain=' . $parentDomain;
        }
    }

    if (!$isHttps) {
        $cookieValue = preg_replace('/;\s*Secure/i', '', $cookieValue);
        $cookieValue = preg_replace('/;\s*SameSite=None/i', '; SameSite=Lax', $cookieValue);
    }

    return $cookieValue;
};

// ── API Proxy（解決瀏覽器 CORS）────────────────────────────────
Route::post('/proxy/api/login', function (\Illuminate\Http\Request $request) use ($rewriteSetCookie) {
    $apiBase  = rtrim(config('app.api_base_url', env('API_BASE_URL', '')), '/');
    $isHttps  = str_starts_with(config('app.url', ''), 'https');

    $guzzle = new \GuzzleHttp\Client(['cookies' => false]);
    $psrRes = $guzzle->post($apiBase . '/api/login', [
        'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
        'json'    => $request->all(),
        'http_errors' => false,
    ]);

    \Illuminate\Support\Facades\Log::debug('[proxy/login] status=' . $psrRes->getStatusCode());
    \Illuminate\Support\Facades\Log::debug('[proxy/login] set-cookie=' . json_encode($psrRes->getHeader('set-cookie')));

    $laravelResponse = response((string) $psrRes->getBody(), $psrRes->getStatusCode())
        ->header('Content-Type', 'application/json');

    foreach ($psrRes->getHeader('set-cookie') as $cookieValue) {
        $laravelResponse->headers->set('Set-Cookie', $rewriteSetCookie($cookieValue), false);
    }

    return $laravelResponse;
});

Route::post('/proxy/api/login/out', function (\Illuminate\Http\Request $request) {
    $apiBase = rtrim(config('app.api_base_url', env('API_BASE_URL', '')), '/');
    $guzzle  = new \GuzzleHttp\Client(['cookies' => false]);
    $psrRes  = $guzzle->post($apiBase . '/api/login/out', [
        'headers'     => ['Content-Type' => 'application/json', 'Cookie' => $request->header('Cookie', '')],
        'http_errors' => false,
    ]);

    return response((string) $psrRes->getBody(), $psrRes->getStatusCode())
        ->header('Content-Type', 'application/json');
});

Route::post('/proxy/api/login/google', function (\Illuminate\Http\Request $request) use ($rewriteSetCookie) {
    $apiBase  = 'https://manage.angkeinfo.com';
    $isHttps  = true;
    $url      = $apiBase . '/api/login/google';

    \Illuminate\Support\Facades\Log::debug('[proxy/google] calling: ' . $url);

    $guzzle = new \GuzzleHttp\Client(['cookies' => false]);
    $psrRes = $guzzle->post($url, [
        'headers'     => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
        'json'        => $request->all(),
        'http_errors' => false,
    ]);

    \Illuminate\Support\Facades\Log::debug('[proxy/google] status=' . $psrRes->getStatusCode() . ' body=' . substr((string) $psrRes->getBody(), 0, 300));

    $laravelResponse = response((string) $psrRes->getBody(), $psrRes->getStatusCode())
        ->header('Content-Type', 'application/json');

    foreach ($psrRes->getHeader('set-cookie') as $cookieValue) {
        $laravelResponse->headers->set('Set-Cookie', $rewriteSetCookie($cookieValue), false);
    }

    return $laravelResponse;
});

Route::get('/proxy/api/frontend/user', function (\Illuminate\Http\Request $request) {
    $apiBase  = rtrim(config('app.api_base_url', env('API_BASE_URL', '')), '/');
    $response = Http::withOptions(['cookies' => false])
        ->withHeaders([
            'Content-Type' => 'application/json',
            'Cookie'        => $request->header('Cookie', ''),
        ])->get($apiBase . '/api/frontend/user/');

    return response($response->body(), $response->status())
        ->header('Content-Type', 'application/json');
});

// 根目錄 → 由 controller 決定第一個 slug
Route::get('/', [PageController::class, 'home']);
Route::get('/test-route', function () {
    dd('route is working');
});
// /{slug}
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-_]+');
