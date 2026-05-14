@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('content')
<div class="container-fluid px-0">
    
    <!-- PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">Data Kendaraan</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-navy mb-0">Manajemen Data Kendaraan</h3>
        </div>
        <div class="action-toolbar d-flex flex-wrap gap-2">
            <form action="{{ route('vehicles.truncate') }}" method="POST" class="delete-confirm">
                @csrf
                <button type="submit" class="btn btn-outline-danger shadow-sm fw-medium d-flex align-items-center gap-2">
                    <i class="bi bi-trash3"></i> <span class="d-none d-sm-inline">Kosongkan</span>
                </button>
            </form>
            <button type="button" class="btn btn-action btn-action-success shadow-sm fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <span class="btn-action-icon"><i class="bi bi-file-earmark-arrow-up"></i></span>
                <span class="d-none d-sm-inline">Import</span>
            </button>
            <a href="{{ route('vehicles.export') }}" class="btn btn-action btn-action-primary shadow-sm fw-semibold d-flex align-items-center gap-2" data-export-button>
                <span class="btn-action-icon"><i class="bi bi-download"></i></span>
                <span class="d-none d-sm-inline" data-export-label>Export</span>
            </a>
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary shadow-sm fw-medium d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Kendaraan
            </a>
        </div>
    </div>



    <!-- OPTIONAL SIDEBAR SUMMARY (Displayed as top cards on smaller screens) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="admin-card p-3 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle lh-1"><i class="bi bi-car-front fs-5"></i></div>
                <div>
                    <h5 class="fw-bold text-navy mb-0">{{ $stats['total'] }}</h5>
                    <small class="text-secondary">Total Kendaraan</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="admin-card p-3 d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle lh-1"><i class="bi bi-check-circle fs-5"></i></div>
                <div>
                    <h5 class="fw-bold text-navy mb-0">{{ $stats['available'] }}</h5>
                    <small class="text-secondary">Kendaraan Aktif</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="admin-card p-3 d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle lh-1"><i class="bi bi-tools fs-5"></i></div>
                <div>
                    <h5 class="fw-bold text-navy mb-0">{{ $stats['damaged'] }}</h5>
                    <small class="text-secondary">Maintenance</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="admin-card p-3 d-flex align-items-center gap-3">
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle lh-1"><i class="bi bi-key fs-5"></i></div>
                <div>
                    <h5 class="fw-bold text-navy mb-0">{{ $stats['borrowed'] }}</h5>
                    <small class="text-secondary">Dipinjam</small>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE SECTION -->
    <div class="admin-card overflow-hidden">
        
        <!-- FILTER & SEARCH SECTION -->
        <div class="bg-light p-3 border-bottom border-light">
            <form action="{{ route('vehicles.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 bg-white shadow-none" placeholder="Cari nomor polisi atau nama pengguna...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm shadow-none" name="status">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Aktif / Tersedia</option>
                        <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm shadow-none" name="jenis">
                        <option value="">Semua Jenis</option>
                        @foreach($vehicleTypes as $type)
                            <option value="{{ $type->name }}" {{ request('jenis') == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-medium">Terapkan</button>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-light border btn-sm bg-white" title="Reset Filter"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 px-4 border-bottom-0 fw-semibold">No. Polisi</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Nama Kendaraan</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Jenis / Tahun</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Pengguna</th>
                        <th class="py-3 border-bottom-0 fw-semibold text-center">Status</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Terakhir Aktif</th>
                        <th class="py-3 px-4 border-bottom-0 fw-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 bg-white">
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="badge bg-light text-dark border border-secondary border-opacity-25 px-3 py-2 fs-6 rounded-3 fw-bold">{{ $vehicle->no_polisi }}</span>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-navy">{{ $vehicle->merk }}</div>
                                <div class="small text-secondary">{{ $vehicle->tipe }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-dark fw-medium">{{ $vehicle->vehicleType->name ?? ($vehicle->jenis ?? 'Mobil Dinas') }}</div>
                                <div class="small text-secondary">
                                    {{ $vehicle->tahun_pembuatan ?? ($vehicle->tgl_perolehan ? \Carbon\Carbon::parse($vehicle->tgl_perolehan)->year : '-') }}
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="fw-medium text-dark"><i class="bi bi-person-fill text-secondary me-1"></i> {{ $vehicle->pemegang }}</div>
                                <div class="small text-secondary">{{ $vehicle->opd }}</div>
                            </td>
                            <td class="text-center">
                                <x-status-badge :status="$vehicle->status" />
                            </td>
                            <td class="py-3 text-secondary small">
                                {{ $vehicle->updated_at ? $vehicle->updated_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-light border shadow-none text-secondary" data-bs-toggle="tooltip" title="Lihat Detail Lengkap">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-light border shadow-none text-primary" data-bs-toggle="tooltip" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline delete-confirm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border shadow-none text-danger" data-bs-toggle="tooltip" title="Hapus Data">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- EMPTY STATE -->
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-car-front text-secondary opacity-50" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-navy mb-1">Belum ada data kendaraan</h5>
                                    <p class="text-secondary mb-4">Sistem saat ini belum memiliki data operasional armada apapun.</p>
                                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary px-4 fw-medium">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Kendaraan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION -->
        @if($vehicles->hasPages())
            <div class="p-3 border-top bg-white d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="small text-secondary">
                    Menampilkan <strong>{{ $vehicles->firstItem() }}</strong> hingga <strong>{{ $vehicles->lastItem() }}</strong> dari <strong>{{ $vehicles->total() }}</strong> entri data
                </div>
                <div>
                    {{ $vehicles->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            @if($vehicles->count() > 0)
                <div class="p-3 border-top bg-white small text-secondary text-center text-md-start">
                    Menampilkan seluruh data (Total: {{ $vehicles->count() }} entri)
                </div>
            @endif
        @endif
    </div>

</div>

@push('modals')
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-navy" id="importModalLabel">Import Data Kendaraan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vehicles.import') }}" method="POST" enctype="multipart/form-data" data-import-form>
                @csrf
                <div class="modal-body p-4">
                    <div class="import-helper d-flex border rounded-3 p-3 mb-4">
                        <div class="me-3">
                            <i class="bi bi-info-circle text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Persiapkan Data Anda</h6>
                            <p class="small text-secondary mb-2">Pastikan data Excel (.xlsx) mengikuti format standar sistem agar proses import berjalan lancar.</p>
                            <a href="{{ route('vehicles.template') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" data-template-button>
                                <i class="bi bi-download"></i> Download Template
                            </a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Pilih File Excel</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required data-import-file>
                        <div class="small text-secondary mt-2" data-import-file-name>Belum ada file dipilih.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-light border fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-medium px-4" data-import-submit>
                        <i class="bi bi-cloud-arrow-up me-1"></i> Proses Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        const exportButton = document.querySelector('[data-export-button]');
        if (exportButton) {
            exportButton.addEventListener('click', function () {
                const label = exportButton.querySelector('[data-export-label]');
                exportButton.classList.add('is-loading');
                if (label) label.textContent = 'Menyiapkan...';

                setTimeout(function () {
                    exportButton.classList.remove('is-loading');
                    if (label) label.textContent = 'Export';
                }, 2500);
            });
        }

        const templateButton = document.querySelector('[data-template-button]');
        if (templateButton) {
            templateButton.addEventListener('click', function () {
                templateButton.classList.add('active');
                setTimeout(function () {
                    templateButton.classList.remove('active');
                }, 1600);
            });
        }

        const importFile = document.querySelector('[data-import-file]');
        const importFileName = document.querySelector('[data-import-file-name]');
        if (importFile && importFileName) {
            importFile.addEventListener('change', function () {
                importFileName.textContent = importFile.files.length
                    ? importFile.files[0].name
                    : 'Belum ada file dipilih.';
            });
        }

        const importForm = document.querySelector('[data-import-form]');
        const importSubmit = document.querySelector('[data-import-submit]');
        if (importForm && importSubmit) {
            importForm.addEventListener('submit', function () {
                importSubmit.disabled = true;
                importSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Mengimport...';
            });
        }
    });
</script>
@endsection
