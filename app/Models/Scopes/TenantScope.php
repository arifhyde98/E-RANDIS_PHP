<?php

namespace App\Models\Scopes;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Scope untuk membatasi akses data berdasarkan OPD pengguna yang login.
 */
class TenantScope implements Scope
{
    /**
     * Terapkan scope ke kueri Eloquent builder yang diberikan.
     * 
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Jika role adalah OPD, batasi kueri hanya untuk opd_id milik user tersebut
            if ($user->role === UserRole::OPD && $user->opd_id) {
                $builder->where('opd_id', $user->opd_id);
            }
        }
    }
}
