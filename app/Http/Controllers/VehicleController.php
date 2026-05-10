<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\VehicleExport;
use App\Exports\VehicleTemplateExport;
use App\Imports\VehicleImport;
use Maatwebsite\Excel\Facades\Excel;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::with('user')->latest()->paginate(10);
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Search function for Landing Page (Public)
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $vehicle = null;

        if ($query) {
            // Membersihkan input pencarian agar sama dengan format di database
            $cleanQuery = preg_replace('/\s+/', ' ', trim($query));
            $cleanQuery = strtoupper($cleanQuery);

            $vehicle = Vehicle::where('no_polisi', 'LIKE', "%{$cleanQuery}%")
                ->orWhere('pemegang', 'LIKE', "%{$cleanQuery}%")
                ->first();
        }

        return view('welcome', compact('vehicle', 'query'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('vehicles.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_polisi' => 'required|unique:vehicles,no_polisi',
            'merk' => 'required',
            'tipe' => 'required',
            'jenis' => 'required',
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
        ]);

        // Clean plate number before store from manual form
        $validated['no_polisi'] = strtoupper(preg_replace('/\s+/', ' ', trim($validated['no_polisi'])));

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $users = User::all();
        return view('vehicles.edit', compact('vehicle', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'no_polisi' => 'required|unique:vehicles,no_polisi,' . $vehicle->id,
            'merk' => 'required',
            'tipe' => 'required',
            'jenis' => 'required',
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
        ]);

        // Clean plate number before update from manual form
        $validated['no_polisi'] = strtoupper(preg_replace('/\s+/', ' ', trim($validated['no_polisi'])));

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    /**
     * Export data to Excel
     */
    public function export() 
    {
        return Excel::download(new VehicleExport, 'data_kendaraan_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download Excel Template
     */
    public function downloadTemplate()
    {
        return Excel::download(new VehicleTemplateExport, 'template_import_kendaraan.xlsx');
    }

    /**
     * Import data from Excel
     */
    public function import(Request $request) 
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new VehicleImport, $request->file('file'));
            return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('vehicles.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}
