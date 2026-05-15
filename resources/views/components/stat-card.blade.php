@props([
    'title',
    'value',
    'icon',
    'gradient' => 'primary',
    'subtitle' => null
])

<div {{ $attributes->merge(['class' => "card stat-card bg-gradient-$gradient p-3"]) }}>
    <i class="bi bi-{{ $icon }} stat-icon" style="z-index: 1;"></i>
    <div class="position-relative z-2">
        <div class="text-white-50 fw-semibold small text-uppercase mb-1">{{ $title }}</div>
        <h2 class="fw-bold mb-0">{{ $value }}</h2>
        @if($subtitle)
            <div class="text-white-50 small fw-medium mt-1">{{ $subtitle }}</div>
        @endif
    </div>
</div>
