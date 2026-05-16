<?php

namespace App\Imports;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\Opd;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;

/**
 * Class untuk Mengimport Data Kendaraan dari Excel (Optimized v2.2)
 * 
 * Menggunakan teknik Batch Inserts dan In-Memory Caching untuk performa maksimal.
 */
class VehicleImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    /** @var int Counter untuk pembuatan ID sementara kendaraan tanpa plat */
    private $rowCount = 0;

    /** @var array Cache memori untuk Master Data */
    private $typeCache = [];
    private $opdCache = [];
    private $existingPlates = [];

    public function __construct()
    {
        // Pre-load Master Data ke Memori untuk menghindari kueri N+1
        $this->typeCache = VehicleType::pluck('id', 'name')->toArray();
        $this->opdCache = Opd::pluck('id', 'nama')->toArray();
        
        // FIX (High Risk): Gunakan withoutGlobalScopes() agar bisa melihat plat secara GLOBAL.
        // Mencegah error duplicate entry jika plat sudah dipakai OPD lain.
        $this->existingPlates = Vehicle::withoutGlobalScopes()->pluck('no_polisi')->flip()->toArray();
    }

    /**
     * Menentukan baris awal dimulainya pembacaan data.
     */
    public function startRow(): int
    {
        return 4;
    }

    /**
     * Tentukan ukuran batch untuk insert sekaligus.
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Tentukan ukuran chunk untuk pembacaan file besar.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Memetakan baris Excel menjadi model Vehicle.
     */
    public function model(array $row)
    {
        // Lewati jika baris kosong
        if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
            return null;
        }

        // 1. Identifikasi Nomor Polisi (Kolom C / Index 2)
        $raw_no_polisi = $row[2] ?? null;
        $vehicleService = app(\App\Services\VehicleService::class);
        $no_polisi = $vehicleService->formatPlateNumber($raw_no_polisi);

        // ATURAN TEMPLATE: Jika plat kosong, buatkan identitas urut
        if (empty($no_polisi) || in_array($no_polisi, ['NOMOR POLISI', '-', '?'])) {
            $this->rowCount++;
            $no_polisi = "TANPA-PLAT-" . str_pad($this->rowCount, 3, '0', STR_PAD_LEFT);
        }

        // Cek duplikat menggunakan Cache Memori (Bukan Database)
        if (isset($this->existingPlates[$no_polisi])) {
            // Jika ada duplikat, tambahkan suffix unik sederhana
            $original_no_polisi = $no_polisi;
            $i = 2;
            while (isset($this->existingPlates[$no_polisi])) {
                $no_polisi = $original_no_polisi . " (" . $i++ . ")";
            }
        }

        // Tandai plat ini sebagai 'terpakai' dalam sesi ini agar baris berikutnya tidak bentrok
        $this->existingPlates[$no_polisi] = true;

        // 2. Proses Jenis Kendaraan menggunakan Cache Memori
        $jenisName = trim($row[0] ?? 'Lainnya');
        if (!isset($this->typeCache[$jenisName])) {
            $vt = VehicleType::firstOrCreate(
                ['name' => $jenisName],
                ['description' => 'Otomatis dibuat saat import Excel']
            );
            $this->typeCache[$jenisName] = $vt->id;
        }
        $typeId = $this->typeCache[$jenisName];

        // 3. Proses OPD menggunakan Cache Memori
        $user = auth()->user();
        if ($user && $user->role === \App\Enums\UserRole::OPD) {
            $opdName = $user->opd?->nama ?? 'INSTANSI TIDAK DIKENAL';
            $opdId = $user->opd_id;
        } else {
            $opdName = strtoupper(trim($row[12] ?? 'SEKRETARIAT DAERAH'));
            if (!isset($this->opdCache[$opdName])) {
                $newOpd = Opd::firstOrCreate(
                    ['nama' => $opdName],
                    ['singkatan' => null, 'alamat' => null]
                );
                $this->opdCache[$opdName] = $newOpd->id;
            }
            $opdId = $this->opdCache[$opdName];
        }

        // 4. Persiapkan Data
        $tglPerolehan = $this->transformDate($row[5] ?? null);
        $tahunPembuatan = $tglPerolehan ? \Carbon\Carbon::parse($tglPerolehan)->year : null;
        $kondisi = \App\Enums\VehicleCondition::fromImport($row[9] ?? null);

        return new Vehicle([
            'jenis'           => $jenisName,
            'vehicle_type_id' => $typeId,
            'merk'            => trim($row[1] ?? '-'),
            'tipe'            => trim($row[1] ?? '-'),
            'no_polisi'       => $no_polisi,
            'user_id'         => $user?->id,
            'no_mesin'        => $row[3] ?? null,
            'no_rangka'       => $row[4] ?? null,
            'tahun_pembuatan' => $tahunPembuatan,
            'tgl_perolehan'   => $tglPerolehan,
            'nilai_perolehan' => $this->transformCurrency($row[6] ?? 0),
            'stnk_ada'        => $row[7] ?? 'Tidak',
            'bpkb_ada'        => $row[8] ?? 'Tidak',
            'kondisi'         => $kondisi->value,
            'status'          => $kondisi->toDefaultStatus()->value,
            'pemegang'        => $row[10] ?? '-',
            'keterangan'      => $row[11] ?? null,
            'opd'             => $opdName,
            'opd_id'          => $opdId,
        ]);
    }

    /**
     * Helper untuk memproses format tanggal dari Excel (Serial) atau String.
     * 
     * @param mixed $value
     * @return \Illuminate\Support\Carbon|null
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
     * Helper untuk membersihkan dan mengonversi format mata uang ke Float.
     * 
     * Menangani simbol Rp, titik ribuan, dan koma desimal.
     * 
     * @param mixed $value
     * @return float
     */
    private function transformCurrency($value)
    {
        if (empty($value)) return 0;
        
        // Jika sudah numeric, langsung kembalikan
        if (is_numeric($value)) return (float) $value;

        // Jika string, bersihkan karakter non-angka kecuali titik/koma desimal
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

