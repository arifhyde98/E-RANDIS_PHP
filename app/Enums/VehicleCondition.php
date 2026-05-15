<?php

namespace App\Enums;

/**
 * Enum untuk Kondisi Fisik Kendaraan.
 * 
 * Digunakan untuk mencatat kondisi nyata fisik kendaraan di lapangan.
 */
enum VehicleCondition: string
{
    case BAIK = 'Baik';
    case RUSAK_RINGAN = 'Rusak Ringan';
    case RUSAK_BERAT = 'Rusak Berat';
    case HILANG = 'Hilang';
    case DALAM_PENELUSURAN = 'Dalam Penelusuran';

    /**
     * Mendapatkan label tampilan untuk kondisi kendaraan.
     * 
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::BAIK => 'Baik / Layak',
            self::RUSAK_RINGAN => 'Rusak Ringan',
            self::RUSAK_BERAT => 'Rusak Berat',
            self::HILANG => 'Hilang / Tidak Ditemukan',
            self::DALAM_PENELUSURAN => 'Dalam Penelusuran',
        };
    }

    /**
     * Memetakan kondisi fisik ke status operasional default.
     * 
     * @return VehicleStatus
     */
    public function toDefaultStatus(): VehicleStatus
    {
        return match ($this) {
            self::BAIK, self::RUSAK_RINGAN => VehicleStatus::TERSEDIA,
            self::RUSAK_BERAT, self::HILANG, self::DALAM_PENELUSURAN => VehicleStatus::NONAKTIF,
        };
    }

    /**
     * Menormalisasi string singkatan dari import Excel menjadi enum.
     * 
     * @param string|null $value Nilai mentah dari kolom Excel
     * @return self
     */
    public static function fromImport(?string $value): self
    {
        if (empty($value)) return self::BAIK;

        $cleaned = strtoupper(trim($value));

        return match (true) {
            in_array($cleaned, ['B', 'BAIK']) => self::BAIK,
            in_array($cleaned, ['RR', 'RUSAK RINGAN']) => self::RUSAK_RINGAN,
            in_array($cleaned, ['RB', 'RUSAK BERAT']) => self::RUSAK_BERAT,
            in_array($cleaned, ['HILANG', 'H']) => self::HILANG,
            in_array($cleaned, ['TD', 'TIDAK DIKETAHUI', 'DALAM PENELUSURAN']) => self::DALAM_PENELUSURAN,
            default => self::BAIK,
        };
    }

    /**
     * Mendapatkan semua label kondisi untuk pilihan dropdown.
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
