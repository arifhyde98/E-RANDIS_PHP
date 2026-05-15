<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = ['user_id', 'description', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mencatat aktivitas ke database.
     */
    public static function log(string $description, string $type = 'info')
    {
        return self::create([
            'user_id' => auth()->id(),
            'description' => $description,
            'type' => $type,
        ]);
    }
}
