@extends('layouts.app')

@section('title', 'Data OPD')

@section('content')
<div class="container-fluid px-0">
    
    <!-- PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master-data.index') }}" class="text-decoration-none text-secondary">Master Data</a></li>
                    <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">Data OPD</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-navy mb-0">Manajemen Data OPD / Instansi</h3>
        </div>
        <div class="action-toolbar">
            <button type="button" class="btn btn-primary shadow-sm fw-medium d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addOpdModal">
                <i class="bi bi-plus-lg"></i> Tambah OPD
            </button>
        </div>
    </div>



    <!-- MAIN TABLE SECTION -->
    <div class="admin-card overflow-hidden">
        
        <!-- FILTER & SEARCH SECTION -->
        <div class="bg-light p-3 border-bottom border-light">
            <form action="{{ route('opds.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 bg-white shadow-none" placeholder="Cari nama atau singkatan OPD...">
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-medium">Cari</button>
                    <a href="{{ route('opds.index') }}" class="btn btn-light border btn-sm bg-white" title="Reset"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-white text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 px-4 border-bottom-0 fw-semibold" style="width: 50px;">No</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Nama Instansi / OPD</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Singkatan</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Alamat</th>
                        <th class="py-3 px-4 border-bottom-0 fw-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0 bg-white">
                    @forelse($opds as $index => $opd)
                        <tr>
                            <td class="px-4 py-3 text-secondary">
                                {{ $opds->firstItem() + $index }}
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-navy">{{ $opd->nama }}</div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-primary border border-primary border-opacity-25 px-2 py-1">{{ $opd->singkatan ?? '-' }}</span>
                            </td>
                            <td class="py-3 text-secondary small">
                                {{ Str::limit($opd->alamat ?? '-', 50) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-light border shadow-none text-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editOpdModal"
                                            data-id="{{ $opd->id }}"
                                            data-nama="{{ $opd->nama }}"
                                            data-singkatan="{{ $opd->singkatan }}"
                                            data-alamat="{{ $opd->alamat }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('opds.destroy', $opd) }}" method="POST" class="d-inline delete-confirm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border shadow-none text-danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-building text-secondary opacity-25" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold text-navy mt-3">Belum ada data OPD</h6>
                                    <p class="text-secondary small">Silakan tambahkan data OPD baru atau jalankan seeder.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION -->
        @if($opds->hasPages())
            <div class="p-3 border-top bg-white d-flex justify-content-center">
                {{ $opds->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>

@push('modals')
<!-- ADD MODAL -->
<div class="modal fade" id="addOpdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4">
                <h5 class="modal-title fw-bold text-navy">Tambah OPD Baru</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('opds.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Nama Instansi / OPD</label>
                        <input type="text" name="nama" class="form-control shadow-none" placeholder="Masukkan nama lengkap OPD" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Singkatan</label>
                        <input type="text" name="singkatan" class="form-control shadow-none" placeholder="Contoh: BAPPEDA">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Alamat</label>
                        <textarea name="alamat" class="form-control shadow-none" rows="3" placeholder="Masukkan alamat lengkap kantor"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 border-top">
                    <button type="button" class="btn btn-light border fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editOpdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom px-4">
                <h5 class="modal-title fw-bold text-navy">Edit Data OPD</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editOpdForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Nama Instansi / OPD</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control shadow-none" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Singkatan</label>
                        <input type="text" name="singkatan" id="edit_singkatan" class="form-control shadow-none">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control shadow-none" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 border-top">
                    <button type="button" class="btn btn-light border fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editOpdModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const singkatan = button.getAttribute('data-singkatan');
                const alamat = button.getAttribute('data-alamat');

                const form = document.getElementById('editOpdForm');
                // Use a safe way to construct the URL
                const baseUrl = "{{ route('opds.index') }}";
                form.action = `${baseUrl}/${id}`;

                document.getElementById('edit_nama').value = nama || '';
                document.getElementById('edit_singkatan').value = (singkatan && singkatan !== '-') ? singkatan : '';
                document.getElementById('edit_alamat').value = (alamat && alamat !== '-') ? alamat : '';
            });
        }
    });
</script>
@endpush
@endsection
