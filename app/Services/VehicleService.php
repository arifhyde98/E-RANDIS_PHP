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
        $cacheKey = 'dashboard.stats.' . ($user?->role?->value ?? 'guest') . '.' . ($user?->opd_id ?? 'global');

        return cache()->remember($cacheKey, 300, function () {
            // Menggunakan kueri agregasi tunggal untuk performa maksimal
            $stats = Vehicle::query()
                ->selectRaw("
                    COUNT(*) as total,
                    SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik,
                    SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan,
                    SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat,
                    SUM(CASE WHEN kondisi IN ('Hilang', 'Dalam Penelusuran') THEN 1 ELSE 0 END) as hilang,
                    SUM(CASE WHEN status = 'Dipinjam' THEN 1 ELSE 0 END) as borrowed
                ")
                ->first();

            return [
                'total' => (int) ($stats->total ?? 0),
                'baik' => (int) ($stats->baik ?? 0),
                'available' => (int) ($stats->baik ?? 0),
                'rusak_ringan' => (int) ($stats->rusak_ringan ?? 0),
                'rusak_berat' => (int) ($stats->rusak_berat ?? 0),
                'hilang' => (int) ($stats->hilang ?? 0),
                'borrowed' => (int) ($stats->borrowed ?? 0),
            ];
        });
    }

    /**
     * Membersihkan cache statistik dashboard secara terarah.
     * 
     * Digunakan setelah operasi CRUD kendaraan atau OPD untuk memastikan
     * data di dashboard tetap akurat tanpa melakukan Cache::flush() global.
     * 
     * @param int|null $opdId ID OPD yang terdampak (opsional)
     * @param int|null $oldOpdId ID OPD lama jika terjadi perpindahan instansi (opsional)
     * @param bool $invalidateAllOpd Jika true, hapus semua cache statistik seluruh OPD
     * @return void
     */
    public function invalidateDashboardStats(?int $opdId = null, ?int $oldOpdId = null, bool $invalidateAllOpd = false): void
    {
        // 1. Selalu hapus key statistik global
        \Illuminate\Support\Facades\Cache::forget('dashboard.stats.superadmin.global');
        \Illuminate\Support\Facades\Cache::forget('dashboard.stats.admin.global');
        \Illuminate\Support\Facades\Cache::forget('dashboard.stats.guest.global');

        // 2. Hapus statistik OPD spesifik atau global OPD role jika ada
        if ($opdId) {
            \Illuminate\Support\Facades\Cache::forget("dashboard.stats.opd.{$opdId}");
        }
        \Illuminate\Support\Facades\Cache::forget("dashboard.stats.opd.global");

        // 3. Hapus statistik OPD lama (kasus pindah instansi)
        if ($oldOpdId && $oldOpdId !== $opdId) {
            \Illuminate\Support\Facades\Cache::forget("dashboard.stats.opd.{$oldOpdId}");
        }

        // 4. Invalidation massal (untuk Import/Truncate/Hapus OPD)
        if ($invalidateAllOpd) {
            // Hapus semua cache yang mungkin ada untuk role OPD
            $opdIds = \App\Models\Opd::pluck('id');
            foreach ($opdIds as $id) {
                \Illuminate\Support\Facades\Cache::forget("dashboard.stats.opd.{$id}");
            }
            
            // Tambahan: Pastikan cache admin/superadmin juga terhapus (sudah di poin 1 tapi dipertegas)
            \Illuminate\Support\Facades\Cache::forget('dashboard.stats.superadmin.global');
            \Illuminate\Support\Facades\Cache::forget('dashboard.stats.admin.global');
        }
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

        // 1. Prioritaskan Exact Match (Sangat cepat jika ada Index)
        $exact = Vehicle::where('no_polisi', $search)->first();
        if ($exact) return $exact;

        // 2. Prioritaskan Prefix Match (Masih bisa menggunakan Index)
        $prefix = Vehicle::where('no_polisi', 'LIKE', "{$search}%")->first();
        if ($prefix) return $prefix;

        // 3. Fallback ke pencarian luas (Lambat - Full Table Scan)
        return Vehicle::where('no_polisi', 'LIKE', "%{$search}%")
            ->orWhere('pemegang', 'LIKE', "%{$query}%")
            ->first();
    }
}

