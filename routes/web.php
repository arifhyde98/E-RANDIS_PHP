<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

// Landing Page with Search
Route::get('/', [VehicleController::class, 'search'])->name('landing');
Route::get('/vehicle-search', [VehicleController::class, 'searchLandingVehicle'])->name('landing.vehicle-search');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Vehicle Resource Routes (Protected by Auth)
Route::middleware('auth')->group(function () {
    Route::get('vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::get('vehicles/template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.template');
    Route::post('vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');
    Route::post('vehicles/truncate', [VehicleController::class, 'truncate'])->name('vehicles.truncate');
    Route::resource('vehicles', VehicleController::class);

    // Master Data Hub
    Route::get('master-data', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master-data.index');
    Route::post('vehicle-types/cleanup', [\App\Http\Controllers\VehicleTypeController::class, 'cleanup'])->name('vehicle-types.cleanup');
    Route::resource('vehicle-types', \App\Http\Controllers\VehicleTypeController::class);
    Route::resource('opds', \App\Http\Controllers\OpdController::class);
    // Settings CMS
    Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});
