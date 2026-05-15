<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Http\Requests\StoreOpdRequest;
use App\Http\Requests\UpdateOpdRequest;
use Illuminate\Http\Request;

/**
 * Controller untuk Manajemen Master Data OPD (Organisasi Perangkat Daerah)
 */
class OpdController extends Controller
{
    protected $accountService;

    public function __construct(\App\Services\AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Menampilkan daftar semua OPD dengan fitur pencarian dan paginasi.
     * 
     * @param StoreOpdRequest $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $query = Opd::query()->with('user');

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
     * @param UpdateOpdRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreOpdRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $opd = Opd::create($validated);
        
        return redirect()->route('opds.index')->with('success', "Data OPD {$opd->nama} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data OPD di database.
     * 
     * @param Request $request
     * @param Opd $opd
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateOpdRequest $request, Opd $opd): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

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

        // Bersihkan cache karena penghapusan OPD akan memicu penghapusan kendaraan (Cascade)
        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('opds.index')->with('success', 'Data OPD berhasil dihapus.');
    }

    /**
     * Mengosongkan seluruh data OPD (Master Data).
     */
    public function truncate(): \Illuminate\Http\RedirectResponse
    {
        // Gunakan get()->each->delete() agar event 'deleting' terpanggil 
        // (untuk hapus avatar user via cascade dan observer)
        \App\Models\Opd::all()->each(function($opd) {
            $opd->delete();
        });

        // Bersihkan cache secara total karena seluruh data kendaraan juga terhapus
        \Illuminate\Support\Facades\Cache::flush();

        return redirect()->route('opds.index')->with('success', 'Seluruh data Master OPD berhasil dikosongkan.');
    }
}
