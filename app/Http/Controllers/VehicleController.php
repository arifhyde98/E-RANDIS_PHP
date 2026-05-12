<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleType;
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
    public function index(Request $request)
    {
        $query = Vehicle::with(['user', 'vehicleType'])->latest();

        if ($request->filled('q')) {
            $search = strtoupper(preg_replace('/\s+/', ' ', trim($request->q)));
            $query->where(function($q) use ($search) {
                $q->where('no_polisi', 'LIKE', "%{$search}%")
                  ->orWhere('pemegang', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%")
                  ->orWhere('opd', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->whereHas('vehicleType', function($q) use ($request) {
                $q->where('name', $request->jenis);
            })->orWhere('jenis', $request->jenis);
        }

        $vehicles = $query->paginate(10)->withQueryString();
        
        // Dynamic Stats
        $stats = [
            'total' => Vehicle::count(),
            'available' => Vehicle::whereIn('status', ['Tersedia', 'Aktif', 'aktif'])->count(),
            'damaged' => Vehicle::whereIn('status', ['Rusak', 'Maintenance', 'maintenance'])->count(),
            'borrowed' => Vehicle::whereIn('status', ['Dipinjam', 'dipinjam'])->count(),
        ];

        $vehicleTypes = VehicleType::orderBy('name')->get();
        
        return view('vehicles.index', compact('vehicles', 'stats', 'vehicleTypes'));
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

        // Stats for Landing Page Hero
        $total = Vehicle::count();
        $activeCount = Vehicle::whereIn('status', ['Tersedia', 'Aktif', 'aktif'])->count();
        $activePercentage = $total > 0 ? round(($activeCount / $total) * 100) : 0;

        return view('welcome', compact('vehicle', 'query', 'total', 'activePercentage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $statuses = Vehicle::getStatuses();
        return view('vehicles.create', compact('users', 'vehicleTypes', 'statuses'));
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
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $statuses = Vehicle::getStatuses();
        return view('vehicles.edit', compact('vehicle', 'users', 'vehicleTypes', 'statuses'));
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
     * Remove all resources from storage.
     */
    public function truncate()
    {
        Vehicle::truncate();
        return redirect()->route('vehicles.index')->with('success', 'Seluruh data kendaraan berhasil dikosongkan.');
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
