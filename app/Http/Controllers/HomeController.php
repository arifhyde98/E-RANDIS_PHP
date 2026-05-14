<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class HomeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Show the application dashboard.
     */
    public function index(\App\Services\VehicleService $vehicleService): \Illuminate\View\View
    {
        $stats = $vehicleService->getDashboardStats();

        $latestVehicles = \App\Models\Vehicle::latest()->take(6)->get();

        return view('home', compact('stats', 'latestVehicles'));
    }
}
