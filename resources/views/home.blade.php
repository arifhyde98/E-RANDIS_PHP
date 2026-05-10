@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="premium-card p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Dashboard <span class="text-gradient">E-RANDIS</span></h2>
                    <p class="text-secondary mb-0">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Berikut adalah ringkasan sistem hari ini.</p>
                </div>
                <div class="d-none d-lg-block">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">ADMIN PORTAL</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Stats Cards --}}
        <div class="col-md-4">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary">
                        <i class="bi bi-car-front fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Total Kendaraan</h5>
                </div>
                <h2 class="fw-bold mb-1">{{ \App\Models\Vehicle::count() }}</h2>
                <p class="text-secondary small mb-0 fw-medium">Unit terdaftar di sistem</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Kendaraan Tersedia</h5>
                </div>
                <h2 class="fw-bold mb-1">{{ \App\Models\Vehicle::where('status', 'Tersedia')->count() }}</h2>
                <p class="text-success small mb-0 fw-medium">Siap untuk digunakan</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Perlu Perhatian</h5>
                </div>
                <h2 class="fw-bold mb-1">{{ \App\Models\Vehicle::where('status', 'Rusak')->count() }}</h2>
                <p class="text-danger small mb-0 fw-medium">Status rusak / butuh servis</p>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="premium-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Data Kendaraan Terbaru</h4>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-bold text-decoration-none">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3 rounded-start">Kendaraan</th>
                                <th class="border-0 py-3">OPD / Dinas</th>
                                <th class="border-0 py-3">Pemegang</th>
                                <th class="border-0 py-3 text-end px-4 rounded-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Vehicle::latest()->take(5)->get() as $v)
                                <tr>
                                    <td class="px-4 py-3"><strong>{{ $v->no_polisi }}</strong><br><small class="text-muted">{{ $v->merk }} {{ $v->tipe }}</small></td>
                                    <td>{{ $v->opd }}</td>
                                    <td>{{ $v->pemegang }}</td>
                                    <td class="text-end px-4">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ $v->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
