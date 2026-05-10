<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Memberikan 1 baris contoh data
        return [
            [
                'KENDARAAN DINAS RODA 4 (EMPAT)',
                'TOYOTA INNOVA ZENIX',
                'DN 1234 XY',
                '1TR-1234567',
                'MHFX-987654321',
                '12/31/2023',
                '450000000',
                'Ada',
                'Ada',
                'BAIK',
                'BUDI SANTOSO',
                'Kendaraan Operasional',
                'BAPENDA SULTENG'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            [
                'TEMPLATE IMPORT DATA KENDARAAN E-RANDIS PHP'
            ],
            [
                '' // Spasi Baris 2
            ],
            [
                'Jenis Kendaraan',
                'Merk/Tipe',
                'Nomor Polisi',
                'Nomor Mesin',
                'Nomor Rangka',
                'Tanggal Perolehan (m/d/Y)',
                'Nilai Perolehan',
                'STNK (Ada/Tidak)',
                'BPKB (Ada/Tidak)',
                'Kondisi',
                'Penggunaan',
                'Keterangan',
                'OPD / DINAS'
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk Judul di Baris 1
            1    => ['font' => ['bold' => true, 'size' => 14]],
            // Style untuk Header di Baris 3
            3    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]],
        ];
    }
}
