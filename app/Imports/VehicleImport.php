<?php

namespace App\Imports;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class VehicleImport implements ToModel, WithStartRow
{
    private $rowCount = 0;

    /**
     * Start from row 2 (assuming row 1 is the header).
     */
    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        // Skip jika baris kosong (Jenis, Merk, dan Plat tidak ada isinya)
        if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
            return null;
        }

        // 1. Identifikasi Nomor Polisi (Kolom C / Index 2)
        $raw_no_polisi = $row[2] ?? null;
        
        // Bersihkan Nomor Polisi (Hilangkan titik dan spasi ganda)
        $no_polisi = $raw_no_polisi ? str_replace('.', '', preg_replace('/\s+/', ' ', trim($raw_no_polisi))) : null;
        $no_polisi = $no_polisi ? strtoupper($no_polisi) : null;

        // ATURAN TEMPLATE: Jika plat kosong/strip/tanda tanya, buatkan identitas urut
        if (empty($no_polisi) || $no_polisi == 'NOMOR POLISI' || $no_polisi == '-' || $no_polisi == '?') {
            $this->rowCount++;
            $no_polisi = "TANPA-PLAT-" . str_pad($this->rowCount, 3, '0', STR_PAD_LEFT);
        }

        // Cek duplikat di database
        $existing = Vehicle::where('no_polisi', $no_polisi)->first();
        if ($existing) {
            // Jika plat sama, cek apakah nomor mesin atau nomor rangka beda
            $isSameEngine = ($existing->no_mesin == ($row[3] ?? null));
            $isSameChassis = ($existing->no_rangka == ($row[4] ?? null));

            if ($isSameEngine && $isSameChassis) {
                // Jika mesin dan rangka juga sama, berarti benar-benar duplikat identik, lewati saja.
                return null;
            } else {
                // Jika mesin atau rangka beda, berarti kendaraan beda dengan plat sama.
                // Tambahkan suffix agar tetap bisa masuk (karena database mewajibkan unik)
                $original_no_polisi = $no_polisi;
                $i = 2;
                while (Vehicle::where('no_polisi', $no_polisi)->exists()) {
                    $no_polisi = $original_no_polisi . " (" . $i++ . ")";
                }
            }
        }

        // 2. Proses Jenis Kendaraan (Kolom A / Index 0)
        $jenisName = trim($row[0] ?? 'Lainnya');
        $vehicleType = VehicleType::firstOrCreate(
            ['name' => $jenisName],
            ['description' => 'Otomatis dibuat saat import Excel']
        );

        // 3. Proses OPD / Instansi (Kolom M / Index 12)
        $opdName = strtoupper(trim($row[12] ?? 'SEKRETARIAT DAERAH'));
        $opd = \App\Models\Opd::firstOrCreate(
            ['nama' => $opdName],
            ['singkatan' => null, 'alamat' => null]
        );

        // 4. Persiapkan Data untuk Insert/Update
        $tglPerolehan = $this->transformDate($row[5] ?? null);
        $tahunPembuatan = $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->year : null;

        $data = [
            'jenis'           => $jenisName,
            'vehicle_type_id' => $vehicleType->id,
            'merk'            => trim($row[1] ?? '-'),
            'tipe'            => trim($row[1] ?? '-'),
            'no_polisi'       => $no_polisi,
            'no_mesin'        => $row[3] ?? null,
            'no_rangka'       => $row[4] ?? null,
            'tahun_pembuatan' => $tahunPembuatan,
            'tgl_perolehan'   => $tglPerolehan,
            'nilai_perolehan' => $this->transformCurrency($row[6] ?? 0),
            'stnk_ada'        => $row[7] ?? 'Tidak',
            'bpkb_ada'        => $row[8] ?? 'Tidak',
            'status'          => (isset($row[9]) && (strtoupper($row[9]) == 'BAIK' || strtoupper($row[9]) == 'RUSAK RINGAN')) ? 'Tersedia' : ($row[9] ?? 'Tersedia'),
            'pemegang'        => $row[10] ?? '-',
            'keterangan'      => $row[11] ?? null,
            'opd'             => $opdName,
            'opd_id'          => $opd->id,
        ];

        return new Vehicle($data);
    }

    /**
     * Helper untuk memproses format tanggal Excel/String
     */
    private function transformDate($value)
    {
        if (empty($value)) return null;
        
        try {
            // Jika input adalah angka
            if (is_numeric($value)) {
                // Jika angka tersebut terlihat seperti Tahun (misal: 1990 - 2030)
                if ($value > 1900 && $value < 2100) {
                    return Carbon::createFromDate($value, 1, 1)->startOfDay();
                }
                // Jika angka serial Excel asli (biasanya > 30000 untuk tahun 1980 ke atas)
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }

            // Jika input adalah string tanggal
            if (str_contains($value, '/')) {
                // Coba beberapa format umum
                try {
                    return Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
                } catch (\Exception $e) {
                    return Carbon::createFromFormat('m/d/Y', $value)->startOfDay();
                }
            }

            return Carbon::parse($value)->startOfDay();
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
        
        // Jika sudah numeric, langsung kembalikan
        if (is_numeric($value)) return (float) $value;

        // Jika string, bersihkan karakter non-angka kecuali titik/koma desimal
        // Menghapus Rp, spasi, dan titik ribuan
        $clean = preg_replace('/[^0-9,.]/', '', $value);
        
        // Standarisasi format: jika ada koma desimal (format Indo), ubah ke titik
        if (str_contains($clean, ',') && str_contains($clean, '.')) {
            // Kasus 1.234.567,89 -> hapus titik, ubah koma ke titik
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (str_contains($clean, ',')) {
            // Kasus 1234567,89 -> ubah koma ke titik
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }
}
