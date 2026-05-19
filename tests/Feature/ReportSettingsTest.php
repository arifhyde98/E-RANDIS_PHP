<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ReportLetterhead;
use App\Models\ReportSignatory;
use App\Models\ReportExportSetting;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Suite Pengujian Fitur Manajemen Pengaturan Laporan Dinamis (QA)
 */
class ReportSettingsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test 1: Pengguna non-superadmin (guest/OPD) tidak diizinkan mengakses halaman pengaturan laporan.
     */
    public function test_non_superadmin_cannot_access_report_settings()
    {
        // Tamu (Guest)
        $this->get(route('reports.settings.index'))
            ->assertRedirect('/login');

        // Pengguna OPD biasa
        $opdUser = User::factory()->create(['role' => UserRole::OPD]);
        $this->actingAs($opdUser)
            ->get(route('reports.settings.index'))
            ->assertRedirect(route('home'));
    }

    /**
     * Test 2: Superadmin dapat melihat dashboard pengaturan laporan.
     */
    public function test_superadmin_can_access_report_settings_dashboard()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        $response = $this->actingAs($superadmin)
            ->get(route('reports.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Dokumen Cetak Laporan');
        $response->assertSee('KOP Surat Resmi');
        $response->assertSee('Pejabat Penanda Tangan');
    }

    /**
     * Test 3: Superadmin dapat memperbarui data Kop Surat Instansi.
     */
    public function test_superadmin_can_update_letterhead()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        $response = $this->actingAs($superadmin)
            ->post(route('reports.settings.letterhead'), [
                'nama_pemerintah' => 'PEMERINTAH PROVINSI SULAWESI BARAT',
                'nama_instansi'   => 'DISHUB SULBAR',
                'nama_unit'       => 'UPTD Wilayah II',
                'alamat'          => 'Jl. Trans Sulawesi No. 12',
                'telepon'         => '0451-9999',
                'email'           => 'dishub@sulbar.go.id',
                'website'         => 'dishub.sulbar.go.id',
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('report_letterheads', [
            'nama_pemerintah' => 'PEMERINTAH PROVINSI SULAWESI BARAT',
            'nama_instansi'   => 'DISHUB SULBAR',
            'email'           => 'dishub@sulbar.go.id',
        ]);
    }

    /**
     * Test 4: Superadmin dapat memperbarui pejabat penanda tangan.
     */
    public function test_superadmin_can_update_signatory()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        $response = $this->actingAs($superadmin)
            ->post(route('reports.settings.signatory'), [
                'nama'             => 'Ir. H. BUDI UTOMO, M.T',
                'jabatan'          => 'Kepala Dinas Perhubungan',
                'nip'              => '19820512 200812 1 005',
                'pangkat_golongan' => 'Pembina Utama Muda, IV/c',
                'kota_ttd'         => 'Mamuju',
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('report_signatories', [
            'nama'     => 'Ir. H. BUDI UTOMO, M.T',
            'jabatan'  => 'Kepala Dinas Perhubungan',
            'nip'      => '19820512 200812 1 005',
            'kota_ttd' => 'Mamuju',
        ]);
    }

    /**
     * Test 5: Superadmin dapat mengatur format per jenis ekspor laporan.
     */
    public function test_superadmin_can_update_export_settings()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        // Pastikan Kop & Pejabat sudah ada
        $letterhead = ReportLetterhead::create([
            'nama_pemerintah' => 'PEMPROV',
            'nama_instansi'   => 'BAPENDA',
            'alamat'          => 'Jl. Cik',
            'is_active'       => true,
            'is_default'      => true,
        ]);

        $signatory = ReportSignatory::create([
            'nama'      => 'Pejabat A',
            'jabatan'   => 'Plt',
            'kota_ttd'  => 'Palu',
            'is_active' => true,
            'is_default'=> true,
        ]);

        $response = $this->actingAs($superadmin)
            ->post(route('reports.settings.export'), [
                'report_type'    => 'status',
                'paper_size'     => 'F4',
                'orientation'    => 'P',
                'show_summary'   => 1,
                'show_signature' => 1,
            ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('report_export_settings', [
            'report_type'    => 'status',
            'paper_size'     => 'F4',
            'orientation'    => 'P',
            'show_summary'   => true,
            'show_signature' => true,
        ]);
    }
}
