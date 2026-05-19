<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Pejabat Penanda Tangan Laporan (ReportSignatory)
 * 
 * Merepresentasikan data pejabat yang berwenang menandatangani cetakan laporan resmi.
 */
class ReportSignatory extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'report_signatories';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama',
        'jabatan',
        'nip',
        'pangkat_golongan',
        'kota_ttd',
        'signature_image_path',
        'is_active',
        'is_default',
    ];

    /**
     * Cast tipe data kolom secara dinamis.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Relasi ke data Pengaturan Ekspor Laporan.
     *
     * @return HasMany
     */
    public function exportSettings(): HasMany
    {
        return $this->hasMany(ReportExportSetting::class, 'signatory_id');
    }
}
