<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk Logika Bisnis Kendaraan
 * 
 * Menangani operasi kompleks terkait data kendaraan seperti statistik dashboard,
 * pembersihan nomor polisi, dan pencarian khusus.
 */
class VehicleService
{
    /**
     * Mendapatkan statistik dashboard untuk kendaraan.
     * 
     * Data di-cache selama 5 menit untuk performa optimal.
     * 
     * @return array{total: int, available: int, damaged: int, borrowed: int, late: int}
     */
    public function getDashboardStats(): array
    {
        return cache()->remember('dashboard.stats', 300, function () {
            $result = DB::table('vehicles')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN status IN ('Tersedia', 'Aktif', 'aktif') THEN 1 ELSE 0 END) as available")
                ->selectRaw("SUM(CASE WHEN status IN ('Rusak', 'Rusak Berat', 'Rusak Ringan', 'Maintenance', 'maintenance', 'rusak') THEN 1 ELSE 0 END) as damaged")
                ->selectRaw("SUM(CASE WHEN status IN ('Dipinjam', 'dipinjam') THEN 1 ELSE 0 END) as borrowed")
                ->first();

            return [
                'total' => (int) ($result->total ?? 0),
                'available' => (int) ($result->available ?? 0),
                'damaged' => (int) ($result->damaged ?? 0),
                'borrowed' => (int) ($result->borrowed ?? 0),
                'late' => 0,
            ];
        });
    }

    /**
     * Membersihkan dan memformat Nomor Polisi.
     * 
     * Mengubah ke huruf kapital, menghapus karakter non-alfanumerik,
     * dan merapikan spasi.
     * 
     * @param string|null $plate
     * @return string|null
     */
    public function formatPlateNumber(?string $plate): ?string
    {
        if (!$plate) return null;

        // 1. Ubah ke Uppercase & Trim
        $clean = strtoupper(trim($plate));

        // 2. Hapus semua karakter kecuali Huruf (A-Z), Angka (0-9), dan Spasi
        $clean = preg_replace('/[^A-Z0-9\s]/', '', $clean);

        // 3. Ubah spasi ganda menjadi spasi tunggal
        $clean = preg_replace('/\s+/', ' ', $clean);
        
        return $clean;
    }

    /**
     * Mencari kendaraan untuk fitur pencarian di landing page.
     * 
     * @param string|null $query
     * @return \App\Models\Vehicle|null
     */
    public function findForLanding(?string $query): ?Vehicle
    {
        if (!$query) return null;

        $search = $this->formatPlateNumber($query);

        return Vehicle::where('no_polisi', 'LIKE', "%{$search}%")
            ->orWhere('pemegang', 'LIKE', "%{$query}%")
            ->first();
    }
}

