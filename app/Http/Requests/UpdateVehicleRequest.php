<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('no_polisi')) {
            $this->merge([
                'no_polisi' => strtoupper(str_replace('.', '', preg_replace('/\s+/', ' ', trim($this->no_polisi))))
            ]);
        }
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')->id;
        
        return [
            'no_polisi' => 'required|unique:vehicles,no_polisi,' . $vehicleId,
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
            'status' => 'required',
            'keterangan' => 'nullable',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
