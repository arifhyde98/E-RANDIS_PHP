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
     * @return array<string, int>
     */
    public function getDashboardStats(): array
    {
        return cache()->remember('dashboard.stats', 300, function () {
            $result = DB::table('vehicles')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik")
                ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan")
                ->selectRaw("SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat")
                ->selectRaw("SUM(CASE WHEN kondisi IN ('Hilang', 'Dalam Penelusuran') THEN 1 ELSE 0 END) as hilang")
                ->selectRaw("SUM(CASE WHEN status = 'Dipinjam' THEN 1 ELSE 0 END) as dipinjam")
                ->first();

            return [
                'total' => (int) ($result->total ?? 0),
                'baik' => (int) ($result->baik ?? 0),
                'available' => (int) ($result->baik ?? 0), // Alias untuk kompatibilitas jika diperlukan
                'rusak_ringan' => (int) ($result->rusak_ringan ?? 0),
                'rusak_berat' => (int) ($result->rusak_berat ?? 0),
                'hilang' => (int) ($result->hilang ?? 0),
                'borrowed' => (int) ($result->dipinjam ?? 0),
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

