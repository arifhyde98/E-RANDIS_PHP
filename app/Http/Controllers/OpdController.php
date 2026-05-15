<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\Request;

/**
 * Controller untuk Manajemen Master Data OPD (Organisasi Perangkat Daerah)
 */
class OpdController extends Controller
{
    /**
     * Menampilkan daftar semua OPD dengan fitur pencarian dan paginasi.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $query = Opd::query();

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%')
                  ->orWhere('singkatan', 'like', '%' . $request->q . '%');
        }

        $opds = $query->orderBy('nama')->paginate(15);
        
        return view('opds.index', compact('opds'));
    }

    /**
     * Menyimpan data OPD baru ke database.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|unique:opds,nama',
            'singkatan' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        Opd::create($validated);

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil ditambahkan.');
    }

    /**
     * Memperbarui data OPD di database.
     * 
     * @param Request $request
     * @param Opd $opd
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Opd $opd): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|unique:opds,nama,' . $opd->id,
            'singkatan' => 'nullable|string',
            'alamat' => 'nullable|string',
        ]);

        $opd->update($validated);

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil diperbarui.');
    }

    /**
     * Menghapus data OPD dari database.
     * 
     * @param Opd $opd
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Opd $opd): \Illuminate\Http\RedirectResponse
    {
        // Untuk saat ini langsung hapus (Master Data)
        $opd->delete();

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil dihapus.');
    }
}

