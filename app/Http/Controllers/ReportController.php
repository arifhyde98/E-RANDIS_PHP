<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Enums\UserRole;
use App\Reports\ReportRegistry;
use App\Services\ReportService;
use App\Http\Requests\ReportFilterRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Exports\DynamicQueryReportExport;
use App\Exports\DynamicCollectionReportExport;
use App\Reports\Contracts\PostProcessesReportRows;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller untuk Manajemen Laporan & Ekspor (Modul Laporan Modular)
 * 
 * Mengelola antarmuka dashboard laporan, penarikan pratinjau AJAX HTML parsial,
 * ekspor berkas Excel dinamis, dan pencetakan dokumen ramah printer.
 */
class ReportController extends Controller implements HasMiddleware
{
    /**
     * Layanan pemrosesan bisnis laporan.
     */
    protected ReportService $reportService;

    /**
     * Registry untuk tipe strategi laporan.
     */
    protected ReportRegistry $registry;

    /**
     * Injeksi dependensi ReportService dan ReportRegistry.
     */
    public function __construct(ReportService $reportService, ReportRegistry $registry)
    {
        $this->reportService = $reportService;
        $this->registry = $registry;
    }

    /**
     * Pendaftaran middleware Laravel 12 terstandarisasi untuk proteksi akses.
     * 
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Menampilkan dashboard utama Modul Laporan lengkap dengan ringkasan metrik.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isOpd = $user->role->value === 'opd';

        // 1. Dapatkan OPD ID (Kunci bagi OPD, Null untuk global bagi Admin/Superadmin)
        $opdId = $isOpd ? $user->opd_id : null;

        // 2. Tarik ringkasan statistik (Tunggal & Ter-cache)
        $summary = $this->reportService->getQuickSummary($opdId);

        // 3. Persiapkan pilihan OPD khusus untuk Admin / Superadmin
        $opds = !$isOpd ? Opd::orderBy('nama')->get() : collect();

        // 4. Dapatkan daftar tipe laporan yang didukung oleh sistem
        $reportTypes = $this->registry->getSupportedTypes();

        return view('reports.index', compact('summary', 'opds', 'reportTypes', 'isOpd'));
    }

    /**
     * Menampilkan pratinjau (preview) laporan secara dinamis (mengembalikan parsial HTML via AJAX).
     *
     * @param \App\Http\Requests\ReportFilterRequest $request
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function preview(ReportFilterRequest $request)
    {
        // Jalankan logika penarikan data terpaginasi
        $previewData = $this->reportService->generatePreview($request->validated());

        // Jika request menginginkan Ajax/Parsial HTML
        if ($request->ajax() || $request->wantsJson()) {
            return view('reports.partials.preview-table', $previewData);
        }

        // Fallback jika diakses langsung (seperti non-AJAX)
        return view('reports.partials.preview-table', $previewData);
    }

    /**
     * Mengekspor laporan dinamis ke format Excel (.xlsx).
     *
     * @param \App\Http\Requests\ReportFilterRequest $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $type = $filters['type'] ?? 'status';

        // 1. Selesaikan strategi laporan via registry
        $strategy = $this->registry->resolve($type);

        // 2. Susun nama berkas unduhan yang bersih
        $filename = 'laporan_' . $type . '_' . date('Ymd_His') . '.xlsx';

        // 3. Jika strategi mengimplementasikan pengayaan data (PostProcessesReportRows), gunakan ekspor berbasis Koleksi
        if ($strategy instanceof PostProcessesReportRows) {
            $data = $strategy->query($filters)->get();
            $strategy->postProcess($data, $data);

            return Excel::download(
                new DynamicCollectionReportExport($data, $strategy->headers()),
                $filename
            );
        }

        // 4. Jika strategi standar, gunakan kueri streaming (FromQuery) hemat memori untuk data besar
        return Excel::download(
            new DynamicQueryReportExport($strategy->query($filters), $strategy->headers()),
            $filename
        );
    }

    /**
     * Membuka halaman pratinjau bersih khusus cetak printer / ekspor PDF ramah browser.
     *
     * @param \App\Http\Requests\ReportFilterRequest $request
     * @return \Illuminate\View\View
     */
    public function print(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $type = $filters['type'] ?? 'status';

        // 1. Selesaikan strategi
        $strategy = $this->registry->resolve($type);

        // 2. Tarik data (tanpa paginasi untuk cetak, agar seluruh data keluar di kertas)
        $data = $strategy->query($filters)->get();

        // 3. Jalankan pengayaan data jika strategi mengimplementasikan PostProcessesReportRows
        if ($strategy instanceof PostProcessesReportRows) {
            $strategy->postProcess($data, $data);
        }

        // 4. Deskripsi tipe laporan
        $reportTitle = $this->registry->getSupportedTypes()[$type] ?? 'Laporan Kendaraan';

        return view('reports.print', [
            'data'        => $data,
            'headers'     => $strategy->headers(),
            'reportTitle' => $reportTitle,
            'filters'     => $filters,
        ]);
    }
}
