<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Mobil', 'description' => 'Kendaraan roda empat atau lebih untuk penumpang.'],
            ['name' => 'Motor', 'description' => 'Kendaraan roda dua atau tiga.'],
            ['name' => 'Bus', 'description' => 'Kendaraan angkutan massal.'],
            ['name' => 'Truck', 'description' => 'Kendaraan angkutan barang / logistik.'],
        ];

        foreach ($types as $type) {
            \App\Models\VehicleType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
