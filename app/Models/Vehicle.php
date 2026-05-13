<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\VehicleStatus;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = [
        'no_polisi',
        'merk',
        'tipe',
        'jenis',
        'vehicle_type_id',
        'tahun_pembuatan',
        'tgl_perolehan',
        'nilai_perolehan',
        'stnk_ada',
        'bpkb_ada',
        'no_rangka',
        'no_mesin',
        'warna',
        'tgl_stnk',
        'opd',
        'opd_id',
        'pemegang',
        'status',
        'keterangan',
        'user_id',
    ];

    /**
     * Get the user that owns the vehicle.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Get the OPD (Organisasi Perangkat Daerah) that owns the vehicle.
     */
    public function opdRelation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Opd::class, 'opd_id');
    }

    public static function getStatuses(): array
    {
        return VehicleStatus::labels();
    }
}
