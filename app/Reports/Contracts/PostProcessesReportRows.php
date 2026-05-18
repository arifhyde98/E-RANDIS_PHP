<?php

namespace App\Reports\Contracts;

use Illuminate\Support\Collection;

/**
 * Kontrak tambahan untuk strategi laporan yang membutuhkan pengayaan data pasca-kueri (post-processing).
 * 
 * Digunakan secara elegan untuk mendefinisikan metode pengayaan baris data di memori PHP (in-memory)
 * tanpa mengotori atau melemahkan arsitektur dasar ReportStrategy.
 */
interface PostProcessesReportRows
{
    /**
     * Melakukan pengayaan atau modifikasi data kustom in-memory pada koleksi baris laporan.
     *
     * @param \Illuminate\Support\Collection $vehicles Koleksi data kendaraan yang akan diperkaya
     * @return void
     */
    public function postProcess(Collection $vehicles): void;
}
