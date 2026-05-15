<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\VehicleStatus;

/**
 * Model untuk Kendaraan
 * 
 * @property int $id
 * @property string $no_polisi Nomor Polisi Kendaraan (Unik)
 * @property string $merk Merk Kendaraan
 * @property string $tipe Tipe/Model Kendaraan
 * @property string $jenis Jenis Kendaraan (Mobil, Motor, dll)
 * @property int|null $vehicle_type_id ID relasi ke tipe kendaraan
 * @property int|null $tahun_pembuatan Tahun pembuatan kendaraan
 * @property string|null $tgl_perolehan Tanggal perolehan kendaraan
 * @property float|null $nilai_perolehan Nilai/Harga perolehan
 * @property string $stnk_ada Status keberadaan STNK
 * @property string $bpkb_ada Status keberadaan BPKB
 * @property string|null $no_rangka Nomor rangka kendaraan
 * @property string|null $no_mesin Nomor mesin kendaraan
 * @property string|null $warna Warna kendaraan
 * @property string|null $tgl_stnk Tanggal masa berlaku STNK
 * @property string $opd Nama OPD (denormalisasi)
 * @property int|null $opd_id ID relasi ke tabel OPD
 * @property string $pemegang Nama pemegang/pengguna kendaraan
 * @property string $status Status operasional (Tersedia, Rusak, dll)
 * @property string|null $keterangan Catatan tambahan
 * @property int|null $user_id ID user pengelola
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\VehicleType|null $vehicleType
 * @property-read \App\Models\Opd|null $opdRelation
 */
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
     * Mendapatkan user yang memiliki/mengelola kendaraan ini.
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan tipe kendaraan.
     * 
     * @return BelongsTo
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * Mendapatkan data OPD (Organisasi Perangkat Daerah) pemilik kendaraan.
     * 
     * @return BelongsTo
     */
    public function opdRelation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Opd::class, 'opd_id');
    }

    /**
     * Mendapatkan daftar label status kendaraan dari Enum.
     * 
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return VehicleStatus::labels();
    }
}

