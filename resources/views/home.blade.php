@extends('layouts.app')

@section('content')
<div class="container">
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
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </div>
                    <h5 class="fw-bold mb-0">Total Kendaraan</h5>
                </div>
                <h2 class="fw-bold mb-1">1,248</h2>
                <p class="text-success small mb-0 fw-medium">+12% dari bulan lalu</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h5 class="fw-bold mb-0">Aset Aktif</h5>
                </div>
                <h2 class="fw-bold mb-1">856</h2>
                <p class="text-secondary small mb-0 fw-medium">Sedang dalam pemakaian</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="premium-card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h5 class="fw-bold mb-0">Pajak Jatuh Tempo</h5>
                </div>
                <h2 class="fw-bold mb-1">24</h2>
                <p class="text-danger small mb-0 fw-medium">Perlu perhatian segera</p>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="premium-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Aktivitas Terakhir</h4>
                    <button class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-bold">Lihat Semua</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3 rounded-start">Kendaraan</th>
                                <th class="border-0 py-3">Pengguna</th>
                                <th class="border-0 py-3">Status</th>
                                <th class="border-0 py-3 rounded-end text-end px-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3"><strong>DN 1234 XY</strong><br><small class="text-muted">Toyota Innova</small></td>
                                <td>Budi Santoso</td>
                                <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Selesai</span></td>
                                <td class="text-end px-4 text-muted small">2 Jam Lalu</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3"><strong>DN 5678 AB</strong><br><small class="text-muted">Honda CR-V</small></td>
                                <td>Siti Aminah</td>
                                <td><span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Pending</span></td>
                                <td class="text-end px-4 text-muted small">5 Jam Lalu</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
