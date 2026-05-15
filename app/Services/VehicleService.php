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
        $user = auth()->user();
        $cacheKey = 'dashboard.stats.' . ($user->role->value ?? 'guest') . '.' . ($user->opd_id ?? 'global');

        return cache()->remember($cacheKey, 300, function () {
            $query = Vehicle::query();

            return [
                'total' => (int) $query->count(),
                'baik' => (int) $query->clone()->where('kondisi', 'Baik')->count(),
                'available' => (int) $query->clone()->where('kondisi', 'Baik')->count(),
                'rusak_ringan' => (int) $query->clone()->where('kondisi', 'Rusak Ringan')->count(),
                'rusak_berat' => (int) $query->clone()->where('kondisi', 'Rusak Berat')->count(),
                'hilang' => (int) $query->clone()->whereIn('kondisi', ['Hilang', 'Dalam Penelusuran'])->count(),
                'borrowed' => (int) $query->clone()->where('status', 'Dipinjam')->count(),
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

