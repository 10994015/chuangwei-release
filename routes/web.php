<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// 根目錄 → 由 controller 決定第一個 slug
Route::get('/', [PageController::class, 'home']);
Route::get('/test-route', function () {
    dd('route is working');
});
// /{slug}
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-_]+');
