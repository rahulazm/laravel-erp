@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'error' => null,
    'icon' => null,
    'size' => null,
    'addon' => null,
    'addonPosition' => 'right',
])

@php
    $inputId = $name ? 'input-' . str_replace(['[', ']', '.'], '-', $name) : uniqid('input-');
    
    $inputClass = 'form-control';
    
    if ($size) {
        $inputClass .= ' form-control-' . $size;
    }
    
    if ($error) {
        $inputClass .= ' is-invalid';
    }
    
    $wrapperClass = 'mb-3';
    if ($icon || $addon) {
        $wrapperClass .= ' has-icon';
    }
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="input-group">
        @if($icon && $addonPosition === 'left')
            <span class="input-group-text">
                <i class="fas fa-{{ $icon }}"></i>
            </span>
        @endif
        
        @if($addon && $addonPosition === 'left')
            <span class="input-group-text">{{ $addon }}</span>
        @endif
        
        <input 
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            {{ $attributes->merge(['class' => $inputClass]) }}>
        
        @if($addon && $addonPosition === 'right')
            <span class="input-group-text">{{ $addon }}</span>
        @endif
        
        @if($icon && $addonPosition === 'right')
            <span class="input-group-text">
                <i class="fas fa-{{ $icon }}"></i>
            </span>
        @endif
        
        @if($error)
            <div class="invalid-feedback">
                {{ $error }}
            </div>
        @endif
    </div>
    
    @if($help)
        <div class="form-text">
            {{ $help }}
        </div>
    @endif
</div>