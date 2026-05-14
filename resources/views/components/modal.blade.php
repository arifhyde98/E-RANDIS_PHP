@props([
    'id', 
    'title', 
    'size' => 'md', 
    'submitLabel' => null, 
    'submitAction' => null,
    'form' => null, // ID dari form yang akan di-submit
    'type' => 'default'
])

@php
    $modalSize = [
        'sm' => 'modal-sm',
        'md' => '',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl'
    ][$size] ?? '';

    $headerClass = [
        'default' => 'bg-white text-navy',
        'danger'  => 'bg-danger text-white',
        'success' => 'bg-success text-white'
    ][$type] ?? 'bg-white text-navy';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true" data-bs-backdrop="static">
    <!-- Tambahkan modal-fullscreen-sm-down untuk tampilan full di mobile -->
    <div class="modal-dialog {{ $modalSize }} modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl overflow-hidden custom-modal-content" style="border-radius: 1.25rem;">
            <!-- Header -->
            <div class="modal-header {{ $headerClass }} py-4 px-4 border-bottom border-light sticky-top bg-white" style="z-index: 1055;">
                <div class="d-flex align-items-center gap-3">
                    @if($type === 'danger')
                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    @elseif($type === 'success')
                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    @else
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                            <i class="bi bi-layers-fill"></i>
                        </div>
                    @endif
                    <h5 class="modal-title fw-bold tracking-tight mb-0" id="{{ $id }}Label">{{ $title }}</h5>
                </div>
                <button type="button" class="btn-close {{ $type !== 'default' ? 'btn-close-white' : '' }} shadow-none p-2 rounded-circle" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-white">
                {{ $slot }}
            </div>

            @if($submitLabel)
                <div class="modal-footer bg-light/50 border-top border-light py-3 px-4 d-flex gap-2 sticky-bottom bg-white" style="z-index: 1055;">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-medium px-4 flex-grow-1 flex-md-grow-0" data-bs-dismiss="modal">Batal</button>
                    <button type="{{ $submitAction ? 'button' : 'submit' }}" 
                            @if($form) form="{{ $form }}" @endif
                            @if($submitAction) onclick="{{ $submitAction }}" @endif
                            class="btn btn-{{ $type === 'danger' ? 'danger' : 'primary' }} fw-bold px-4 rounded-3 shadow-sm transition-all hover:scale-105 active:scale-95 flex-grow-1 flex-md-grow-0">
                        {{ $submitLabel }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@once
<style>
    .modal-backdrop.show {
        backdrop-filter: blur(10px) saturate(180%);
        background-color: rgba(15, 23, 42, 0.5);
    }
    
    /* Desktop Animation */
    @media (min-width: 576px) {
        .modal.fade .modal-dialog {
            transform: scale(0.95);
            transition: transform 0.2s ease-out;
        }
        .modal.show .modal-dialog {
            transform: scale(1);
        }
    }

    /* Mobile "Form" Appearance */
    @media (max-width: 575.98px) {
        .custom-modal-content {
            border-radius: 0 !important;
        }
        
        .modal-fullscreen-sm-down .modal-content {
            height: 100% !important;
        }

        .modal-header {
            padding-top: 1.5rem !important;
            padding-bottom: 1rem !important;
        }

        .modal-footer {
            background: white !important;
            padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0px)) !important;
        }

        /* Slide up animation for mobile */
        .modal.fade .modal-dialog {
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal.show .modal-dialog {
            transform: translateY(0);
        }
    }

    .shadow-2xl {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .transition-all {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1); }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
</style>
@endonce
