<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Controller untuk Halaman Utama (Dashboard) Admin
 */
class HomeController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    protected $vehicleService;

    /**
     * Mendapatkan middleware yang ditugaskan ke controller ini.
     */
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('auth'),
        ];
    }

    /**
     * Create a new controller instance.
     */
    public function __construct(\App\Services\VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Menampilkan halaman dashboard utama admin dengan statistik dan data terbaru.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->vehicleService->getDashboardStats();
        $latestVehicles = \App\Models\Vehicle::with(['user', 'vehicleType'])->latest()->take(5)->get();
        $activities = \App\Models\Activity::with('user')->latest()->take(10)->get();

        return view('home', compact('stats', 'latestVehicles', 'activities'));
    }
}
