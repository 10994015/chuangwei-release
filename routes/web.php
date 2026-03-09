<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/site/{templeId}/{slug?}', [PageController::class, 'showWithTempleId']);

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9_-]+');

// 根目錄最後放
Route::get('/', fn() => redirect('/home'));
