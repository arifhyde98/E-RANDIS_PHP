<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

// Landing Page with Search
Route::get('/', [VehicleController::class, 'search'])->name('landing');
Route::get('/vehicle-search', [VehicleController::class, 'searchLandingVehicle'])->name('landing.vehicle-search');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // Profil Pengguna
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Vehicle Resource Routes (Semua Role Punya Akses Dasar)
    Route::get('vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::get('vehicles/template', [VehicleController::class, 'downloadTemplate'])->name('vehicles.template');
    Route::post('vehicles/import', [VehicleController::class, 'import'])->name('vehicles.import');
    
    // Hanya Superadmin yang boleh mengosongkan seluruh data
    Route::post('vehicles/truncate', [VehicleController::class, 'truncate'])->middleware('role:superadmin')->name('vehicles.truncate');
    
    Route::resource('vehicles', VehicleController::class)->except(['create', 'edit', 'show']);

    // Master Data Hub (Hanya Superadmin & Admin BMD)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::get('master-data', [\App\Http\Controllers\MasterDataController::class, 'index'])->name('master-data.index');
        Route::post('vehicle-types/cleanup', [\App\Http\Controllers\VehicleTypeController::class, 'cleanup'])->name('vehicle-types.cleanup');
        Route::resource('vehicle-types', \App\Http\Controllers\VehicleTypeController::class)->except(['create', 'edit', 'show']);
        Route::delete('opds/truncate', [\App\Http\Controllers\OpdController::class, 'truncate'])->name('opds.truncate');
        Route::resource('opds', \App\Http\Controllers\OpdController::class)->except(['create', 'edit', 'show']);
    });

    // Settings & User Management (Khusus Superadmin)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        
        Route::post('users/generate-opd-accounts', [\App\Http\Controllers\UserController::class, 'generateAllOpdAccounts'])->name('users.generate-opd-accounts');
        Route::post('users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'edit', 'show']);
    });
});
