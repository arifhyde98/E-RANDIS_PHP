<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\User;

class MasterDataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Master Data hub page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Mock data for summary, in a real app these would be counts from respective models
        $stats = [
            'total_kendaraan' => Vehicle::count(),
            'total_pengguna' => User::count(),
            'total_opd' => 24, // Example count
            'total_sopir' => 12, // Example count
        ];

        return view('master-data.index', compact('stats'));
    }
}
