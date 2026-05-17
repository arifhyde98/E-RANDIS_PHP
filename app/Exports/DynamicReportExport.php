<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Mesin Ekspor Laporan Dinamis (Modular Excel Exporter)
 * 
 * Mengekspor data kueri dari strategi laporan apa pun secara dinamis,
 * menata header kolom, menyelaraskan tipe data, dan menerapkan gaya visual Navy khas E-RANDIS.
 */
class DynamicReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * Objek Kueri Pembangun Eloquent.
     */
    protected $query;

    /**
     * Pemetaan header kolom laporan.
     * 
     * @var array<string, string>
     */
    protected array $headers;

    /**
     * Inisiasi ekspor dinamis dengan kueri ter-eager load dan pemetaan header.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<string, string> $headers
     */
    public function __construct($query, array $headers)
    {
        $this->query = $query;
        $this->headers = $headers;
    }

    /**
     * Menyediakan kueri basis data untuk dialirkan langsung ke file Excel (menghindari memory limit).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Mendapatkan daftar label header untuk baris pertama di Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return array_values($this->headers);
    }

    /**
     * Memetakan baris data Eloquent ke struktur kolom Excel secara dinamis.
     * 
     * Melakukan pembersihan data kotor, konversi nilai uang ke format angka desimal,
     * serta normalisasi format tanggal agar terbaca rapi.
     *
     * @param mixed $row Baris data model Eloquent
     * @return array
     */
    public function map($row): array
    {
        $mapped = [];

        foreach (array_keys($this->headers) as $key) {
            $value = $row->{$key};

            // Normalisasi dinamis tipe data khusus Excel
            if ($key === 'nilai_perolehan') {
                // Konversi ke numerik desimal agar bisa di-agregasi (SUM/AVERAGE) oleh pengguna di Excel
                $mapped[] = $value ? (float) $value : 0;
            } elseif ($key === 'tgl_stnk' || $key === 'tgl_perolehan') {
                // Format tanggal standar Indonesia
                $mapped[] = $value ? \Carbon\Carbon::parse($value)->format('d-m-Y') : '-';
            } else {
                $mapped[] = $value ?? '-';
            }
        }

        return $mapped;
    }

    /**
     * Menerapkan gaya estetika premium pada berkas Excel.
     * 
     * Mengatur baris header pertama dengan warna latar belakang Navy (#1E40AF)
     * dan warna teks putih tebal.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}
