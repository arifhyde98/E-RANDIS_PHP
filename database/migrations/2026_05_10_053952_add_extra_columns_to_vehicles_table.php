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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('tgl_perolehan')->nullable()->after('tahun_pembuatan');
            $table->decimal('nilai_perolehan', 15, 2)->nullable()->after('tgl_perolehan');
            $table->string('stnk_ada')->default('Tidak')->after('nilai_perolehan'); // Ada / Tidak
            $table->string('bpkb_ada')->default('Tidak')->after('stnk_ada'); // Ada / Tidak
            
            // Mengubah jenis agar lebih fleksibel mengikuti Excel
            $table->string('jenis')->change(); 
            // Mengubah status agar lebih fleksibel mengikuti Excel (BAIK, RUSAK RINGAN, dll)
            $table->string('status')->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['tgl_perolehan', 'nilai_perolehan', 'stnk_ada', 'bpkb_ada']);
        });
    }
};
