<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleType;
use App\Models\Opd;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Exports\VehicleExport;
use App\Exports\VehicleTemplateExport;
use App\Imports\VehicleImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Services\VehicleService;

class VehicleController extends Controller
{
    protected $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
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
            $status = $request->status;
            if ($status === 'Rusak') {
                $query->whereIn('status', ['Rusak', 'Rusak Berat', 'Rusak Ringan', 'Maintenance', 'maintenance', 'rusak']);
            } elseif ($status === 'Tersedia') {
                $query->whereIn('status', ['Tersedia', 'Aktif', 'aktif']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('jenis')) {
            $query->whereHas('vehicleType', function($q) use ($request) {
                $q->where('name', $request->jenis);
            })->orWhere('jenis', $request->jenis);
        }

        $vehicles = $query->paginate(10)->withQueryString();
        
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $stats = $this->vehicleService->getDashboardStats();
        $opds = Opd::orderBy('nama')->get();
        $statuses = Vehicle::getStatuses();

        return view('vehicles.index', compact('vehicles', 'stats', 'vehicleTypes', 'opds', 'statuses'));
    }

    /**
     * Search function for Landing Page (Public)
     */
    public function search(Request $request): View
    {
        $query = $request->input('q');
        $vehicle = $this->vehicleService->findForLanding($query);

        // Stats for Landing Page Hero
        $stats = $this->vehicleService->getDashboardStats();
        $total = $stats['total'];
        $activeCount = $stats['available'];
        $activePercentage = $total > 0 ? round(($activeCount / $total) * 100) : 0;

        return view('welcome', compact('vehicle', 'query', 'total', 'activePercentage'));
    }

    public function searchLandingVehicle(Request $request): JsonResponse
    {
        $query = $request->input('q');
        $vehicle = $this->vehicleService->findForLanding($query);

        return response()->json([
            'found' => (bool) $vehicle,
            'query' => $query,
            'vehicle' => $vehicle ? [
                'no_polisi' => $vehicle->no_polisi,
                'nama' => trim($vehicle->merk.' '.$vehicle->tipe),
                'opd' => $vehicle->opd,
                'pemegang' => $vehicle->pemegang,
                'status' => \App\Models\Vehicle::getStatuses()[$vehicle->status] ?? $vehicle->status,
            ] : null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::all();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $statuses = Vehicle::getStatuses();
        $opds = Opd::orderBy('nama')->get();
        return view('vehicles.create', compact('users', 'vehicleTypes', 'statuses', 'opds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Clean plate number using Service
        $validated['no_polisi'] = $this->vehicleService->formatPlateNumber($validated['no_polisi']);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['user', 'vehicleType']);
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle): View
    {
        $users = User::all();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $statuses = Vehicle::getStatuses();
        $opds = Opd::orderBy('nama')->get();
        return view('vehicles.edit', compact('vehicle', 'users', 'vehicleTypes', 'statuses', 'opds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validated();

        // Clean plate number using Service
        $validated['no_polisi'] = $this->vehicleService->formatPlateNumber($validated['no_polisi']);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    /**
     * Remove all resources from storage.
     */
    public function truncate(): RedirectResponse
    {
        Vehicle::truncate();
        return redirect()->route('vehicles.index')->with('success', 'Seluruh data kendaraan berhasil dikosongkan.');
    }

    /**
     * Export data to Excel
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new VehicleExport, 'data_kendaraan_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Download Excel Template
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new VehicleTemplateExport, 'template_import_kendaraan.xlsx');
    }

    /**
     * Import data from Excel
     */
    public function import(Request $request): RedirectResponse
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
