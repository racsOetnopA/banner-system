<?php

use App\Http\Controllers\Api\BannerApiController;
use App\Http\Controllers\Api\TrackController;
use Illuminate\Support\Facades\Route;


Route::get('/banners', [BannerApiController::class, 'show']);
Route::get('/track/view/{id}', [TrackController::class, 'view']);
Route::get('/track/click/{id}', [TrackController::class, 'click']);
