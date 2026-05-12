@extends('layouts.app')

@section('title', 'Edit Jenis Kendaraan')

@section('content')
<div class="container-fluid px-0">
    
    <!-- PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-navy mb-1">Edit Jenis Kendaraan</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master-data.index') }}" class="text-decoration-none text-secondary">Master Data</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicle-types.index') }}" class="text-decoration-none text-secondary">Jenis Kendaraan</a></li>
                    <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('vehicle-types.index') }}" class="btn btn-light border rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="row">
        <div class="col-lg-6">
            <div class="admin-card p-4">
                <form action="{{ route('vehicle-types.update', $vehicleType) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-navy">Nama Jenis Kendaraan</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $vehicleType->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold text-navy">Deskripsi (Opsional)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $vehicleType->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold">
                            <i class="bi bi-save me-1"></i> Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
