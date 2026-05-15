<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Menghapus seluruh riwayat aktivitas.
     */
    public function clear()
    {
        \App\Models\Activity::truncate();
        
        return redirect()->back()->with('success', 'Seluruh riwayat aktivitas berhasil dibersihkan.');
    }
}
