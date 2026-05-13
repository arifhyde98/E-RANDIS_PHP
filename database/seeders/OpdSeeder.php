<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get unique OPD names from vehicles table
        $uniqueOpds = \DB::table('vehicles')
            ->whereNotNull('opd')
            ->distinct()
            ->pluck('opd');

        foreach ($uniqueOpds as $opdName) {
            // Check if it already exists to prevent duplicate errors
            \App\Models\Opd::firstOrCreate([
                'nama' => $opdName
            ]);
        }
    }
}
