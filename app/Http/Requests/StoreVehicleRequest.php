<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk Validasi Penambahan Kendaraan Baru
 */
class StoreVehicleRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan untuk membuat request ini.
     * 
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Menyiapkan data untuk divalidasi (Pre-processing).
     * 
     * Melakukan pembersihan format nomor polisi sebelum masuk ke aturan validasi.
     */
    protected function prepareForValidation()
    {
        if ($this->has('no_polisi')) {
            $this->merge([
                'no_polisi' => strtoupper(str_replace('.', '', preg_replace('/\s+/', ' ', trim($this->no_polisi))))
            ]);
        }
    }

    /**
     * Mendapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'no_polisi' => 'required|unique:vehicles,no_polisi',
            'merk' => 'required',
            'tipe' => 'required',
            'jenis' => 'required',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'tahun_pembuatan' => 'nullable|integer',
            'tgl_perolehan' => 'nullable|date',
            'nilai_perolehan' => 'nullable|numeric',
            'stnk_ada' => 'required',
            'bpkb_ada' => 'required',
            'no_rangka' => 'nullable',
            'no_mesin' => 'nullable',
            'warna' => 'nullable',
            'tgl_stnk' => 'nullable|date',
            'opd' => 'required',
            'pemegang' => 'required',
            'status' => 'required|in:Tersedia,Dipinjam,Nonaktif',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat,Hilang,Dalam Penelusuran',
            'keterangan' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}

