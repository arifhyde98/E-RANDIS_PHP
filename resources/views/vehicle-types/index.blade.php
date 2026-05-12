@extends('layouts.app')

@section('title', 'Jenis Kendaraan')

@section('content')
<div class="container-fluid px-0">
    
    <!-- PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-navy mb-1">Daftar Jenis Kendaraan</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master-data.index') }}" class="text-decoration-none text-secondary">Master Data</a></li>
                    <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">Jenis Kendaraan</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('vehicle-types.cleanup') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA jenis yang tidak memiliki kendaraan?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger rounded-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-eraser"></i> Bersihkan Jenis Kosong
                </button>
            </form>
            <a href="{{ route('vehicle-types.create') }}" class="btn btn-primary rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Jenis
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- DATA TABLE -->
    <div class="admin-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="py-3 px-4 border-bottom-0 fw-semibold" style="width: 50px;">No</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Nama Jenis</th>
                        <th class="py-3 border-bottom-0 fw-semibold">Deskripsi</th>
                        <th class="py-3 border-bottom-0 fw-semibold text-center">Total Kendaraan</th>
                        <th class="py-3 px-4 border-bottom-0 fw-semibold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($types as $type)
                        <tr>
                            <td class="px-4 py-3 text-secondary">{{ $loop->iteration }}</td>
                            <td class="py-3">
                                <div class="fw-bold text-navy">{{ $type->name }}</div>
                            </td>
                            <td class="py-3 text-secondary small">
                                {{ $type->description ?: '-' }}
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-light text-navy border px-3 py-2 rounded-pill fw-medium">
                                    {{ $type->vehicles_count ?? $type->vehicles()->count() }} Unit
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('vehicle-types.edit', $type) }}" class="btn btn-sm btn-light border text-primary rounded-3">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($type->vehicles_count == 0)
                                        <form action="{{ route('vehicle-types.destroy', $type) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-3" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-light border text-muted rounded-3" disabled title="Tidak bisa dihapus karena masih ada unit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-grid fs-1 d-block mb-2 text-light"></i>
                                Belum ada data jenis kendaraan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
