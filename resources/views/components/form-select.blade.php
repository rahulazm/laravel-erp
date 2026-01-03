@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'multiple' => false,
    'help' => null,
    'error' => null,
    'size' => null,
    'grouped' => false,
])

@php
    $inputId = $name ? 'select-' . str_replace(['[', ']', '.'], '-', $name) : uniqid('select-');
    
    $selectClass = 'form-select';
    
    if ($size) {
        $selectClass .= ' form-select-' . $size;
    }
    
    if ($error) {
        $selectClass .= ' is-invalid';
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
    
    <select 
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->merge(['class' => $selectClass]) }}>
        
        @if($placeholder && !$multiple)
            <option value="" disabled selected>{{ $placeholder }}</option>
        @endif
        
        @if($grouped)
            @foreach($options as $groupLabel => $groupOptions)
                <optgroup label="{{ $groupLabel }}">
                    @foreach($groupOptions as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}"
                            @if($selected && (
                                ($multiple && in_array($optionValue, (array) $selected)) || 
                                (!$multiple && $optionValue == $selected)
                            ))
                                selected
                            @endif>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        @else
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}"
                    @if($selected && (
                        ($multiple && in_array($optionValue, (array) $selected)) || 
                        (!$multiple && $optionValue == $selected)
                    ))
                        selected
                    @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @endif
    </select>
    
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