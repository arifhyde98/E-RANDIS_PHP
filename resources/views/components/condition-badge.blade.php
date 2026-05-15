@props(['kondisi'])

@php
    use App\Enums\VehicleCondition;
    
    $enum = $kondisi instanceof VehicleCondition ? $kondisi : VehicleCondition::tryFrom($kondisi);
    
    $color = match($enum) {
        VehicleCondition::BAIK => 'bg-success',
        VehicleCondition::RUSAK_RINGAN => 'bg-warning text-dark',
        VehicleCondition::RUSAK_BERAT => 'bg-danger',
        VehicleCondition::HILANG => 'bg-purple text-white',
        VehicleCondition::DALAM_PENELUSURAN => 'bg-secondary',
        default => 'bg-light text-dark'
    };
    
    $label = $enum ? $enum->label() : ($kondisi ?? 'Tidak Diketahui');
    
    $icon = match($enum) {
        VehicleCondition::BAIK => 'bi-check-circle-fill',
        VehicleCondition::RUSAK_RINGAN => 'bi-exclamation-triangle-fill',
        VehicleCondition::RUSAK_BERAT => 'bi-x-octagon-fill',
        VehicleCondition::HILANG => 'bi-question-circle-fill',
        VehicleCondition::DALAM_PENELUSURAN => 'bi-search',
        default => 'bi-info-circle'
    };
@endphp

<style>
    .bg-purple { background-color: #6f42c1; }
</style>

<span {{ $attributes->merge(['class' => "badge $color rounded-pill px-3 d-inline-flex align-items-center gap-1"]) }}>
    <i class="bi {{ $icon }}" style="font-size: 0.8rem;"></i>
    {{ $label }}
</span>
