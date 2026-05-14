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
    <x-table-card 
        :empty="$opds->isEmpty()" 
        emptyText="Belum ada data OPD" 
        emptyIcon="bi-building">
        
        <x-slot:filters>
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
        </x-slot:filters>

        <x-slot:thead>
            <tr>
                <th class="py-3 px-4 border-bottom-0 fw-semibold" style="width: 50px;">No</th>
                <th class="py-3 border-bottom-0 fw-semibold">Nama Instansi / OPD</th>
                <th class="py-3 border-bottom-0 fw-semibold">Singkatan</th>
                <th class="py-3 border-bottom-0 fw-semibold d-none d-md-table-cell">Alamat</th>
                <th class="py-3 px-4 border-bottom-0 fw-semibold text-center">Aksi</th>
            </tr>
        </x-slot:thead>

        @foreach($opds as $index => $opd)
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
                <td class="py-3 text-secondary small d-none d-md-table-cell">
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
        @endforeach

        <x-slot:pagination>
            @if($opds->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $opds->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </x-slot:pagination>
    </x-table-card>

</div>

@push('modals')
    <!-- ADD MODAL -->
    <x-modal id="addOpdModal" title="Tambah OPD / Instansi Baru" size="md" submitLabel="Simpan Data" form="addOpdForm">
        <form id="addOpdForm" action="{{ route('opds.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Nama Instansi / OPD</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap OPD" value="{{ old('nama') }}" required>
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Singkatan</label>
                <input type="text" name="singkatan" class="form-control" placeholder="Contoh: BAPPEDA" value="{{ old('singkatan') }}">
            </div>
            <div class="mb-0">
                <label class="form-label fw-semibold text-dark small">Alamat Kantor</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Jalur Dua No. 1...">{{ old('alamat') }}</textarea>
            </div>
        </form>
    </x-modal>

    <!-- EDIT MODAL -->
    <x-modal id="editOpdModal" title="Edit Data OPD" size="md" submitLabel="Simpan Perubahan" form="editOpdForm">
        <form id="editOpdForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Nama Instansi / OPD</label>
                <input type="text" name="nama" id="edit_nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-dark small">Singkatan</label>
                <input type="text" name="singkatan" id="edit_singkatan" class="form-control">
            </div>
            <div class="mb-0">
                <label class="form-label fw-semibold text-dark small">Alamat Kantor</label>
                <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
            </div>
        </form>
    </x-modal>
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
                const routeTemplate = "{{ route('opds.update', ':id') }}";
                form.action = routeTemplate.replace(':id', id);

                document.getElementById('edit_nama').value = nama || '';
                document.getElementById('edit_singkatan').value = (singkatan && singkatan !== '-') ? singkatan : '';
                document.getElementById('edit_alamat').value = (alamat && alamat !== '-') ? alamat : '';
            });
        }
    });
</script>
@endpush
@endsection
