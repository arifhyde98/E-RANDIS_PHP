<?php

namespace App\Enums;

/**
 * Enum untuk Status Operasional Kendaraan.
 * 
 * Mengatur ketersediaan kendaraan untuk dipinjam atau digunakan.
 */
enum VehicleStatus: string
{
    case TERSEDIA = 'Tersedia';
    case DIPINJAM = 'Dipinjam';
    case NONAKTIF = 'Nonaktif';

    /**
     * Mendapatkan label tampilan status.
     * 
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::TERSEDIA => 'Tersedia',
            self::DIPINJAM => 'Dipinjam',
            self::NONAKTIF => 'Nonaktif',
        };
    }

    /**
     * Mendapatkan class CSS warna untuk badge status.
     * 
     * @return string
     */
    public function colorClass(): string
    {
        return match ($this) {
            self::TERSEDIA => 'bg-success',
            self::DIPINJAM => 'bg-info',
            self::NONAKTIF => 'bg-secondary',
        };
    }

    /**
     * Mendapatkan semua label status untuk dropdown.
     * 
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $item) {
            $carry[$item->value] = $item->label();
            return $carry;
        }, []);
    }
}
