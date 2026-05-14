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
        'default' => 'bg-light text-navy',
        'danger'  => 'bg-danger text-white',
        'success' => 'bg-success text-white'
    ][$type] ?? 'bg-light text-navy';
@endphp

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog {{ $modalSize }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header {{ $headerClass }} py-3 px-4 border-bottom-0">
                <h5 class="modal-title fw-bold" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close {{ $type !== 'default' ? 'btn-close-white' : '' }} shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                {{ $slot }}
            </div>

            @if($submitLabel)
                <div class="modal-footer bg-light border-top-0 py-3 px-4">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="{{ $submitAction ? 'button' : 'submit' }}" 
                            @if($form) form="{{ $form }}" @endif
                            @if($submitAction) onclick="{{ $submitAction }}" @endif
                            class="btn btn-{{ $type === 'danger' ? 'danger' : 'primary' }} fw-bold px-4 shadow-sm">
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
        backdrop-filter: blur(4px);
        background-color: rgba(0, 0, 0, 0.4);
    }
</style>
@endonce
