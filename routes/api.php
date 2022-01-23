<?php

use App\Http\Controllers\BrandStationsController;
use App\Http\Controllers\StationsController;
use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api'])->group(function() {
    Route::get('stations', [StationsController::class, 'stations']);
    Route::post('sort-stations', [StationsController::class, 'sortStations']);
    Route::post('create-branded-station', [BrandStationsController::class, 'store'])->middleware('verify.header');

    Route::get('brand-stations/{brandStation}', [BrandStationsController::class, 'brandStations'])->name('brand.stations.deeplink');
});




