<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

/**
 * Mesin Ekspor Laporan Berbasis Koleksi (Collection Exporter)
 * 
 * Digunakan khusus untuk laporan yang membutuhkan modifikasi/pengayaan data in-memory (post-processing)
 * sebelum dialirkan ke dalam dokumen Excel.
 */
class DynamicCollectionReportExport extends DynamicReportExport implements FromCollection
{
    /**
     * Koleksi data hasil laporan ter-enrich.
     */
    protected Collection $collection;

    /**
     * Inisiasi ekspor berbasis koleksi data.
     * 
     * @param \Illuminate\Support\Collection $collection
     * @param array<string, string> $headers
     */
    public function __construct(Collection $collection, array $headers)
    {
        parent::__construct($headers);
        $this->collection = $collection;
    }

    /**
     * Mengembalikan koleksi data ter-enrich ke Laravel Excel.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }
}
