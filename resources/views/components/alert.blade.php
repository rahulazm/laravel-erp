@props([
    'type' => 'info',
    'dismissible' => false,
    'icon' => null,
])

@php
    $alertClasses = [
        'info' => 'alert-info',
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'danger' => 'alert-danger',
        'primary' => 'alert-primary',
        'secondary' => 'alert-secondary',
    ];
    
    $defaultIcons = [
        'info' => 'info-circle',
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'times-circle',
        'primary' => 'info-circle',
        'secondary' => 'info-circle',
    ];
    
    $icon = $icon ?? $defaultIcons[$type] ?? 'info-circle';
@endphp

<div {{ $attributes->merge(['class' => "alert {$alertClasses[$type]} alert-dismissible fade show"]) }} role="alert">
    <div class="d-flex">
        <div class="flex-shrink-0 me-3">
            <i class="fas fa-{{ $icon }} fa-lg"></i>
        </div>
        <div class="flex-grow-1">
            {{ $slot }}
        </div>
    </div>
    
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>