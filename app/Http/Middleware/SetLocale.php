<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    private const LOCALE_MAP = [
        'ZH-TW' => 'zh_TW',
        'ZH-CN' => 'zh_CN',
        'EN-US' => 'en',
        'EN'    => 'en',
    ];

    public function handle(Request $request, Closure $next)
    {
        $queryLocale = strtoupper($request->query('locale', 'ZH-TW'));
        $locale      = self::LOCALE_MAP[$queryLocale] ?? 'zh_TW';

        App::setLocale($locale);

        return $next($request);
    }
}
