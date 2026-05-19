<?php

namespace Database\Seeders;

use App\Models\ReportLetterhead;
use App\Models\ReportSignatory;
use App\Models\ReportExportSetting;
use Illuminate\Database\Seeder;

/**
 * Seeder Pengaturan Dokumen Laporan (ReportSettingSeeder)
 * 
 * Mengisi data awal resmi Kop Surat Bapenda Sulawesi Tengah,
 * Pejabat Penanda Tangan, dan konfigurasi jenis ekspor laporan.
 */
class ReportSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Awal Kop Surat Bapenda Sulteng
        $letterhead = ReportLetterhead::updateOrCreate(
            ['email' => 'bapenda@sultengprov.go.id'],
            [
                'nama_pemerintah' => 'PEMERINTAH PROVINSI SULAWESI TENGAH',
                'nama_instansi'   => 'BADAN PENDAPATAN DAERAH (BAPENDA)',
                'nama_unit'       => 'Upt. Pengelolaan Pendapatan Wilayah I Palu',
                'alamat'          => 'Jalan Cik Ditiro No. 23, Kota Palu, Sulawesi Tengah',
                'telepon'         => '(0451) 421234',
                'website'         => 'bapenda.sultengprov.go.id',
                'logo_path'       => 'images/logo-sulteng.png',
                'is_active'       => true,
                'is_default'      => true,
            ]
        );

        // 2. Data Awal Pejabat Penanda Tangan
        $signatory = ReportSignatory::updateOrCreate(
            ['nip' => '19780512 200212 1 002'],
            [
                'nama'                 => 'Drs. H. ARIF HYDE, M.Si',
                'jabatan'              => 'Plt. Kepala Badan Pendapatan Daerah',
                'pangkat_golongan'     => 'Pembina Utama Muda, IV/c',
                'kota_ttd'             => 'Palu',
                'signature_image_path' => null, // Tanda tangan basah
                'is_active'            => true,
                'is_default'           => true,
            ]
        );

        // 3. Konfigurasi awal semua tipe laporan yang tersedia.
        foreach (['status', 'opd', 'document', 'duplicate'] as $reportType) {
            ReportExportSetting::updateOrCreate(
                ['report_type' => $reportType],
                [
                    'letterhead_id'  => $letterhead->id,
                    'signatory_id'   => $signatory->id,
                    'paper_size'     => 'A4',
                    'orientation'    => 'L',
                    'show_summary'   => true,
                    'show_signature' => true,
                ]
            );
        }
    }
}
