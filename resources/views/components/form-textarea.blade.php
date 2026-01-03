@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'rows' => 3,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'help' => null,
    'error' => null,
    'size' => null,
])

@php
    $inputId = $name ? 'textarea-' . str_replace(['[', ']', '.'], '-', $name) : uniqid('textarea-');
    
    $textareaClass = 'form-control';
    
    if ($size) {
        $textareaClass .= ' form-control-' . $size;
    }
    
    if ($error) {
        $textareaClass .= ' is-invalid';
    }
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <textarea 
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge(['class' => $textareaClass]) }}>{{ old($name, $value) }}</textarea>
    
    @if($error)
        <div class="invalid-feedback d-block">
            {{ $error }}
        </div>
    @endif
    
    @if($help)
        <div class="form-text">
            {{ $help }}
        </div>
    @endif
</div>