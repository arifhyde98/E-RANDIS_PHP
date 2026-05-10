<?php

namespace App\Imports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class VehicleImport implements ToModel, WithStartRow
{
    /**
     * Start from row 4 as per the image provided.
     */
    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        // Skip jika No Polisi (Index 2) kosong
        if (!isset($row[2]) || empty($row[2])) {
            return null;
        }

        // Membersihkan Nomor Polisi dari spasi ganda dan spasi di ujung
        $no_polisi = preg_replace('/\s+/', ' ', trim($row[2]));
        $no_polisi = strtoupper($no_polisi); // Standarisasi Huruf Kapital

        return new Vehicle([
            'jenis'           => $row[0],
            'merk'            => $row[1],
            'tipe'            => $row[1],
            'no_polisi'       => $no_polisi,
            'no_mesin'        => $row[3],
            'no_rangka'       => $row[4],
            'tgl_perolehan'   => $this->transformDate($row[5]),
            'nilai_perolehan' => $this->transformCurrency($row[6]),
            'stnk_ada'        => $row[7] ?? 'Tidak',
            'bpkb_ada'        => $row[8] ?? 'Tidak',
            'status'          => $row[9] ?? 'BAIK',
            'pemegang'        => $row[10],
            'keterangan'      => $row[11],
            'opd'             => $row[12] ?? 'SEKRETARIAT DAERAH',
        ]);
    }

    /**
     * Helper untuk memproses format tanggal Excel/String
     */
    private function transformDate($value)
    {
        if (empty($value)) return null;
        
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            return Carbon::createFromFormat('m/d/Y', $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Helper untuk memproses format uang
     */
    private function transformCurrency($value)
    {
        if (empty($value)) return 0;
        $clean = str_replace(['.', ','], ['', '.'], $value);
        return (float) $clean;
    }
}
