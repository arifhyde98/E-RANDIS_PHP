<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-RANDIS PHP | Official Monitoring System</title>
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light py-4">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-gradient" href="{{ url('/') }}">E-RANDIS PHP</a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ route('home') }}" class="btn btn-premium">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link fw-medium">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-premium">Get Started</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row min-vh-75 align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="display-3 fw-bold mb-4">
                    Monitor <span class="text-gradient">Kendaraan Dinas</span> Lebih Cepat & Akurat.
                </h1>
                <p class="lead text-secondary mb-5 pe-lg-5">
                    Sistem pemantauan kendaraan dinas (E-RANDIS) hadir dengan interface modern, responsif, dan performa tinggi berbasis Laravel 12.
                </p>
                <div class="d-flex gap-3">
                    <a href="#search-section" class="btn btn-premium px-5 py-3 fs-5">Mulai Monitoring</a>
                    <a href="#" class="btn btn-outline-dark border-2 px-5 py-3 fs-5 rounded-4 fw-semibold">Pelajari Selengkapnya</a>
                </div>
            </div>
            <div class="col-lg-6" id="search-section">
                <div class="premium-card p-5 text-center">
                    <div class="mb-4">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">FEATURED SYSTEM</span>
                    </div>
                    <h3 class="fw-bold mb-3">Pencarian Cepat</h3>
                    <p class="text-muted mb-4">Cari data aset kendaraan berdasarkan plat nomor atau nama pegawai dalam hitungan detik.</p>
                    
                    <form action="{{ route('landing') }}" method="GET">
                        <div class="input-group mb-3 premium-card p-2 shadow-sm">
                            <input type="text" name="q" class="form-control border-0 bg-transparent shadow-none" placeholder="Masukkan Nomor Plat..." value="{{ $query ?? '' }}" required>
                            <button class="btn btn-premium rounded-3" type="submit">Cari Sekarang</button>
                        </div>
                    </form>

                    {{-- Search Results --}}
                    @if(isset($query))
                        <div class="mt-4 pt-4 border-top animate__animated animate__fadeIn">
                            @if($vehicle)
                                <div class="text-start bg-light p-4 rounded-4 border-start border-primary border-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold mb-0">Hasil Ditemukan</h5>
                                        <span class="badge bg-primary px-3 py-2 rounded-pill">{{ $vehicle->no_polisi }}</span>
                                    </div>
                                    <div class="row g-2 small">
                                        <div class="col-6 text-muted">Merk / Tipe:</div>
                                        <div class="col-6 fw-bold">{{ $vehicle->merk }} {{ $vehicle->tipe }}</div>
                                        
                                        <div class="col-6 text-muted">Pemegang:</div>
                                        <div class="col-6 fw-bold text-primary">{{ $vehicle->pemegang }}</div>
                                        
                                        <div class="col-6 text-muted">OPD:</div>
                                        <div class="col-6 fw-bold">{{ $vehicle->opd }}</div>
                                        
                                        <div class="col-6 text-muted">Status:</div>
                                        <div class="col-6"><span class="badge bg-success bg-opacity-10 text-success">{{ $vehicle->status }}</span></div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger border-0 rounded-4 p-4 text-start">
                                    <i class="bi bi-exclamation-circle me-2"></i> Data untuk plat nomor <strong>"{{ $query }}"</strong> tidak ditemukan di database kami.
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <h4 class="fw-bold mb-0">{{ \App\Models\Vehicle::count() }}</h4>
                                <small class="text-muted">Total Aset</small>
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-0">98%</h4>
                                <small class="text-muted">Akurasi</small>
                            </div>
                            <div class="col">
                                <h4 class="fw-bold mb-0">Realtime</h4>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="container py-5 mt-5 border-top text-center text-secondary small">
        &copy; {{ date('Y') }} E-RANDIS PHP. Built with <span class="text-danger">&hearts;</span> using Laravel 12 & Bootstrap 5.
    </footer>
</body>
</html>
