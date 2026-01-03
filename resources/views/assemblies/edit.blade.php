@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Assembly</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('assemblies.index') }}">Assemblies</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Assembly Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('assemblies.update', $assembly->id) }}" method="POST" id="assembly-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Assembly Code *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" 
                                           value="{{ old('code', $assembly->code) }}" required>
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Assembly Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name', $assembly->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Assembly Type *</label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="product" {{ old('type', $assembly->type) == 'product' ? 'selected' : '' }}>Final Product</option>
                                        <option value="sub_assembly" {{ old('type', $assembly->type) == 'sub_assembly' ? 'selected' : '' }}>Sub-Assembly</option>
                                        <option value="component" {{ old('type', $assembly->type) == 'component' ? 'selected' : '' }}>Component</option>
                                        <option value="raw_material" {{ old('type', $assembly->type) == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $assembly->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_of_measure">Unit of Measure</label>
                                    <input type="text" class="form-control @error('unit_of_measure') is-invalid @enderror" 
                                           id="unit_of_measure" name="unit_of_measure" 
                                           value="{{ old('unit_of_measure', $assembly->unit_of_measure) }}">
                                    @error('unit_of_measure')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $assembly->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cost">Standard Cost</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                               id="cost" name="cost" min="0" step="0.01"
                                               value="{{ old('cost', $assembly->cost) }}">
                                    </div>
                                    @error('cost')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="selling_price">Selling Price</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                                               id="selling_price" name="selling_price" min="0" step="0.01"
                                               value="{{ old('selling_price', $assembly->selling_price) }}">
                                    </div>
                                    @error('selling_price')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lead_time">Lead Time (Days)</label>
                                    <input type="number" class="form-control @error('lead_time') is-invalid @enderror" 
                                           id="lead_time" name="lead_time" min="0"
                                           value="{{ old('lead_time', $assembly->lead_time) }}">
                                    @error('lead_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weight">Weight (kg)</label>
                                    <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                           id="weight" name="weight" min="0" step="0.01"
                                           value="{{ old('weight', $assembly->weight) }}">
                                    @error('weight')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_stock">Minimum Stock Level</label>
                                    <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                           id="minimum_stock" name="minimum_stock" min="0"
                                           value="{{ old('minimum_stock', $assembly->minimum_stock) }}">
                                    @error('minimum_stock')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="maximum_stock">Maximum Stock Level</label>
                                    <input type="number" class="form-control @error('maximum_stock') is-invalid @enderror" 
                                           id="maximum_stock" name="maximum_stock" min="0"
                                           value="{{ old('maximum_stock', $assembly->maximum_stock) }}">
                                    @error('maximum_stock')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reorder_point">Reorder Point</label>
                                    <input type="number" class="form-control @error('reorder_point') is-invalid @enderror" 
                                           id="reorder_point" name="reorder_point" min="0"
                                           value="{{ old('reorder_point', $assembly->reorder_point) }}">
                                    @error('reorder_point')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="specifications">Specifications (JSON)</label>
                            <textarea class="form-control @error('specifications') is-invalid @enderror" 
                                      id="specifications" name="specifications" rows="5">{{ old('specifications', json_encode($assembly->specifications, JSON_PRETTY_PRINT)) }}</textarea>
                            @error('specifications')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_active" name="is_active" 
                                               value="1" {{ old('is_active', $assembly->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_purchasable" name="is_purchasable" 
                                               value="1" {{ old('is_purchasable', $assembly->is_purchasable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_purchasable">Purchasable</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $assembly->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Assembly
                            </button>
                            <a href="{{ route('assemblies.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="{{ route('assemblies.show', $assembly->id) }}" 
                               class="btn btn-info">
                                <i class="fas fa-eye"></i> View Tree
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Components Section -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Components / Bill of Materials</h5>
                </div>
                <div class="card-body">
                    <div id="components-container">
                        @foreach($assembly->components as $index => $component)
                        <div class="component-item border p-3 mb-3 rounded">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Component</label>
                                        <select class="form-control component-select" name="components[{{ $index }}][assembly_id]">
                                            <option value="">Select Component</option>
                                            @foreach($allAssemblies as $item)
                                                <option value="{{ $item->id }}" 
                                                    {{ $component->component_id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->code }} - {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" class="form-control" 
                                               name="components[{{ $index }}][quantity]" min="0.01" step="0.01"
                                               value="{{ $component->quantity }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Unit</label>
                                        <input type="text" class="form-control" 
                                               name="components[{{ $index }}][unit]"
                                               value="{{ $component->unit }}">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-danger btn-block remove-component">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Operation</label>
                                        <input type="text" class="form-control" 
                                               name="components[{{ $index }}][operation]"
                                               value="{{ $component->operation }}"
                                               placeholder="e.g., Assembly, Cutting, Welding">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" 
                                                   name="components[{{ $index }}][optional]" value="1"
                                                   {{ $component->optional ? 'checked' : '' }}>
                                            <label class="form-check-label">Optional</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <button type="button" id="add-component" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Add Component
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Assembly Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Assembly ID:</dt>
                        <dd class="col-sm-7">{{ $assembly->id }}</dd>
                        
                        <dt class="col-sm-5">Current Stock:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-{{ $assembly->current_stock < $assembly->minimum_stock ? 'danger' : 'success' }}">
                                {{ $assembly->current_stock }} {{ $assembly->unit_of_measure }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-5">Total Cost:</dt>
                        <dd class="col-sm-7">{{ format_currency($assembly->total_cost) }}</dd>
                        
                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $assembly->creator->name ?? 'System' }}</dd>
                        
                        <dt class="col-sm-5">Created At:</dt>
                        <dd class="col-sm-7">{{ $assembly->created_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Components:</dt>
                        <dd class="col-sm-7">{{ $assembly->components_count ?? 0 }} items</dd>
                        
                        <dt class="col-sm-5">Used In:</dt>
                        <dd class="col-sm-7">{{ $assembly->parentAssemblies->count() }} assemblies</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Assembly Tree</h5>
                </div>
                <div class="card-body">
                    <div class="tree-view">
                        @if($assembly->parentAssemblies->count() > 0)
                            <strong>Parent Assemblies:</strong>
                            <ul class="list-group mt-2">
                                @foreach($assembly->parentAssemblies as $parent)
                                <li class="list-group-item">
                                    <a href="{{ route('assemblies.show', $parent->id) }}">
                                        {{ $parent->code }} - {{ $parent->name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                        
                        @if($assembly->components->count() > 0)
                            <strong class="mt-3 d-block">Child Components:</strong>
                            <ul class="list-group mt-2">
                                @foreach($assembly->components as $component)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('assemblies.show', $component->component_id) }}">
                                        {{ $component->component->code ?? 'N/A' }}
                                    </a>
                                    <span>{{ $component->quantity }} {{ $component->unit }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('assemblies.show', $assembly->id) }}" 
                           class="btn btn-info mb-2">
                            <i class="fas fa-sitemap"></i> View Tree Diagram
                        </a>
                        
                        <a href="{{ route('assemblies.duplicate', $assembly->id) }}" 
                           class="btn btn-warning mb-2" onclick="return confirm('Duplicate this assembly?');">
                            <i class="fas fa-copy"></i> Duplicate Assembly
                        </a>
                        
                        <a href="{{ route('assemblies.bom', $assembly->id) }}" 
                           class="btn btn-success mb-2" target="_blank">
                            <i class="fas fa-file-pdf"></i> Export BOM
                        </a>
                        
                        <form action="{{ route('assemblies.destroy', $assembly->id) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Delete this assembly and all its components? This action cannot be undone.');">
                                <i class="fas fa-trash"></i> Delete Assembly
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.component-item {
    background-color: #f8f9fa;
}
.tree-view ul {
    list-style-type: none;
    padding-left: 20px;
}
</style>
@endpush

@push('scripts')
<script>
let componentIndex = {{ $assembly->components->count() }};

document.getElementById('add-component').addEventListener('click', function() {
    const container = document.getElementById('components-container');
    const template = `
        <div class="component-item border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Component</label>
                        <select class="form-control component-select" name="components[${componentIndex}][assembly_id]">
                            <option value="">Select Component</option>
                            @foreach($allAssemblies as $item)
                                <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" class="form-control" 
                               name="components[${componentIndex}][quantity]" min="0.01" step="0.01" value="1">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" class="form-control" 
                               name="components[${componentIndex}][unit]" value="pcs">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-block remove-component">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Operation</label>
                        <input type="text" class="form-control" 
                               name="components[${componentIndex}][operation]"
                               placeholder="e.g., Assembly, Cutting, Welding">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" 
                                   name="components[${componentIndex}][optional]" value="1">
                            <label class="form-check-label">Optional</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', template);
    componentIndex++;
});

document.addEventListener('click', function(e) {
    if(e.target.closest('.remove-component')) {
        e.target.closest('.component-item').remove();
    }
});

// Format JSON in specifications field
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('specifications');
    if(textarea.value) {
        try {
            const json = JSON.parse(textarea.value);
            textarea.value = JSON.stringify(json, null, 2);
        } catch(e) {
            // Keep original value
        }
    }
});
</script>
@endpush