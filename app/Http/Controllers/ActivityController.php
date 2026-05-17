<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityController extends Controller implements HasMiddleware
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
            new Middleware('role:superadmin'),
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
     * Menghapus seluruh riwayat aktivitas dari database (Truncate).
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        \App\Models\Activity::truncate();
        
        return redirect()->back()->with('success', 'Seluruh riwayat aktivitas berhasil dibersihkan.');
    }
}
