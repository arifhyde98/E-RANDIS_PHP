@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    
    <!-- SECTION 1: Welcome Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-navy mb-1">Overview Operasional</h3>
            <p class="text-secondary mb-0 small">
                Ringkasan monitoring kendaraan dinas hari ini: <strong class="text-dark">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>
            </p>
        </div>
        <div>
            <button class="btn btn-primary rounded-3 shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-download"></i> Unduh Laporan
            </button>
        </div>
    </div>

    <!-- SECTION 2: Statistic Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Kendaraan -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-gradient-primary p-3">
                <i class="bi bi-car-front-fill stat-icon"></i>
                <div class="position-relative z-2">
                    <div class="text-white-50 fw-semibold small text-uppercase mb-1">Total Kendaraan</div>
                    <h2 class="fw-bold mb-0">{{ $stats['total'] }}</h2>
                    <div class="text-white-50 small fw-medium mt-1"><i class="bi bi-arrow-up-short"></i> Seluruh Aset</div>
                </div>
            </div>
        </div>

        <!-- Kendaraan Aktif -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-gradient-success p-3">
                <i class="bi bi-check-circle-fill stat-icon"></i>
                <div class="position-relative z-2">
                    <div class="text-white-50 fw-semibold small text-uppercase mb-1">Tersedia / Aktif</div>
                    <h2 class="fw-bold mb-0">{{ $stats['available'] }}</h2>
                    <div class="text-white-50 small mt-1">Siap digunakan</div>
                </div>
            </div>
        </div>

        <!-- Kendaraan Dipinjam -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-gradient-warning p-3">
                <i class="bi bi-geo-alt-fill stat-icon"></i>
                <div class="position-relative z-2">
                    <div class="text-white-50 fw-semibold small text-uppercase mb-1">Dipinjam</div>
                    <h2 class="fw-bold mb-0">{{ $stats['borrowed'] }}</h2>
                    <div class="text-white-50 small mt-1">Sedang beroperasi</div>
                </div>
            </div>
        </div>

        <!-- Maintenance -->
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card bg-gradient-danger p-3">
                <i class="bi bi-tools stat-icon"></i>
                <div class="position-relative z-2">
                    <div class="text-white-50 fw-semibold small text-uppercase mb-1">Maintenance</div>
                    <h2 class="fw-bold mb-0">{{ $stats['damaged'] }}</h2>
                    <div class="text-white-50 small fw-medium mt-1">Dalam perbaikan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Split -->
    <div class="row g-4">
        
        <!-- Main Column (Table) -->
        <div class="col-xl-8">
            <!-- SECTION 4: Table Kendaraan Terbaru -->
            <div class="admin-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="fw-bold text-navy mb-0">Status Kendaraan Terbaru</h5>
                        <small class="text-secondary">Pantau aktivitas operasional armada terkini.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-secondary"></i></span>
                            <input type="text" class="form-control border-start-0 bg-light shadow-none" placeholder="Cari Nopol...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="py-3 px-4 border-bottom-0 fw-semibold">Kendaraan</th>
                                <th class="py-3 border-bottom-0 fw-semibold">Pengguna / OPD</th>
                                <th class="py-3 border-bottom-0 fw-semibold">Waktu Update</th>
                                <th class="py-3 border-bottom-0 fw-semibold text-center">Status</th>
                                <th class="py-3 px-4 border-bottom-0 fw-semibold text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($latestVehicles as $v)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-navy">{{ $v->no_polisi }}</div>
                                        <small class="text-secondary">{{ $v->merk }} {{ $v->tipe }}</small>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium text-dark"><i class="bi bi-person-fill text-secondary me-1"></i> {{ $v->pemegang }}</div>
                                        <small class="text-secondary">{{ $v->opd }}</small>
                                    </td>
                                    <td class="py-3 text-secondary small">
                                        {{ $v->updated_at ? $v->updated_at->diffForHumans() : 'Baru saja' }}
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $statusClasses = [
                                                'Tersedia' => 'bg-success text-success',
                                                'Aktif' => 'bg-success text-success',
                                                'Rusak' => 'bg-warning text-warning',
                                                'Maintenance' => 'bg-warning text-warning',
                                                'Nonaktif' => 'bg-secondary text-secondary',
                                                'Dipinjam' => 'bg-info text-info'
                                            ];
                                            $class = $statusClasses[$v->status] ?? 'bg-info text-info';
                                            $label = \App\Models\Vehicle::getStatuses()[$v->status] ?? $v->status;
                                        @endphp
                                        <span class="badge {{ explode(' ', $class)[0] }} rounded-pill px-3">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button class="btn btn-sm btn-light rounded-3 text-primary border shadow-sm">
                                            Detail <i class="bi bi-chevron-right ms-1" style="font-size:0.7rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-light"></i>
                                        Belum ada data operasional.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center">
                    <small class="text-secondary">Menampilkan 6 dari total kendaraan</small>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Side Column (Quick Monitoring & Activity) -->
        <div class="col-xl-4 d-flex flex-column gap-4">
            
            <!-- SECTION 3: Quick Monitoring -->
            <div class="admin-card p-4">
                <h6 class="fw-bold text-navy mb-3"><i class="bi bi-radar text-primary me-2"></i> Monitor Cepat</h6>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                        <div>
                            <div class="fw-semibold text-dark mb-1">Sedang Digunakan</div>
                            <small class="text-secondary d-block">Kendaraan dalam perjalanan dinas</small>
                        </div>
                        <span class="badge bg-info text-white rounded-pill px-3 py-2">{{ $stats['borrowed'] }} Unit</span>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                        <div>
                            <div class="fw-semibold text-dark mb-1">Maintenance Hari Ini</div>
                            <small class="text-secondary d-block">Terjadwal untuk perbaikan rutin</small>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">{{ $stats['damaged'] }} Unit</span>
                    </li>
                    <li class="list-group-item px-0 py-3 pb-0 d-flex justify-content-between align-items-center border-0">
                        <div>
                            <div class="fw-semibold text-danger mb-1">Terlambat Pengembalian</div>
                            <small class="text-secondary d-block">Melewati batas waktu peminjaman</small>
                        </div>
                        <span class="badge bg-danger text-white rounded-pill px-3 py-2">{{ $stats['late'] }} Unit</span>
                    </li>
                </ul>
            </div>

            <!-- RIGHT PANEL OPTIONAL: Aktivitas Terbaru -->
            <div class="admin-card p-4 flex-grow-1">
                <h6 class="fw-bold text-navy mb-4"><i class="bi bi-activity text-primary me-2"></i> Aktivitas Terbaru</h6>
                
                <div class="position-relative border-start border-2 border-light ms-3 ps-4 pb-1">
                    
                    <div class="position-relative mb-4">
                        <span class="position-absolute top-0 start-0 translate-middle bg-info border border-white border-3 rounded-circle" style="width: 16px; height: 16px; margin-left: -25px;"></span>
                        <div class="fw-medium text-dark small">Kendaraan Dipinjam</div>
                        <div class="text-secondary mb-1" style="font-size: 0.8rem;">B 1234 XY oleh Dinas Kesehatan</div>
                        <small class="text-muted" style="font-size: 0.7rem;">10 menit yang lalu</small>
                    </div>

                    <div class="position-relative mb-4">
                        <span class="position-absolute top-0 start-0 translate-middle bg-success border border-white border-3 rounded-circle" style="width: 16px; height: 16px; margin-left: -25px;"></span>
                        <div class="fw-medium text-dark small">Maintenance Selesai</div>
                        <div class="text-secondary mb-1" style="font-size: 0.8rem;">DD 9991 AA siap digunakan</div>
                        <small class="text-muted" style="font-size: 0.7rem;">2 jam yang lalu</small>
                    </div>

                    <div class="position-relative">
                        <span class="position-absolute top-0 start-0 translate-middle bg-primary border border-white border-3 rounded-circle" style="width: 16px; height: 16px; margin-left: -25px;"></span>
                        <div class="fw-medium text-dark small">Pengguna Baru Ditambahkan</div>
                        <div class="text-secondary mb-1" style="font-size: 0.8rem;">Akun Budi Santoso aktif</div>
                        <small class="text-muted" style="font-size: 0.7rem;">Kemarin, 14:30</small>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
