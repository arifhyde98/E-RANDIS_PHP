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
            <a class="navbar-brand fw-bold fs-4 text-gradient" href="#">E-RANDIS PHP</a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="btn btn-premium">Dashboard</a>
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
                    <a href="#" class="btn btn-premium px-5 py-3 fs-5">Mulai Monitoring</a>
                    <a href="#" class="btn btn-outline-dark border-2 px-5 py-3 fs-5 rounded-4 fw-semibold">Pelajari Selengkapnya</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="premium-card p-5 text-center">
                    <div class="mb-4">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">FEATURED SYSTEM</span>
                    </div>
                    <h3 class="fw-bold mb-3">Pencarian Cepat</h3>
                    <p class="text-muted mb-4">Cari data aset kendaraan berdasarkan plat nomor atau nama pegawai dalam hitungan detik.</p>
                    <div class="input-group mb-3 premium-card p-2 shadow-sm">
                        <input type="text" class="form-control border-0 bg-transparent shadow-none" placeholder="Masukkan Nomor Plat...">
                        <button class="btn btn-premium rounded-3">Cari Sekarang</button>
                    </div>
                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col">
                                <h4 class="fw-bold mb-0">1.2k+</h4>
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
