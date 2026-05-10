<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Vehicle::all();
    }

    public function headings(): array
    {
        return [
            'Jenis Kendaraan',
            'Merk/Tipe',
            'Nomor Polisi',
            'Nomor Mesin',
            'Nomor Rangka',
            'Tgl Perolehan',
            'Nilai Perolehan',
            'STNK',
            'BPKB',
            'Kondisi',
            'Pemegang',
            'Keterangan',
            'OPD'
        ];
    }

    /**
    * @var Vehicle $vehicle
    */
    public function map($vehicle): array
    {
        return [
            $vehicle->jenis,
            $vehicle->merk . ' ' . $vehicle->tipe,
            $vehicle->no_polisi,
            $vehicle->no_mesin,
            $vehicle->no_rangka,
            $vehicle->tgl_perolehan,
            $vehicle->nilai_perolehan,
            $vehicle->stnk_ada,
            $vehicle->bpkb_ada,
            $vehicle->status,
            $vehicle->pemegang,
            $vehicle->keterangan,
            $vehicle->opd,
        ];
    }
}
