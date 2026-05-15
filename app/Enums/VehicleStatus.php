<?php

namespace App\Enums;

/**
 * Enum untuk Status Kendaraan
 * 
 * Merepresentasikan berbagai status operasional yang dapat dimiliki oleh kendaraan.
 */
enum VehicleStatus: string
{
    case TERSEDIA = 'Tersedia';
    case DIPINJAM = 'Dipinjam';
    case RUSAK = 'Rusak';
    case NONAKTIF = 'Nonaktif';

    /**
     * Mendapatkan label yang mudah dibaca untuk status tersebut.
     * 
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::TERSEDIA => 'Aktif / Tersedia',
            self::DIPINJAM => 'Sedang Dipinjam',
            self::RUSAK => 'Maintenance / Rusak',
            self::NONAKTIF => 'Nonaktif / Dilelang',
        };
    }

    /**
     * Mendapatkan class warna latar belakang Bootstrap untuk status tersebut.
     * 
     * @return string
     */
    public function colorClass(): string
    {
        return match($this) {
            self::TERSEDIA => 'bg-success',
            self::DIPINJAM => 'bg-warning',
            self::RUSAK => 'bg-danger',
            self::NONAKTIF => 'bg-secondary',
        };
    }

    /**
     * Mendapatkan semua label status sebagai array asosiatif.
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

