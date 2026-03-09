<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── 開發用：帶 templeId ──────────────────────────────────────────────────
Route::get('/site/{templeId}/{slug?}', [PageController::class, 'showWithTempleId']);

// ── 正式：根目錄 → /home ─────────────────────────────────────────────────
Route::get('/', function () {
    return redirect('/home');
});

// ── 正式：/{slug} ────────────────────────────────────────────────────────
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-_]+');
