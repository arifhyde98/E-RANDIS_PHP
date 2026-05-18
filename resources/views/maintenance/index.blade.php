@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="container-fluid px-0">
    <!-- PAGE HEADER -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                    <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">Maintenance</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-navy mb-0">Maintenance & Pemeliharaan</h3>
        </div>
    </div>

    <!-- MAIN CARD AREA -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <!-- Hero Coming Soon Card -->
            <div id="maintenance-hero" class="card border-0 shadow-sm overflow-hidden mb-4 pulse-glow text-white" style="border-radius: 1.25rem;">
                <div class="card-body p-5 text-center position-relative">
                    <!-- Background decor elements -->
                    <div class="position-absolute opacity-10 end-0 top-0 spin-slow" style="font-size: 15rem; margin-right: -4rem; margin-top: -4rem; pointer-events: none;">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <div class="position-absolute opacity-10 start-0 bottom-0 float-animation" style="font-size: 8rem; margin-left: -2rem; margin-bottom: -2rem; pointer-events: none;">
                        <i class="bi bi-tools"></i>
                    </div>

                    <!-- Hero Content -->
                    <div class="my-4 position-relative" style="z-index: 2;">
                        <div class="bg-white bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle p-4 mb-4 float-animation" style="width: 100px; height: 100px; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                            <i class="bi bi-tools text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider mb-3 shadow-sm" style="font-size: 0.75rem; letter-spacing: 1px;">Fitur Masa Depan (E-Maintenance)</span>
                        </div>
                        <h2 class="fw-extrabold mb-3 text-white" style="font-size: 2.25rem;">Sedang Dalam Pengembangan</h2>
                        <p class="text-white-50 mx-auto fs-5 mb-4" style="max-width: 600px; line-height: 1.6;">
                            Kami sedang merancang modul **E-Maintenance** terintegrasi untuk mengoptimalkan pemeliharaan kendaraan dinas, kontrol anggaran daerah (APBD), dan manajemen perbaikan secara real-time.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-warning px-4 py-2 fw-semibold text-dark shadow-sm d-flex align-items-center gap-2" id="btnNotifyRelease">
                                <i class="bi bi-bell-fill"></i> Beri Tahu Saya Jika Rilis
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-light px-4 py-2 fw-semibold">
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Roadmap Title -->
            <div class="text-center my-5">
                <h5 class="fw-bold text-navy text-uppercase tracking-wider mb-2" style="letter-spacing: 1.5px; font-size: 0.85rem;">Peta Jalan Pengembangan</h5>
                <h3 class="fw-extrabold text-navy mb-0">Apa Saja yang Akan Hadir?</h3>
                <p class="text-secondary small mt-1">Berikut adalah fitur utama yang sedang dalam tahap perancangan sistem.</p>
            </div>

            <!-- Grid of Features Roadmap -->
            <div class="row g-4 mb-5">
                <!-- Card 1 -->
                <div class="col-md-6">
                    <div class="card h-100 border bg-white p-4 transition-card shadow-sm" style="border-radius: 1rem;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-primary-subtle rounded-3 p-3 d-inline-flex text-primary">
                                    <i class="bi bi-clock-history fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-navy mb-0">Riwayat Servis Digital</h5>
                                    <span class="badge bg-success-subtle text-success small rounded-pill px-2.5 mt-1" style="font-size: 0.65rem;">Tahap Desain</span>
                                </div>
                            </div>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                Catat seluruh histori servis rutin, perbaikan mesin, ganti oli/ban, hingga penggantian suku cadang lengkap dengan unggah foto nota kuitansi digital untuk transparansi fisik.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-6">
                    <div class="card h-100 border bg-white p-4 transition-card shadow-sm" style="border-radius: 1rem;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-warning-subtle rounded-3 p-3 d-inline-flex text-warning">
                                    <i class="bi bi-cash-coin fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-navy mb-0">Kontrol Anggaran Aset</h5>
                                    <span class="badge bg-success-subtle text-success small rounded-pill px-2.5 mt-1" style="font-size: 0.65rem;">Tahap Desain</span>
                                </div>
                            </div>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                Pantau pagu anggaran pemeliharaan randis tahunan dari APBD per instansi (OPD) untuk mencegah pemborosan atau pengeluaran servis yang melebihi batas nilai ekonomis kendaraan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-6">
                    <div class="card h-100 border bg-white p-4 transition-card shadow-sm" style="border-radius: 1rem;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-danger-subtle rounded-3 p-3 d-inline-flex text-danger">
                                    <i class="bi bi-alarm fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-navy mb-0">Pengingat Pajak & STNK</h5>
                                    <span class="badge bg-primary-subtle text-primary small rounded-pill px-2.5 mt-1" style="font-size: 0.65rem;">Tahap Integrasi</span>
                                </div>
                            </div>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                Sistem notifikasi otomatis sebelum jatuh tempo masa berlaku STNK tahunan dan plat 5 tahunan kepada pemegang kendaraan dan OPD agar administrasi randis selalu patuh hukum.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-md-6">
                    <div class="card h-100 border bg-white p-4 transition-card shadow-sm" style="border-radius: 1rem;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-info-subtle rounded-3 p-3 d-inline-flex text-info">
                                    <i class="bi bi-clipboard2-check fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-navy mb-0">Alur Pengajuan Pengguna</h5>
                                    <span class="badge bg-success-subtle text-success small rounded-pill px-2.5 mt-1" style="font-size: 0.65rem;">Tahap Desain</span>
                                </div>
                            </div>
                            <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                Hubungkan driver/OPD langsung ke Bagian Umum secara digital untuk mengajukan perbaikan ketika terjadi kendala di lapangan, lengkap dengan sistem persetujuan dan rujukan Surat Perintah Kerja (SPK).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Development Progress Section -->
            <div class="card border bg-white p-4 mb-5 shadow-sm" style="border-radius: 1rem;">
                <div class="card-body p-2">
                    <h5 class="fw-bold text-navy mb-3"><i class="bi bi-cpu text-primary me-2"></i> Estimasi Progress Pengembangan</h5>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-secondary fw-semibold">Rancangan UI/UX & Database</span>
                        <span class="small text-navy fw-bold">80% Selesai</span>
                    </div>
                    <div class="progress mb-4" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: 80%; border-radius: 5px;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-secondary fw-semibold">Pengkodean Core Logic & API</span>
                        <span class="small text-warning fw-bold">15% Selesai</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: 15%; border-radius: 5px;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .pulse-glow {
        animation: pulseGlow 3s infinite ease-in-out;
    }
    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.08);
        }
        50% {
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.25);
        }
    }
    .spin-slow {
        animation: spinSlow 20s infinite linear;
    }
    @keyframes spinSlow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .float-animation {
        animation: floatAnim 4s infinite ease-in-out;
    }
    @keyframes floatAnim {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    #maintenance-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%) !important;
        border: none !important;
        border-radius: 1.25rem !important;
    }
    #maintenance-hero h2,
    #maintenance-hero span.badge.bg-warning,
    #maintenance-hero a,
    #maintenance-hero i {
        color: #ffffff !important;
    }
    #maintenance-hero p {
        color: rgba(255, 255, 255, 0.75) !important;
    }
    #maintenance-hero .badge {
        background-color: #f59e0b !important;
        color: #0f172a !important;
    }
    #maintenance-hero #btnNotifyRelease {
        background-color: #f59e0b !important;
        color: #0f172a !important;
        border-color: #f59e0b !important;
    }
    #maintenance-hero .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.4) !important;
        color: #ffffff !important;
    }
    #maintenance-hero .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
    }
    .card.border {
        border: 1px solid var(--card-border) !important;
    }
    .transition-card {
        transition: all 0.3s ease;
        border: 1px solid var(--card-border) !important;
    }
    .transition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(30, 64, 175, 0.12) !important;
        border-color: var(--accent) !important;
    }
    .bg-primary-subtle { background-color: rgba(37, 99, 235, 0.08) !important; }
    .bg-warning-subtle { background-color: rgba(217, 119, 6, 0.08) !important; }
    .bg-danger-subtle { background-color: rgba(220, 38, 38, 0.08) !important; }
    .bg-info-subtle { background-color: rgba(13, 148, 136, 0.08) !important; }
    .text-primary { color: #2563eb !important; }
    .text-warning { color: #d97706 !important; }
    .text-danger { color: #dc2626 !important; }
    .text-info { color: #0d9488 !important; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnNotify = document.getElementById('btnNotifyRelease');
    if (btnNotify) {
        btnNotify.addEventListener('click', function() {
            Swal.fire({
                title: 'Berhasil Didaftarkan!',
                text: 'Terima kasih atas antusiasme Anda. Kami akan mengirimkan notifikasi khusus begitu modul E-Maintenance dirilis.',
                icon: 'success',
                confirmButtonColor: '#1e40af',
                background: document.getElementById('theme-root').getAttribute('data-theme') === 'dark' ? '#1e293b' : '#ffffff',
                color: document.getElementById('theme-root').getAttribute('data-theme') === 'dark' ? '#f1f5f9' : '#1e293b',
            });
        });
    }
});
</script>
@endpush
