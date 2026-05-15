<?php

namespace App\Observers;

use App\Models\Opd;
use App\Models\Activity;
use App\Services\AccountService;

/**
 * Observer untuk Model Opd.
 * 
 * Mengelola otomatisasi pembuatan akun admin saat OPD baru didaftarkan.
 */
class OpdObserver
{
    /**
     * Menangani event "created" OPD.
     * 
     * @param Opd $opd
     * @return void
     */
    public function created(Opd $opd): void
    {
        // Auto-generate akun setiap kali OPD baru dibuat (Form/Import/Seeder)
        $result = app(AccountService::class)->createOpdAccount($opd);

        // Jika dalam konteks request web, flash password ke session 
        // agar bisa ditampilkan di UI SweetAlert
        if (request()->hasSession()) {
            session()->flash('new_account', [
                'opd_nama' => $opd->nama,
                'email' => $result['user']->email,
                'password' => $result['password']
            ]);
        }

        Activity::log("Menambahkan Master Data OPD: {$opd->nama}", 'success');
    }

    /**
     * Menangani event "deleted" OPD.
     * 
     * @param Opd $opd
     * @return void
     */
    public function deleted(Opd $opd): void
    {
        Activity::log("Menghapus Master Data OPD: {$opd->nama}", 'danger');
    }
}
