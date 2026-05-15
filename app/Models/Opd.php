<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk OPD (Organisasi Perangkat Daerah)
 * 
 * @property int $id
 * @property string $nama Nama lengkap instansi/OPD
 * @property string|null $singkatan Singkatan nama instansi
 * @property string|null $alamat Alamat kantor instansi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicle[] $vehicles
 */
class Opd extends Model
{
    protected $fillable = ['nama', 'singkatan', 'alamat'];

    /**
     * Boot model untuk menangani event Eloquent.
     */
    protected static function booted()
    {
        static::created(function ($opd) {
            // Auto-generate akun setiap kali OPD baru dibuat (Form/Import/Seeder)
            $result = app(\App\Services\AccountService::class)->createOpdAccount($opd);

            // Jika dalam konteks request web, flash password ke session 
            // agar bisa ditampilkan di UI SweetAlert
            if (request()->hasSession()) {
                session()->flash('new_account', [
                    'opd_nama' => $opd->nama,
                    'email' => $result['user']->email,
                    'password' => $result['password']
                ]);
            }
        });
    }

    /**
     * Mendapatkan daftar semua kendaraan yang dimiliki oleh OPD ini.
     * 
     * @return HasMany
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(\App\Models\Vehicle::class, 'opd_id');
    }

    /**
     * Mendapatkan data akun admin yang mengelola OPD ini.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class);
    }
}

