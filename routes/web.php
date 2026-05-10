<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

// Landing Page with Search
Route::get('/', [VehicleController::class, 'search'])->name('landing');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Vehicle Resource Routes (Protected by Auth)
Route::middleware('auth')->group(function () {
    Route::get('vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::get('vehicles/template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.template');
    Route::post('vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');
    Route::resource('vehicles', VehicleController::class);
});
