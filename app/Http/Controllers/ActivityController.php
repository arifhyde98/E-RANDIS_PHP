<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class ActivityController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            'role:superadmin',
        ];
    }

    /**
     * Menampilkan daftar riwayat aktivitas sistem.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $activities = \App\Models\Activity::with('user')
            ->latest()
            ->paginate(20);

        return view('activities.index', compact('activities'));
    }

    /**
     * Menghapus seluruh riwayat aktivitas.
     */
    public function clear()
    {
        \App\Models\Activity::truncate();
        
        return redirect()->back()->with('success', 'Seluruh riwayat aktivitas berhasil dibersihkan.');
    }
}
