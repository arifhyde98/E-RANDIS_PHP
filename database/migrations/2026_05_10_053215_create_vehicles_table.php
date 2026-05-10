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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('no_polisi')->unique();
            $table->string('merk');
            $table->string('tipe');
            $table->enum('jenis', ['Mobil', 'Motor', 'Bus', 'Truck'])->default('Mobil');
            $table->integer('tahun_pembuatan')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('warna')->nullable();
            $table->date('tgl_stnk')->nullable();
            $table->string('opd'); // Dinas / Instansi
            $table->string('pemegang'); // Nama Pemegang
            $table->enum('status', ['Tersedia', 'Digunakan', 'Rusak', 'Dilelang'])->default('Tersedia');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Relasi ke tabel users (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
