<?php

use App\Http\Controllers\GisController;
use App\Http\Controllers\SkineController;
use Illuminate\Support\Facades\Route;

// ==========================================
// DASHBOARD SKINE SEBAGAI HALAMAN UTAMA
// ==========================================

Route::get('/', [SkineController::class, 'dashboard'])
    ->name('home');

Route::get('/dashboard', [SkineController::class, 'dashboard'])
    ->name('dashboard');

// ==========================================
// HALAMAN SKINE
// ==========================================

Route::prefix('skincare')->group(function () {

    Route::get('/dashboard', [SkineController::class, 'dashboard'])
        ->name('skine.dashboard');

    Route::get('/products', [SkineController::class, 'products'])
        ->name('skine.products');

    Route::get('/orders', [SkineController::class, 'orders'])
        ->name('skine.orders');

    Route::get('/distribution', [SkineController::class, 'distribution'])
        ->name('skine.distribution');

    Route::get('/store', [SkineController::class, 'store'])
        ->name('skine.store');

});

// ==========================================
// PRAKTIKUM LAMA WEB GIS (TETAP DIPERTAHANKAN)
// ==========================================

Route::get('/gis', [GisController::class, 'index'])
    ->name('gis.index');

Route::get('/api/geojson/gadm/{level}', [GisController::class, 'getGadmGeoJson'])
    ->where('level', '[0-4]')
    ->name('gis.geojson');