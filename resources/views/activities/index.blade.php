@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-navy mb-1">Audit Log Sistem</h3>
            <p class="text-secondary mb-0 small">Memantau seluruh jejak aktivitas pengguna di dalam aplikasi.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('activities.clear') }}" method="POST" class="delete-confirm">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-2 rounded-3 shadow-sm">
                    <i class="bi bi-trash3"></i> Bersihkan Log
                </button>
            </form>
        </div>
    </div>

    <div class="admin-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 text-center" style="width: 80px;">Tipe</th>
                        <th class="py-3">Aktivitas</th>
                        <th class="py-3">Dilakukan Oleh</th>
                        <th class="py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr>
                            <td class="px-4 text-center">
                                @php
                                    $icon = match($activity->type) {
                                        'success' => 'bi-check-circle-fill text-success',
                                        'danger' => 'bi-exclamation-octagon-fill text-danger',
                                        'warning' => 'bi-exclamation-triangle-fill text-warning',
                                        default => 'bi-info-circle-fill text-info',
                                    };
                                @endphp
                                <i class="bi {{ $icon }} fs-5"></i>
                            </td>
                            <td class="py-3 fw-medium text-dark">
                                {{ $activity->description }}
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small">{{ $activity->user->name ?? 'Sistem' }}</div>
                                        <small class="text-secondary">{{ $activity->user->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-secondary small">
                                <div>{{ $activity->created_at->translatedFormat('d F Y') }}</div>
                                <div>{{ $activity->created_at->format('H:i:s') }} ({{ $activity->created_at->diffForHumans() }})</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-journal-x fs-1 text-light"></i>
                                    <p class="text-secondary mt-3">Belum ada riwayat aktivitas yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($activities->hasPages())
            <div class="px-4 py-3 bg-light border-top">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
