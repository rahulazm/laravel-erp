@props([
    'type' => 'button',
    'variant' => 'primary',
    'outline' => false,
    'size' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false,
])

@php
    $buttonClasses = [
        'primary' => $outline ? 'btn-outline-primary' : 'btn-primary',
        'secondary' => $outline ? 'btn-outline-secondary' : 'btn-secondary',
        'success' => $outline ? 'btn-outline-success' : 'btn-success',
        'danger' => $outline ? 'btn-outline-danger' : 'btn-danger',
        'warning' => $outline ? 'btn-outline-warning' : 'btn-warning',
        'info' => $outline ? 'btn-outline-info' : 'btn-info',
        'light' => $outline ? 'btn-outline-light' : 'btn-light',
        'dark' => $outline ? 'btn-outline-dark' : 'btn-dark',
        'link' => 'btn-link',
    ];
    
    $sizeClasses = [
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        null => '',
    ];
    
    $class = "btn {$buttonClasses[$variant]} {$sizeClasses[$size]}";
    
    if ($disabled || $loading) {
        $class .= ' disabled';
    }
@endphp

<button 
    {{ $attributes->merge([
        'type' => $type,
        'class' => $class,
        'disabled' => $disabled || $loading,
    ]) }}>
    
    @if($loading)
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
    @endif
    
    @if($icon && $iconPosition === 'left')
        <i class="fas fa-{{ $icon }} me-2"></i>
    @endif
    
    {{ $slot }}
    
    @if($icon && $iconPosition === 'right')
        <i class="fas fa-{{ $icon }} ms-2"></i>
    @endif
</button>