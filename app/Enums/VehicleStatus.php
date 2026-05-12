<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case TERSEDIA = 'Tersedia';
    case DIPINJAM = 'Dipinjam';
    case RUSAK = 'Rusak';
    case NONAKTIF = 'Nonaktif';

    public function label(): string
    {
        return match($this) {
            self::TERSEDIA => 'Aktif / Tersedia',
            self::DIPINJAM => 'Sedang Dipinjam',
            self::RUSAK => 'Maintenance / Rusak',
            self::NONAKTIF => 'Nonaktif / Dilelang',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::TERSEDIA => 'bg-success',
            self::DIPINJAM => 'bg-warning',
            self::RUSAK => 'bg-danger',
            self::NONAKTIF => 'bg-secondary',
        };
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $item) {
            $carry[$item->value] = $item->label();
            return $carry;
        }, []);
    }
}
