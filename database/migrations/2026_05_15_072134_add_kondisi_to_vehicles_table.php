<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Menjalankan migrasi untuk menambah kolom kondisi.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('kondisi')->default('Baik')->after('status');
            $table->index('kondisi');
        });
    }

    /**
     * Membatalkan migrasi (menghapus kolom kondisi).
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['kondisi']);
            $table->dropColumn('kondisi');
        });
    }
};
