<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Pengaturan Jenis Ekspor Laporan (ReportExportSetting)
 * 
 * Menentukan konfigurasi spesifik (Kop, Tanda tangan, Ukuran/Orientasi kertas) per tipe laporan.
 */
class ReportExportSetting extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'report_export_settings';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'report_type',
        'letterhead_id',
        'signatory_id',
        'paper_size',
        'orientation',
        'show_summary',
        'show_signature',
    ];

    /**
     * Cast tipe data kolom secara dinamis.
     */
    protected $casts = [
        'show_summary' => 'boolean',
        'show_signature' => 'boolean',
    ];

    /**
     * Relasi ke data Kop Surat yang ditugaskan.
     *
     * @return BelongsTo
     */
    public function letterhead(): BelongsTo
    {
        return $this->belongsTo(ReportLetterhead::class, 'letterhead_id');
    }

    /**
     * Relasi ke data Pejabat Penanda Tangan yang ditugaskan.
     *
     * @return BelongsTo
     */
    public function signatory(): BelongsTo
    {
        return $this->belongsTo(ReportSignatory::class, 'signatory_id');
    }
}
