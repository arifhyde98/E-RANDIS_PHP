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
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'total' => \App\Models\Vehicle::count(),
            'available' => \App\Models\Vehicle::whereIn('status', ['Tersedia', 'Aktif', 'aktif'])->count(),
            'borrowed' => \App\Models\Vehicle::whereIn('status', ['Dipinjam', 'dipinjam'])->count(),
            'damaged' => \App\Models\Vehicle::whereIn('status', ['Rusak', 'Maintenance', 'maintenance'])->count(),
            'late' => 2, // Placeholder for now as late return logic might not exist yet
        ];

        $latestVehicles = \App\Models\Vehicle::latest()->take(6)->get();

        return view('home', compact('stats', 'latestVehicles'));
    }
}
