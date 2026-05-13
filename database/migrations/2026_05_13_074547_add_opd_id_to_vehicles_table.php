<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add opd_id column (nullable, no FK constraint yet)
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('opd_id')->nullable()->after('opd');
        });

        // 2. Auto-fill opd_id by matching existing opd string to opds.nama
        DB::statement('
            UPDATE vehicles v
            JOIN opds o ON TRIM(LOWER(v.opd)) = TRIM(LOWER(o.nama))
            SET v.opd_id = o.id
        ');

        // 3. Now add the foreign key constraint
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreign('opd_id')
                  ->references('id')
                  ->on('opds')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['opd_id']);
            $table->dropColumn('opd_id');
        });
    }
};
