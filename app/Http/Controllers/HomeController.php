<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Controller untuk Halaman Utama (Dashboard) Admin
 */
class HomeController extends Controller implements HasMiddleware
{
    /**
     * Mendapatkan middleware yang ditugaskan ke controller ini.
     * 
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Menampilkan halaman dashboard utama admin dengan statistik dan data terbaru.
     * 
     * @param \App\Services\VehicleService $vehicleService
     * @return \Illuminate\View\View
     */
    public function index(\App\Services\VehicleService $vehicleService): \Illuminate\View\View
    {
        $stats = $vehicleService->getDashboardStats();

        $latestVehicles = \App\Models\Vehicle::latest()->take(6)->get();

        return view('home', compact('stats', 'latestVehicles'));
    }
}

