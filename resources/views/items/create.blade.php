@extends('layouts.app')

@section('title', 'Create Item')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Create New Item</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('items.store') }}" id="itemForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Item Code</label>
                                <input type="text" name="item_code" class="form-control" 
                                       value="{{ old('item_code', $nextItemCode) }}" required>
                                <small class="text-muted">Unique identifier for the item</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Item Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="raw_material" {{ old('type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                                    <option value="sub_assembly" {{ old('type') == 'sub_assembly' ? 'selected' : '' }}>Sub Assembly</option>
                                    <option value="finished_good" {{ old('type') == 'finished_good' ? 'selected' : '' }}>Finished Good</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Unit of Measure</label>
                                <input type="text" name="unit_of_measure" class="form-control" 
                                       value="{{ old('unit_of_measure', 'each') }}" required>
                                <small class="text-muted">e.g., each, kg, meter, liter</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Unit Cost</label>
                                <input type="number" name="unit_cost" class="form-control" 
                                       value="{{ old('unit_cost', 0) }}" step="0.01" min="0" required>
                                <small class="text-muted">Cost per unit</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specifications Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Specifications</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addSpecification()">
                            <i class="fas fa-plus"></i> Add Specification
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="specifications-container">
                            <!-- Specifications will be added here dynamically -->
                            <div class="text-center py-3" id="no-specifications">
                                <i class="fas fa-list fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No specifications added yet</p>
                            </div>
                        </div>
                        
                        <!-- Specification Suggestions -->
                        @if($specKeys->isNotEmpty())
                            <div class="mt-4">
                                <h6>Common Specifications:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($specKeys as $key)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSuggestedSpec('{{ $key }}')">
                                            {{ $key }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Additional Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Weight</label>
                                <div class="input-group">
                                    <input type="number" name="weight" class="form-control" 
                                           value="{{ old('weight') }}" step="0.01" min="0">
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Material Grade</label>
                                <input type="text" name="material_grade" class="form-control" 
                                       value="{{ old('material_grade') }}" list="materialGradeSuggestions">
                                <datalist id="materialGradeSuggestions">
                                    @foreach($materialGrades as $grade)
                                        <option value="{{ $grade }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_obsolete" id="isObsolete" value="1" {{ old('is_obsolete') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isObsolete">
                                        Mark as obsolete (not available for new BOMs)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Actions Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Item
                            </button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Item Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Item Statistics</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Items
                                <span class="badge bg-primary rounded-pill">{{ $totalItems }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Raw Materials
                                <span class="badge bg-info rounded-pill">{{ $rawMaterialCount }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Sub Assemblies
                                <span class="badge bg-warning rounded-pill">{{ $subAssemblyCount }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Finished Goods
                                <span class="badge bg-success rounded-pill">{{ $finishedGoodCount }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Version Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Version Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            All items are versioned. This will be version 1.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Initial Change Description</label>
                            <textarea name="changes_description" class="form-control" rows="3" 
                                      placeholder="Describe this item...">{{ old('changes_description') }}</textarea>
                            <small class="text-muted">This will be recorded in the version history</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let specCount = 0;

function addSpecification() {
    specCount++;
    const container = document.getElementById('specifications-container');
    const noSpecs = document.getElementById('no-specifications');
    
    if (noSpecs) {
        noSpecs.style.display = 'none';
    }
    
    const specDiv = document.createElement('div');
    specDiv.className = 'specification-item border rounded p-3 mb-3';
    specDiv.id = 'spec-' + specCount;
    
    specDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0">Specification #${specCount}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeSpecification(${specCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Key</label>
                <input type="text" name="specifications[key][]" class="form-control spec-key" 
                       placeholder="e.g., Diameter, Length, Material">
            </div>
            <div class="col-md-6">
                <label class="form-label">Value</label>
                <input type="text" name="specifications[value][]" class="form-control" 
                       placeholder="e.g., 10mm, 100cm, Steel">
            </div>
        </div>
    `;
    
    container.appendChild(specDiv);
}

function removeSpecification(id) {
    const spec = document.getElementById('spec-' + id);
    if (spec) {
        spec.remove();
        specCount--;
        
        // Show no specs message if all removed
        const container = document.getElementById('specifications-container');
        if (container.children.length === 0 || (container.children.length === 1 && container.children[0].id === 'no-specifications')) {
            document.getElementById('no-specifications').style.display = 'block';
        }
    }
}

function addSuggestedSpec(key) {
    addSpecification();
    const newSpecKey = document.querySelector(`#spec-${specCount} .spec-key`);
    if (newSpecKey) {
        newSpecKey.value = key;
    }
}

// Form validation
document.getElementById('itemForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let valid = true;
    
    requiredFields.forEach(field => {
        if (!field.value) {
            field.classList.add('is-invalid');
            valid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Check if item code is unique (would need AJAX call in real implementation)
    const itemCode = this.querySelector('[name="item_code"]').value;
    if (itemCode) {
        // In real implementation, make AJAX call to check uniqueness
    }
    
    if (!valid) {
        e.preventDefault();
        alert('Please fill all required fields');
    } else {
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Item...';
    }
});
</script>
@endpush