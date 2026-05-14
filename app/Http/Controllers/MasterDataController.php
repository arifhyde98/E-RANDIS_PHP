<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MasterDataController extends Controller implements HasMiddleware
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
     * Show the Master Data hub page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'total_kendaraan' => Vehicle::count(),
            'total_pengguna' => User::count(),
            'total_opd' => \App\Models\Opd::count(),
            'total_sopir' => 12, // Still mocked as requested/needed
        ];

        return view('master-data.index', compact('stats'));
    }
}
