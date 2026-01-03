@props([
    'header' => null,
    'footer' => null,
    'title' => null,
    'subtitle' => null,
    'border' => true,
    'shadow' => false,
    'hover' => false,
    'class' => '',
])

@php
    $cardClass = 'card';
    
    if ($border) {
        $cardClass .= ' border';
    }
    
    if ($shadow) {
        $cardClass .= ' shadow';
    }
    
    if ($hover) {
        $cardClass .= ' card-hover';
    }
    
    $cardClass .= ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $cardClass]) }}>
    @if($header)
        <div class="card-header">
            {{ $header }}
        </div>
    @endif
    
    <div class="card-body">
        @if($title || $subtitle)
            <div class="card-title">
                @if($title)
                    <h5 class="mb-1">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <p class="card-subtitle text-muted mb-3">{{ $subtitle }}</p>
                @endif
            </div>
        @endif
        
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>