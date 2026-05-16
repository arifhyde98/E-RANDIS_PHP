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
use App\Http\Requests\ImportVehicleRequest;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Services\VehicleService;

/**
 * Controller untuk Manajemen Data Kendaraan
 * 
 * Menangani CRUD data kendaraan, pencarian, serta fitur import/export Excel.
 */
class VehicleController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['search', 'searchLandingVehicle']),
            new Middleware('role:superadmin', only: ['truncate']),
        ];
    }

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
            $query->where(function($q) use ($request) {
                $q->whereHas('vehicleType', function($sq) use ($request) {
                    $sq->where('name', $request->jenis);
                })->orWhere('jenis', $request->jenis);
            });
        }

        $vehicles = $query->paginate(10)->withQueryString();
        
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $stats = $this->vehicleService->getDashboardStats();
        $opds = Opd::orderBy('nama')->get();
        $statuses = Vehicle::getStatuses();
        $conditions = Vehicle::getConditions();

        $vehicleDataMap = $vehicles->getCollection()->keyBy('id')->map(fn($v) => $v->only([
            'id', 'no_polisi', 'merk', 'tipe', 'jenis', 'opd', 'opd_id', 'pemegang', 'status', 
            'vehicle_type_id', 'tahun_pembuatan', 'warna', 'stnk_ada', 'bpkb_ada', 
            'tgl_stnk', 'tgl_perolehan', 'nilai_perolehan', 'no_mesin', 'no_rangka', 
            'keterangan', 'foto_kendaraan'
        ]));

        return view('vehicles.index', compact('vehicles', 'stats', 'vehicleTypes', 'opds', 'statuses', 'conditions', 'vehicleDataMap'));
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

        // Ambil Pengaturan Web dalam satu kali proses (Optimasi Fase 2)
        $settings = [
            'site_name' => \App\Models\Setting::get('site_name', 'PEMERINTAH DAERAH'),
            'site_logo' => \App\Models\Setting::get('site_logo'),
            'hero_title' => \App\Models\Setting::get('hero_title', 'E-RANDIS'),
            'hero_subtitle' => \App\Models\Setting::get('hero_subtitle', 'Sistem Monitoring Kendaraan Dinas Pemerintah Daerah'),
            'hero_image' => \App\Models\Setting::get('hero_image', 'images/hero-illustration.png'),
            'hero_bg_image' => \App\Models\Setting::get('hero_bg_image', 'images/hero-illustration.png'),
        ];

        return view('welcome', compact('vehicle', 'query', 'total', 'activePercentage', 'settings'));
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

        $vehicle = Vehicle::create($validated);
        
        // Invalidation terarah (Hanya dashboard stats)
        $this->vehicleService->invalidateDashboardStats(opdId: $vehicle->opd_id);

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

        $oldOpdId = $vehicle->opd_id;
        $vehicle->update($validated);
        
        // Invalidation terarah (Handle kasus pindah instansi)
        $this->vehicleService->invalidateDashboardStats(
            opdId: $vehicle->opd_id, 
            oldOpdId: $oldOpdId
        );

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
        
        $opdId = $vehicle->opd_id;
        $vehicle->delete();
        
        // Invalidation terarah
        $this->vehicleService->invalidateDashboardStats(opdId: $opdId);

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
        
        // Invalidation massal seluruh OPD (Dashboard stats)
        $this->vehicleService->invalidateDashboardStats(invalidateAllOpd: true);

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
    public function import(ImportVehicleRequest $request): RedirectResponse
    {
        try {
            Excel::import(new VehicleImport, $request->file('file'));
            
            // Invalidation massal seluruh OPD (Dashboard stats)
            $this->vehicleService->invalidateDashboardStats(invalidateAllOpd: true);

            return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('vehicles.index')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}

