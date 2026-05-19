<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Pengaturan Kop Surat (ReportLetterhead)
 * 
 * Merepresentasikan data Kop Surat Laporan resmi yang dikelola secara dinamis.
 */
class ReportLetterhead extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'report_letterheads';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_pemerintah',
        'nama_instansi',
        'nama_unit',
        'alamat',
        'telepon',
        'email',
        'website',
        'logo_path',
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
        return $this->hasMany(ReportExportSetting::class, 'letterhead_id');
    }
}
