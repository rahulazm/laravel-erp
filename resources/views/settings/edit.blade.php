@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Setting</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Setting Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update', $setting->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="key">Setting Key *</label>
                                    <input type="text" class="form-control @error('key') is-invalid @enderror" 
                                           id="key" name="key" 
                                           value="{{ old('key', $setting->key) }}" required
                                           @if($setting->is_protected) readonly @endif>
                                    @error('key')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Category *</label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}" 
                                                {{ old('category', $setting->category) == $cat ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $cat)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="display_name">Display Name *</label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" 
                                   value="{{ old('display_name', $setting->display_name) }}" required>
                            @error('display_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2">{{ old('description', $setting->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="data_type">Data Type *</label>
                                    <select class="form-control @error('data_type') is-invalid @enderror" 
                                            id="data_type" name="data_type" required 
                                            onchange="toggleValueInput()">
                                        <option value="string" {{ old('data_type', $setting->data_type) == 'string' ? 'selected' : '' }}>String</option>
                                        <option value="integer" {{ old('data_type', $setting->data_type) == 'integer' ? 'selected' : '' }}>Integer</option>
                                        <option value="float" {{ old('data_type', $setting->data_type) == 'float' ? 'selected' : '' }}>Float</option>
                                        <option value="boolean" {{ old('data_type', $setting->data_type) == 'boolean' ? 'selected' : '' }}>Boolean</option>
                                        <option value="array" {{ old('data_type', $setting->data_type) == 'array' ? 'selected' : '' }}>Array</option>
                                        <option value="json" {{ old('data_type', $setting->data_type) == 'json' ? 'selected' : '' }}>JSON</option>
                                        <option value="text" {{ old('data_type', $setting->data_type) == 'text' ? 'selected' : '' }}>Text</option>
                                    </select>
                                    @error('data_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="group">Group</label>
                                    <input type="text" class="form-control @error('group') is-invalid @enderror" 
                                           id="group" name="group" 
                                           value="{{ old('group', $setting->group) }}">
                                    @error('group')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="value-input-container">
                            <label for="value">Value *</label>
                            <div id="string-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'string' ? '' : 'display: none;' }}">
                                <input type="text" class="form-control @error('value') is-invalid @enderror" 
                                       name="value" value="{{ old('value', $setting->value) }}">
                            </div>
                            <div id="integer-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'integer' ? '' : 'display: none;' }}">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       name="value" step="1" value="{{ old('value', $setting->value) }}">
                            </div>
                            <div id="float-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'float' ? '' : 'display: none;' }}">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       name="value" step="0.01" value="{{ old('value', $setting->value) }}">
                            </div>
                            <div id="boolean-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'boolean' ? '' : 'display: none;' }}">
                                <select class="form-control @error('value') is-invalid @enderror" name="value">
                                    <option value="1" {{ old('value', $setting->value) == '1' ? 'selected' : '' }}>True</option>
                                    <option value="0" {{ old('value', $setting->value) == '0' ? 'selected' : '' }}>False</option>
                                </select>
                            </div>
                            <div id="array-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'array' ? '' : 'display: none;' }}">
                                <textarea class="form-control @error('value') is-invalid @enderror" 
                                          name="value" rows="3" placeholder="Enter values separated by commas">{{ old('value', is_array($setting->value) ? implode(',', $setting->value) : $setting->value) }}</textarea>
                            </div>
                            <div id="json-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'json' ? '' : 'display: none;' }}">
                                <textarea class="form-control @error('value') is-invalid @enderror" 
                                          name="value" rows="6">{{ old('value', is_string($setting->value) ? $setting->value : json_encode($setting->value, JSON_PRETTY_PRINT)) }}</textarea>
                            </div>
                            <div id="text-value" class="value-input" style="{{ old('data_type', $setting->data_type) == 'text' ? '' : 'display: none;' }}">
                                <textarea class="form-control @error('value') is-invalid @enderror" 
                                          name="value" rows="6">{{ old('value', $setting->value) }}</textarea>
                            </div>
                            @error('value')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="validation_rules">Validation Rules</label>
                                    <input type="text" class="form-control @error('validation_rules') is-invalid @enderror" 
                                           id="validation_rules" name="validation_rules" 
                                           value="{{ old('validation_rules', $setting->validation_rules) }}"
                                           placeholder="e.g., required|min:3|max:255">
                                    @error('validation_rules')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="options">Options (JSON)</label>
                                    <textarea class="form-control @error('options') is-invalid @enderror" 
                                              id="options" name="options" rows="3"
                                              placeholder='{"option1": "value1", "option2": "value2"}'>{{ old('options', $setting->options ? json_encode($setting->options, JSON_PRETTY_PRINT) : '') }}</textarea>
                                    @error('options')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_visible" name="is_visible" 
                                               value="1" {{ old('is_visible', $setting->is_visible) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_visible">Visible in UI</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_editable" name="is_editable" 
                                               value="1" {{ old('is_editable', $setting->is_editable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_editable">Editable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_encrypted" name="is_encrypted" 
                                               value="1" {{ old('is_encrypted', $setting->is_encrypted) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_encrypted">Encrypt Value</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $setting->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Setting
                            </button>
                            <a href="{{ route('settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="{{ route('settings.show', $setting->id) }}" 
                               class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Setting Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Setting ID:</dt>
                        <dd class="col-sm-7">{{ $setting->id }}</dd>
                        
                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $setting->creator->name ?? 'System' }}</dd>
                        
                        <dt class="col-sm-5">Created At:</dt>
                        <dd class="col-sm-7">{{ $setting->created_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ $setting->updated_at->format('d M Y, H:i') }}</dd>
                        
                        @if($setting->is_protected)
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-warning">Protected</span>
                            <small class="d-block text-muted">This is a system setting</small>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Value Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Current Value:</strong>
                        <div class="mt-1 p-2 bg-light rounded">
                            @if($setting->data_type == 'boolean')
                                {{ $setting->value ? 'True' : 'False' }}
                            @elseif($setting->data_type == 'json')
                                <pre class="mb-0"><code>{{ json_encode($setting->value, JSON_PRETTY_PRINT) }}</code></pre>
                            @elseif($setting->data_type == 'array')
                                {{ implode(', ', (array)$setting->value) }}
                            @else
                                {{ $setting->value }}
                            @endif
                        </div>
                    </div>
                    
                    @if($setting->description)
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p class="mb-0">{{ $setting->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Danger Zone</h5>
                </div>
                <div class="card-body">
                    @if(!$setting->is_protected)
                    <form action="{{ route('settings.destroy', $setting->id) }}" 
                          method="POST" onsubmit="return confirm('Are you sure you want to delete this setting? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Setting
                        </button>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-lock"></i> This is a protected system setting and cannot be deleted.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleValueInput() {
    const dataType = document.getElementById('data_type').value;
    document.querySelectorAll('.value-input').forEach(el => {
        el.style.display = 'none';
    });
    document.getElementById(dataType + '-value').style.display = 'block';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleValueInput();
    
    // Format JSON in options field
    const optionsTextarea = document.getElementById('options');
    if(optionsTextarea.value) {
        try {
            const json = JSON.parse(optionsTextarea.value);
            optionsTextarea.value = JSON.stringify(json, null, 2);
        } catch(e) {
            // Keep original value
        }
    }
});
</script>
@endpush