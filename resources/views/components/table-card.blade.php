@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'filters' => null,
    'thead' => null,
    'pagination' => null,
    'empty' => false,
    'emptyText' => 'Belum ada data yang tersedia.',
    'emptyIcon' => 'bi-inbox'
])

<div class="admin-card overflow-hidden">
    <!-- Header Bagian Atas (Opsi) -->
    @if($title || $actions)
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                @if($title)
                    <h5 class="fw-bold text-navy mb-0">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <small class="text-secondary">{{ $subtitle }}</small>
                @endif
            </div>
            @if($actions)
                <div class="d-flex gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <!-- Area Filter & Search (Opsi) -->
    @if($filters)
        <div class="bg-light p-3 border-bottom border-light">
            {{ $filters }}
            
            <!-- Mobile Swipe Hint -->
            <div class="d-md-none mt-2 text-center">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white border rounded-pill shadow-sm">
                    <i class="bi bi-arrow-left-right text-primary small"></i>
                    <span class="text-secondary" style="font-size: 0.7rem;">Geser tabel untuk melihat detail</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Konten Utama Tabel -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            @if($thead)
                <thead class="bg-white text-secondary small text-uppercase">
                    {{ $thead }}
                </thead>
            @endif
            <tbody class="border-top-0 bg-white">
                @if($empty)
                    <tr>
                        <td colspan="100" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi {{ $emptyIcon }} text-secondary opacity-50" style="font-size: 2.5rem;"></i>
                                </div>
                                <h5 class="fw-bold text-navy mb-1">{{ $emptyText }}</h5>
                                <p class="text-secondary mb-0 small">Silakan tambahkan data baru atau sesuaikan filter pencarian Anda.</p>
                            </div>
                        </td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>

    <!-- Area Pagination (Opsi) -->
    @if($pagination)
        <div class="p-3 border-top bg-white">
            {{ $pagination }}
        </div>
    @endif
</div>
