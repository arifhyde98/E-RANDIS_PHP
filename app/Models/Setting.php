<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Model untuk Pengaturan Aplikasi
 * 
 * @property int $id
 * @property string $key Nama kunci pengaturan (unik)
 * @property string|null $value Nilai pengaturan
 * @property string $type Tipe data (text, image, textarea)
 * @property string $group Grup pengaturan (general, landing, login)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Mendapatkan nilai pengaturan berdasarkan key.
     * Menggunakan cache untuk performa selama 1 jam.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return cache()->remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Mendapatkan URL gambar dari path yang disimpan.
     * 
     * Menangani path lokal maupun URL eksternal secara otomatis.
     * 
     * @param string|null $path
     * @return string|null
     */
    public static function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'images/', 'uploads/'])) {
            return asset($path);
        }

        return Storage::url($path);
    }
}

