<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BannerController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebController;

// Serve favicon requests (redirect to svg asset)
Route::get('/favicon.ico', function() {
    return redirect(asset('favicon.svg'));
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['web','auth'])->group(function() {
    Route::get('/', fn() => redirect()->route('banners.index'))->name('dashboard');

    Route::resource('banners', BannerController::class);
    Route::resource('zones', ZoneController::class);
    Route::resource('assignments', AssignmentController::class)->parameters(['assignments' => 'assignment']);
    Route::resource('webs', WebController::class);
    Route::get('/estadisticas', [DashboardController::class, 'index'])->name('estadisticas.index');
    Route::get('/estadisticas/zone-stats', [DashboardController::class, 'zoneStats'])->name('estadisticas.zone_stats');
    Route::get('/estadisticas/series', [DashboardController::class, 'series'])->name('estadisticas.series');
});


require __DIR__.'/auth.php';


