<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'proxy/api/login',
        'proxy/api/login/out',
        'proxy/api/login/google',
        'proxy/api/frontend/user',
    ];
}
