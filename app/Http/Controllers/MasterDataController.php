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
        $stats = [
            'total_kendaraan' => Vehicle::count(),
            'total_pengguna' => User::count(),
            'total_opd' => \App\Models\Opd::count(),
            'total_sopir' => 12, // Still mocked as requested/needed
        ];

        return view('master-data.index', compact('stats'));
    }
}
