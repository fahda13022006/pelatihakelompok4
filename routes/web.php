<?php

use App\Http\Controllers\GisController;
use Illuminate\Support\Facades\Route;
// Route untuk Halaman Utama Peta dan Endpoint API GeoJSON
Route::get('/', [GisController::class, 'index']);
Route::get('/api/geojson/gadm/{level}',[GisController::class,
'getGadmGeoJson'])->where('level', '[0-4]');

