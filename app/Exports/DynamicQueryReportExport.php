<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Database\Eloquent\Builder;

/**
 * Mesin Ekspor Laporan Hemat Memori Berbasis Kueri (Streaming Exporter)
 * 
 * Digunakan untuk laporan berukuran besar yang tidak membutuhkan pengayaan data pasca-kueri.
 * Mengalirkan data (streaming) langsung dari kueri database untuk meminimalkan beban RAM.
 */
class DynamicQueryReportExport extends DynamicReportExport implements FromQuery
{
    /**
     * Builder kueri database yang akan dialirkan.
     */
    protected Builder $query;

    /**
     * Inisiasi ekspor kueri ter-optimasi.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<string, string> $headers
     * @param array $filters
     * @param string $reportTitle
     * @param array $docSettings
     */
    public function __construct(Builder $query, array $headers, array $filters = [], string $reportTitle = 'Laporan Kendaraan Dinas', array $docSettings = [])
    {
        parent::__construct($headers, $filters, $reportTitle, $docSettings);
        $this->query = $query;
    }

    /**
     * Mengembalikan kueri pembangun ke Laravel Excel untuk dialirkan secara streaming.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->query;
    }
}
