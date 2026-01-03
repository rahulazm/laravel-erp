@props([
    'type' => 'primary',
    'pill' => false,
    'icon' => null,
])

@php
    $badgeClasses = [
        'primary' => 'bg-primary',
        'secondary' => 'bg-secondary',
        'success' => 'bg-success',
        'danger' => 'bg-danger',
        'warning' => 'bg-warning',
        'info' => 'bg-info',
        'light' => 'bg-light text-dark',
        'dark' => 'bg-dark',
    ];
    
    $class = $badgeClasses[$type] ?? 'bg-primary';
    
    if ($pill) {
        $class .= ' rounded-pill';
    }
@endphp

<span {{ $attributes->merge(['class' => "badge {$class}"]) }}>
    @if($icon)
        <i class="fas fa-{{ $icon }} me-1"></i>
    @endif
    {{ $slot }}
</span>