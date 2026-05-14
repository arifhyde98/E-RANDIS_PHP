<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleService
{
    /**
     * Get dashboard statistics for vehicles.
     */
    public function getDashboardStats(): array
    {
        return [
            'total' => Vehicle::count(),
            'available' => Vehicle::whereIn('status', ['Tersedia', 'Aktif', 'aktif'])->count(),
            'damaged' => Vehicle::whereIn('status', ['Rusak', 'Rusak Berat', 'Rusak Ringan', 'Maintenance', 'maintenance', 'rusak'])->count(),
            'borrowed' => Vehicle::whereIn('status', ['Dipinjam', 'dipinjam'])->count(),
        ];
    }

    /**
     * Clean and format plate number (Nomor Polisi).
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
     * Find vehicle for landing page search.
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
