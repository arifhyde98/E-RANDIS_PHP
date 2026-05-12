<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-RANDIS | Sistem Monitoring Kendaraan Dinas</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body data-bs-spy="scroll" data-bs-target="#navbar-main">

    <!-- Navbar -->
    <nav id="navbar-main" class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <!-- Logo Replikasi Persis Referensi -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                @php
                    $siteLogo = \App\Models\Setting::get('site_logo');
                    $siteName = \App\Models\Setting::get('site_name', 'PEMERINTAH DAERAH');
                @endphp
                
                @if($siteLogo)
                    <img src="{{ Storage::url($siteLogo) }}" alt="Logo" style="height: 40px; width: auto;">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 120" style="width: 32px; height: 38px;">
                        <!-- Shield outline -->
                        <path d="M50 0 L90 20 V70 C90 95 50 120 50 120 C50 120 10 95 10 70 V20 Z" fill="#15803d" stroke="#facc15" stroke-width="6"/>
                        <!-- Inner elements to match government crest look -->
                        <circle cx="50" cy="30" r="8" fill="#facc15"/>
                        <path d="M30 65 C40 50 60 50 70 65 Z" fill="#facc15"/>
                        <path d="M40 85 L50 70 L60 85 Z" fill="#ffffff"/>
                    </svg>
                @endif
                <span class="fw-bold text-white fs-6" style="letter-spacing: 0.05em;">{{ $siteName }}</span>
            </a>
            <button class="navbar-toggler shadow-none border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-medium gap-lg-4 align-items-center">
                    <li class="nav-item position-relative">
                        <a class="nav-link active pb-1" href="#hero-section">Beranda</a>
                        <!-- Active line indicator exactly under text as shown in image -->
                        <div class="position-absolute bottom-0 start-0 w-100 bg-primary" style="height: 3px; border-radius: 2px;"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pb-1" href="#search-section">Cek Kendaraan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pb-1" href="#feature-section">Informasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link pb-1" href="#footer">Kontak</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero-section" style="
        @php
            $heroBg = \App\Models\Setting::get('hero_bg_image', 'images/hero-illustration.png');
            $bgUrl = Str::startsWith($heroBg, 'images/') ? asset($heroBg) : Storage::url($heroBg);
        @endphp
        background: linear-gradient(rgba(30, 64, 175, 0.85), rgba(30, 58, 138, 0.95)), url('{{ $bgUrl }}');
        background-size: cover;
        background-position: center;
    ">
        <div class="container position-relative z-1">
            <div class="row align-items-center pt-3 pt-lg-0">
                <div class="col-lg-7 mb-5 mb-lg-0 pe-lg-4">
                    <!-- Judul 1 baris persis seperti referensi -->
                    <h1 class="mb-4 fw-bold text-white lh-sm" style="font-size: 2.85rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">{{ \App\Models\Setting::get('hero_title', 'E-RANDIS') }}</h1>
                    <p class="lead mb-5 text-white opacity-90 fw-normal" style="max-width: 560px; font-size: 1.15rem; line-height: 1.6; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                        {{ \App\Models\Setting::get('hero_subtitle', 'Sistem Monitoring Kendaraan Dinas Pemerintah Daerah') }}
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#search-section" class="btn btn-primary px-4 py-2 fs-6 fw-semibold">Cek Kendaraan</a>
                        <a href="#feature-section" class="btn btn-outline-white px-4 py-2 fs-6 fw-semibold">Pelajari Sistem</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center text-lg-end mt-4 mt-lg-0">
                    <div class="hero-image-wrapper">
                        @php
                            $heroImage = \App\Models\Setting::get('hero_image', 'images/hero-illustration.png');
                        @endphp
                        <img src="{{ Str::startsWith($heroImage, 'images/') ? asset($heroImage) : Storage::url($heroImage) }}" alt="Monitoring Illustration" style="max-height: 400px; width: auto;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hero Curve/Wave persis referensi (melengkung ke bawah di tengah) -->
        <div class="hero-curve">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0,20 C450,110 990,110 1440,20 V120 H0 Z" fill="#ffffff"/>
            </svg>
        </div>
    </section>

    <!-- Search Section -->
    <section id="search-section" class="search-section mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="text-center mb-4 mt-3">
                        <!-- Judul dengan garis bawah tipis persis referensi -->
                         
                        <h3 class="fw-bold text-navy mb-2">Cek Status Kendaraan Anda</h3>
                        <div class="mx-auto" style="width: 240px; height: 1px; background-color: #cbd5e1;"></div>
                    </div>
                    
                    <div class="search-card border border-light-subtle">
                        <form action="{{ route('landing') }}" method="GET">
                            <div class="d-flex flex-column flex-md-row gap-2 align-items-stretch">
                                <div class="flex-grow-1">
                                    <input type="text" name="q" class="form-control form-control-lg border py-3 px-4 shadow-none fs-6" placeholder="Masukkan Nomor Polisi" value="{{ $query ?? '' }}" required style="text-transform: uppercase;">
                                </div>
                                <div>
                                    <button class="btn btn-primary px-5 py-3 fw-bold h-100 w-100" type="submit" style="background-color: #0256c4;">Cari</button>
                                </div>
                            </div>
                            <div class="form-text mt-2 text-secondary text-start ps-1 small">
                                Contoh: {{ $query ?? 'DD 1234 XX' }}
                            </div>
                        </form>

                        @if(isset($query))
                            <div class="mt-4 pt-4 border-top text-start">
                                @if($vehicle)
                                    <div class="p-4 bg-light rounded-3 border-start border-primary border-4 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <div class="text-secondary small mb-1">Hasil Pencarian</div>
                                                <h4 class="fw-bold text-primary mb-0">{{ $vehicle->no_polisi }}</h4>
                                            </div>
                                            <span class="badge bg-success px-3 py-2 rounded-pill fw-medium">
                                                {{ \App\Models\Vehicle::getStatuses()[$vehicle->status] ?? $vehicle->status }}
                                            </span>
                                        </div>
                                        
                                        <div class="row g-3 pt-2 border-top border-light">
                                            <div class="col-md-4">
                                                <div class="text-secondary small">Nama Kendaraan</div>
                                                <div class="fw-bold text-dark">{{ $vehicle->merk }} {{ $vehicle->tipe }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-secondary small">OPD / Instansi</div>
                                                <div class="fw-bold text-dark">{{ $vehicle->opd }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-secondary small">Pemegang</div>
                                                <div class="fw-bold text-dark">{{ $vehicle->pemegang }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center p-3 m-0">
                                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                                        <div class="small text-dark">
                                            <strong>Data tidak ditemukan.</strong> Kendaraan dengan plat "{{ $query }}" belum terdaftar.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-center mt-4 text-secondary small">
                        Cek status kendaraan, pengguna, dan informasi penggunaannya dengan mudah.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 animate-on-scroll bg-white" style="margin-top: 4rem;">
        <div class="container py-4">
            <!-- Judul dilewati garis tengah persis referensi -->
            <div class="position-relative text-center mb-5">
                <hr class="position-absolute w-100 top-50 start-0 translate-middle-y m-0" style="border-color: #cbd5e1; z-index: 0;">
                <h3 class="fw-bold d-inline-block bg-white px-4 position-relative text-navy m-0" style="z-index: 1;">Features</h3>
            </div>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="feature-card border border-light-subtle rounded-4 p-4 h-100 bg-white shadow-sm">
                        <i class="bi bi-car-front-fill text-primary display-5 mb-3 d-block" style="color: #1e40af;"></i>
                        <h6 class="fw-bold mb-3 text-navy">Monitoring Kendaraan</h6>
                        <!-- Garis pemisah kecil di dalam card persis referensi -->
                        <hr class="mx-auto my-2" style="width: 40px; border-color: #e2e8f0;">
                        <p class="text-secondary small mt-3 mb-0" style="font-size: 0.85rem;">Pantau kondisi kendaraan secara real-time.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="feature-card border border-light-subtle rounded-4 p-4 h-100 bg-white shadow-sm">
                        <i class="bi bi-clipboard-check-fill text-primary display-5 mb-3 d-block" style="color: #1e40af;"></i>
                        <h6 class="fw-bold mb-3 text-navy">Status Penggunaan</h6>
                        <hr class="mx-auto my-2" style="width: 40px; border-color: #e2e8f0;">
                        <p class="text-secondary small mt-3 mb-0" style="font-size: 0.85rem;">Lihat status aktif, dipinjam, atau maintenance</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="feature-card border border-light-subtle rounded-4 p-4 h-100 bg-white shadow-sm">
                        <i class="bi bi-person-check-fill text-primary display-5 mb-3 d-block" style="color: #1e40af;"></i>
                        <h6 class="fw-bold mb-3 text-navy">Data Pengguna</h6>
                        <hr class="mx-auto my-2" style="width: 40px; border-color: #e2e8f0;">
                        <p class="text-secondary small mt-3 mb-0" style="font-size: 0.85rem;">Informasi lengkap pengguna kendaraan</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 text-center">
                    <div class="feature-card border border-light-subtle rounded-4 p-4 h-100 bg-white shadow-sm">
                        <i class="bi bi-clock-history text-primary display-5 mb-3 d-block" style="color: #1e40af;"></i>
                        <h6 class="fw-bold mb-3 text-navy">Riwayat Kendaraan</h6>
                        <hr class="mx-auto my-2" style="width: 40px; border-color: #e2e8f0;">
                        <p class="text-secondary small mt-3 mb-0" style="font-size: 0.85rem;">Catatan lengkap riwayat penggunaan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light animate-on-scroll">
        <div class="container py-4">
            <!-- Judul dilewati garis tengah persis referensi -->
            <div class="position-relative text-center mb-4">
                <hr class="position-absolute w-100 top-50 start-0 translate-middle-y m-0" style="border-color: #e2e8f0; z-index: 0;">
                <h3 class="fw-bold d-inline-block bg-white px-4 position-relative text-navy m-0" style="z-index: 1;">Tentang E-RANDIS</h3>
            </div>
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <p class="text-secondary small lh-base m-0" style="font-size: 0.95rem;">
                        Sistem untuk transparansi dan efisiensi pengelolaan kendaraan dinas di lingkungan pemerintah daerah. Memastikan penggunaan kendaraan lebih aman dan terpantau.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="py-5 bg-white border-top border-light-subtle">
        <div class="container text-center pt-2">
            <!-- Logo Replikasi Persis Referensi di Footer -->
            <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                @if($siteLogo)
                    <img src="{{ Storage::url($siteLogo) }}" alt="Logo" style="height: 30px; width: auto;">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 120" style="width: 24px; height: 28px;">
                        <path d="M50 0 L90 20 V70 C90 95 50 120 50 120 C50 120 10 95 10 70 V20 Z" fill="#15803d" stroke="#facc15" stroke-width="6"/>
                        <circle cx="50" cy="30" r="8" fill="#facc15"/>
                        <path d="M30 65 C40 50 60 50 70 65 Z" fill="#facc15"/>
                        <path d="M40 85 L50 70 L60 85 Z" fill="#ffffff"/>
                    </svg>
                @endif
                <span class="fw-bold text-navy fs-6 letter-spacing-1">{{ $siteName }}</span>
            </div>
            
            <!-- Garis pemisah di bawah logo persis referensi -->
            <hr class="mx-auto mb-4" style="width: 100%; max-width: 900px; border-color: #e2e8f0;">
            
            <div class="text-secondary small mb-2" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} Pemerintah Daerah. All Rights Reserved.
            </div>
            <div class="text-secondary small fw-medium" style="font-size: 0.8rem;">
                Kontak: <span class="text-navy">admin@example.com</span>
            </div>
        </div>
    </footer>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
