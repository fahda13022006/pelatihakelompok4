<?php

use App\Http\Controllers\GisController;
use App\Http\Controllers\SkineController;
use Illuminate\Support\Facades\Route;

// Route untuk Halaman Utama Peta dan Endpoint API GeoJSON
Route::get('/', [GisController::class, 'index']);
Route::get('/api/geojson/gadm/{level}', [GisController::class, 'getGadmGeoJson'])->where('level', '[0-4]');

Route::prefix('skincare')->group(function () {
    Route::get('/dashboard', [SkineController::class, 'dashboard'])->name('skine.dashboard');
    Route::get('/products', [SkineController::class, 'products'])->name('skine.products');
    Route::get('/orders', [SkineController::class, 'orders'])->name('skine.orders');
    Route::get('/distribution', [SkineController::class, 'distribution'])->name('skine.distribution');
    Route::get('/store', [SkineController::class, 'store'])->name('skine.store');
});

