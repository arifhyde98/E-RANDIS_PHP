@props([
    'status'
])

@php
    use App\Enums\VehicleStatus;
    
    // Ensure we are dealing with the Enum instance if it's a string
    $enum = $status instanceof VehicleStatus ? $status : VehicleStatus::tryFrom($status);
    $color = $enum ? $enum->colorClass() : 'bg-secondary';
    $label = $enum ? $enum->label() : ($status ?? 'Unknown');
@endphp

<span {{ $attributes->merge(['class' => "badge $color rounded-pill px-3"]) }}>
    {{ $label }}
</span>
