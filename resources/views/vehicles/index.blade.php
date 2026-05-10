@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar <span class="text-gradient">Kendaraan</span></h2>
            <p class="text-secondary mb-0">Total {{ $vehicles->total() }} unit kendaraan terdaftar.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success px-3" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel me-2"></i> Import
            </button>
            <a href="{{ route('vehicles.export') }}" class="btn btn-outline-primary px-3">
                <i class="bi bi-download me-2"></i> Export
            </a>
            <a href="{{ route('vehicles.create') }}" class="btn btn-premium px-4">
                <i class="bi bi-plus-lg me-2"></i> Tambah Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success premium-card border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger premium-card border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="premium-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light bg-opacity-50">
                    <tr>
                        <th class="border-0 px-4 py-3">No. Polisi</th>
                        <th class="border-0 py-3">Merk / Tipe</th>
                        <th class="border-0 py-3">OPD / Dinas</th>
                        <th class="border-0 py-3">Pemegang</th>
                        <th class="border-0 py-3 text-center">Dokumen</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3 text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="badge bg-dark px-3 py-2 rounded-3 fw-bold">{{ $vehicle->no_polisi }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $vehicle->merk }}</div>
                                <div class="small text-secondary">{{ $vehicle->tipe }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $vehicle->opd }}</div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $vehicle->pemegang }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $vehicle->stnk_ada == 'Ada' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $vehicle->stnk_ada == 'Ada' ? 'success' : 'danger' }} small me-1">STNK</span>
                                <span class="badge {{ $vehicle->bpkb_ada == 'Ada' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $vehicle->bpkb_ada == 'Ada' ? 'success' : 'danger' }} small">BPKB</span>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                    {{ $vehicle->status }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-light btn-sm rounded-3 px-2">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm rounded-3 px-2">
                                            <i class="bi bi-trash3 text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($vehicles->hasPages())
            <div class="p-4 border-top">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-card border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Import Data Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vehicles.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-4 bg-primary bg-opacity-10 p-3 rounded-4 d-flex align-items-center">
                        <div class="bg-primary text-white p-2 rounded-3 me-3">
                            <i class="bi bi-download"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Belum punya formatnya?</h6>
                            <a href="{{ route('vehicles.template') }}" class="small fw-bold text-decoration-none">Download Template Excel di sini</a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Pilih File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="bg-light p-3 rounded-3 small text-secondary">
                        <i class="bi bi-info-circle me-1"></i> Sistem akan membaca data mulai dari baris ke-4 sesuai format template.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Proses Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
