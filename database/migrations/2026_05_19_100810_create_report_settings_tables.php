<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel KOP Surat Laporan
        Schema::create('report_letterheads', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemerintah');
            $table->string('nama_instansi');
            $table->string('nama_unit')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 2. Tabel Pejabat Penanda Tangan
        Schema::create('report_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('nip')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('kota_ttd');
            $table->string('signature_image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 3. Tabel Aturan Ekspor per Jenis Laporan
        Schema::create('report_export_settings', function (Blueprint $table) {
            $table->id();
            $table->string('report_type')->unique();
            
            $table->foreignId('letterhead_id')
                ->nullable()
                ->constrained('report_letterheads')
                ->nullOnDelete();

            $table->foreignId('signatory_id')
                ->nullable()
                ->constrained('report_signatories')
                ->nullOnDelete();

            $table->string('paper_size')->default('A4');
            $table->string('orientation')->default('L'); // L: Landscape, P: Portrait
            $table->boolean('show_summary')->default(true);
            $table->boolean('show_signature')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_export_settings');
        Schema::dropIfExists('report_signatories');
        Schema::dropIfExists('report_letterheads');
    }
};
