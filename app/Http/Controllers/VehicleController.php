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
use Illuminate\Support\Facades\Storage;

use App\Services\VehicleService;

/**
 * Controller untuk Manajemen Data Kendaraan
 * 
 * Menangani CRUD data kendaraan, pencarian, serta fitur import/export Excel.
 */
class VehicleController extends Controller
{
    protected $vehicleService;

    /**
     * Konstruktor Controller.
     * 
     * @param VehicleService $vehicleService
     */
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    /**
     * Menampilkan daftar kendaraan dengan fitur filter dan pencarian.
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Vehicle::with(['user', 'vehicleType'])->latest();

        // Filter Pencarian Global
        if ($request->filled('q')) {
            $search = strtoupper(preg_replace('/\s+/', ' ', trim($request->q)));
            $query->where(function($q) use ($search) {
                $q->where('no_polisi', 'LIKE', "%{$search}%")
                  ->orWhere('pemegang', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%")
                  ->orWhere('opd', 'LIKE', "%{$search}%");
            });
        }

        // Filter Berdasarkan Status Operasional
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Berdasarkan Kondisi Fisik
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Filter Berdasarkan Jenis Kendaraan
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
        $conditions = Vehicle::getConditions();

        return view('vehicles.index', compact('vehicles', 'stats', 'vehicleTypes', 'opds', 'statuses', 'conditions'));
    }

    /**
     * Fungsi pencarian untuk Landing Page (Akses Publik).
     * 
     * @param Request $request
     * @return View
     */
    public function search(Request $request): View
    {
        $query = $request->input('q');
        $vehicle = $this->vehicleService->findForLanding($query);

        // Statistik untuk Hero Landing Page
        $stats = $this->vehicleService->getDashboardStats();
        $total = $stats['total'];
        $activeCount = $stats['available'];
        $activePercentage = $total > 0 ? round(($activeCount / $total) * 100) : 0;

        return view('welcome', compact('vehicle', 'query', 'total', 'activePercentage'));
    }

    /**
     * Endpoint API pencarian kendaraan untuk dipanggil via AJAX di landing page.
     * 
     * @param Request $request
     * @return JsonResponse
     */
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
                'kondisi' => \App\Enums\VehicleCondition::tryFrom($vehicle->kondisi)?->label() ?? $vehicle->kondisi,
                'status' => \App\Enums\VehicleStatus::tryFrom($vehicle->status)?->label() ?? $vehicle->status,
                'foto_kendaraan' => $vehicle->foto_kendaraan,
            ] : null,
        ]);
    }

    /**
     * Menampilkan form untuk menambah kendaraan baru.
     * 
     * @return View
     */
    public function create(): View
    {
        $users = User::all();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $statuses = Vehicle::getStatuses();
        $conditions = Vehicle::getConditions();
        $opds = Opd::orderBy('nama')->get();
        return view('vehicles.create', compact('users', 'vehicleTypes', 'statuses', 'conditions', 'opds'));
    }

    /**
     * Menyimpan data kendaraan baru ke database.
     * 
     * @param StoreVehicleRequest $request
     * @return RedirectResponse
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Format nomor polisi menggunakan Service
        $validated['no_polisi'] = $this->vehicleService->formatPlateNumber($validated['no_polisi']);

        // Handle Foto Kendaraan
        if ($request->hasFile('foto_kendaraan')) {
            $paths = [];
            foreach ($request->file('foto_kendaraan') as $image) {
                $paths[] = $image->store('vehicles', 'public');
            }
            $validated['foto_kendaraan'] = $paths;
        }

        Vehicle::create($validated);
        
        // Bersihkan Cache spesifik user
        $user = auth()->user();
        $cacheKey = 'dashboard.stats.' . ($user->role->value ?? 'guest') . '.' . ($user->opd_id ?? 'global');
        cache()->forget($cacheKey);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail data satu kendaraan.
     * 
     * @param Vehicle $vehicle
     * @return View
     */
    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['user', 'vehicleType']);
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Menampilkan form untuk mengedit data kendaraan.
     * 
     * @param Vehicle $vehicle
     * @return View
     */
    public function edit(Vehicle $vehicle): View
    {
        $users = User::all();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $statuses = Vehicle::getStatuses();
        $conditions = Vehicle::getConditions();
        $opds = Opd::orderBy('nama')->get();
        return view('vehicles.edit', compact('vehicle', 'users', 'vehicleTypes', 'statuses', 'conditions', 'opds'));
    }

    /**
     * Memperbarui data kendaraan di database.
     * 
     * @param UpdateVehicleRequest $request
     * @param Vehicle $vehicle
     * @return RedirectResponse
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validated();

        // Format nomor polisi menggunakan Service
        $validated['no_polisi'] = $this->vehicleService->formatPlateNumber($validated['no_polisi']);

        // Handle Foto Kendaraan (Replace All)
        if ($request->hasFile('foto_kendaraan')) {
            // Hapus foto lama
            if ($vehicle->foto_kendaraan) {
                foreach ($vehicle->foto_kendaraan as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan foto baru
            $paths = [];
            foreach ($request->file('foto_kendaraan') as $image) {
                $paths[] = $image->store('vehicles', 'public');
            }
            $validated['foto_kendaraan'] = $paths;
        }

        $vehicle->update($validated);
        
        // Bersihkan Cache spesifik user
        $user = auth()->user();
        $cacheKey = 'dashboard.stats.' . ($user->role->value ?? 'guest') . '.' . ($user->opd_id ?? 'global');
        cache()->forget($cacheKey);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Menghapus data kendaraan dari database.
     * 
     * @param Vehicle $vehicle
     * @return RedirectResponse
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        // Hapus foto fisik
        if ($vehicle->foto_kendaraan) {
            foreach ($vehicle->foto_kendaraan as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        
        $vehicle->delete();
        
        // Bersihkan Cache spesifik user
        $user = auth()->user();
        $cacheKey = 'dashboard.stats.' . ($user->role->value ?? 'guest') . '.' . ($user->opd_id ?? 'global');
        cache()->forget($cacheKey);

        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    /**
     * Mengosongkan seluruh data di tabel kendaraan.
     * 
     * @return RedirectResponse
     */
    public function truncate(): RedirectResponse
    {
        // Hapus seluruh folder foto kendaraan
        Storage::disk('public')->deleteDirectory('vehicles');
        
        Vehicle::truncate();
        
        // Bersihkan Cache spesifik user
        $user = auth()->user();
        $cacheKey = 'dashboard.stats.' . ($user->role->value ?? 'guest') . '.' . ($user->opd_id ?? 'global');
        cache()->forget($cacheKey);

        return redirect()->route('vehicles.index')->with('success', 'Seluruh data kendaraan berhasil dikosongkan.');
    }

    /**
     * Mengekspor seluruh data kendaraan ke file Excel.
     * 
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new VehicleExport, 'data_kendaraan_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Mengunduh file template Excel untuk import data.
     * 
     * @return BinaryFileResponse
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new VehicleTemplateExport, 'template_import_kendaraan.xlsx');
    }

    /**
     * Mengimport data kendaraan dari file Excel.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new VehicleImport, $request->file('file'));
            
            // Bersihkan Cache spesifik user
            $user = auth()->user();
            $cacheKey = 'dashboard.stats.' . ($user->role->value ?? 'guest') . '.' . ($user->opd_id ?? 'global');
            cache()->forget($cacheKey);

            return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('vehicles.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}

