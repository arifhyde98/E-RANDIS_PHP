<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;

/**
 * Observer untuk Model User.
 * 
 * Mengelola pembersihan file fisik (avatar) dan log aktivitas akun.
 */
class UserObserver
{
    /**
     * Menangani event "created" User.
     * 
     * @param User $user
     * @return void
     */
    public function created(User $user): void
    {
        // Mendapatkan value role karena menggunakan Enum
        $roleName = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;
        Activity::log("Membuat akun baru: {$user->email} ({$roleName})", 'info');
    }

    /**
     * Menangani event "deleting" User.
     * 
     * @param User $user
     * @return void
     */
    public function deleting(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        Activity::log("Menghapus akun pengguna: {$user->email}", 'danger');
    }
}
