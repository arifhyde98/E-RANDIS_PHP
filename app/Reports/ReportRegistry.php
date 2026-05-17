<?php

namespace App\Reports;

use App\Reports\Contracts\ReportStrategy;
use App\Reports\Strategies\VehicleStatusReport;
use App\Reports\Strategies\OpdAssetReport;
use App\Reports\Strategies\DocumentValidityReport;
use InvalidArgumentException;

/**
 * Pendaftar (Registry) dinamis untuk tipe strategi laporan di E-RANDIS.
 * 
 * Memetakan tipe laporan dari parameter input langsung ke kelas strategi yang sesuai.
 */
class ReportRegistry
{
    /**
     * Daftar pemetaan tipe laporan ke kelas strateginya.
     *
     * @var array<string, string>
     */
    protected array $strategies = [
        'status'   => VehicleStatusReport::class,
        'opd'      => OpdAssetReport::class,
        'document' => DocumentValidityReport::class,
    ];

    /**
     * Menyelesaikan (resolve) instansi strategi laporan berdasarkan tipe string.
     *
     * @param string $type Tipe laporan ('status', 'opd', 'document')
     * @return ReportStrategy
     * @throws InvalidArgumentException Jika tipe laporan tidak dikenali di sistem
     */
    public function resolve(string $type): ReportStrategy
    {
        if (!array_key_exists($type, $this->strategies)) {
            throw new InvalidArgumentException("Tipe laporan '{$type}' tidak terdaftar di dalam sistem.");
        }

        $strategyClass = $this->strategies[$type];

        return app($strategyClass);
    }

    /**
     * Mendapatkan daftar seluruh tipe laporan yang didukung beserta deskripsi lengkapnya.
     *
     * @return array<string, string>
     */
    public function getSupportedTypes(): array
    {
        return [
            'status'   => 'Status dan Kondisi Fisik Kendaraan',
            'opd'      => 'Distribusi Aset Per Instansi (OPD)',
            'document' => 'Masa Berlaku Dokumen/STNK',
        ];
    }
}
