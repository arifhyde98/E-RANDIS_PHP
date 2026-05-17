<?php

namespace App\Reports\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Kontrak dasar (Strategy) yang wajib diimplementasikan oleh setiap jenis laporan di E-RANDIS.
 * 
 * Menjamin keseragaman penarikan kueri data dan penentuan header kolom.
 */
interface ReportStrategy
{
    /**
     * Membangun kueri basis data untuk laporan berdasarkan filter yang diberikan.
     *
     * @param array<string, mixed> $filters Kumpulan filter pencarian (kondisi, opd_id, tahun, dll)
     * @return Builder Kueri pembangun Eloquent yang belum dieksekusi (mendukung paginasi/ekspor)
     */
    public function query(array $filters): Builder;

    /**
     * Mendapatkan daftar judul kolom (headers) untuk tabel laporan.
     *
     * @return array<string, string> Asosiasi kunci kolom database ke label header dalam Bahasa Indonesia
     */
    public function headers(): array;
}
