<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opd extends Model
{
    protected $fillable = ['nama', 'singkatan', 'alamat'];

    /**
     * Get all vehicles belonging to this OPD.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(\App\Models\Vehicle::class, 'opd_id');
    }
}
