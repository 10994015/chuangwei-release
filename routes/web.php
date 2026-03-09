<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// 根目錄 → /home
Route::get('/', function () {
    return redirect('/home');
});

// /{slug}
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-_]+');
