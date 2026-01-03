@extends('layouts.app')

@section('title', 'Create BOM')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('boms.index') }}">BOMs</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Create Bill of Materials</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('boms.store') }}" id="bomForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- BOM Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">BOM Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">BOM Number</label>
                                <input type="text" class="form-control" value="{{ $nextBomNumber }}" readonly>
                                <small class="text-muted">Auto-generated</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">BOM Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Assembly</label>
                                <select name="assembly_id" class="form-select" required>
                                    <option value="">Select Assembly</option>
                                    @foreach($assemblies as $assembly)
                                        <option value="{{ $assembly->id }}" {{ old('assembly_id') == $assembly->id ? 'selected' : '' }}>
                                            {{ $assembly->code }} - {{ $assembly->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sales Order</label>
                                <select name="sales_order_id" class="form-select">
                                    <option value="">Select Sales Order (Optional)</option>
                                    @foreach($salesOrders as $order)
                                        <option value="{{ $order->id }}" {{ old('sales_order_id') == $order->id ? 'selected' : '' }}>
                                            {{ $order->order_number }} - {{ $order->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Components Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Components</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addComponent()">
                            <i class="fas fa-plus"></i> Add Component
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="components-container">
                            <!-- Components will be added here dynamically -->
                            <div class="text-center py-3" id="no-components">
                                <i class="fas fa-boxes fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No components added yet</p>
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
                        <div class="mb-3">
                            <label class="form-label">Initial Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" selected>Draft</option>
                                <option value="pending_approval">Submit for Approval</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save BOM
                            </button>
                            <a href="{{ route('boms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Change Description -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Change Description</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="changes_description" class="form-control" rows="4" 
                                  placeholder="Describe the changes or reason for creating this BOM...">{{ old('changes_description') }}</textarea>
                        <small class="text-muted">This will be recorded in the version history</small>
                    </div>
                </div>

                <!-- Items List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Available Items</h5>
                    </div>
                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="itemSearch" placeholder="Search items...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="items-list">
                            @foreach($items as $item)
                                <div class="item-entry mb-2 p-2 border rounded" 
                                     data-item-code="{{ $item->item_code }}"
                                     data-item-name="{{ $item->name }}"
                                     data-unit-cost="{{ $item->unit_cost }}"
                                     data-unit-of-measure="{{ $item->unit_of_measure }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $item->item_code }}</strong>
                                            <small class="d-block text-muted">{{ $item->name }}</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemToBOM('{{ $item->id }}', '{{ $item->item_code }}', '{{ $item->name }}')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
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
let componentCount = 0;

function addComponent() {
    componentCount++;
    const container = document.getElementById('components-container');
    const noComponents = document.getElementById('no-components');
    
    if (noComponents) {
        noComponents.style.display = 'none';
    }
    
    const componentDiv = document.createElement('div');
    componentDiv.className = 'component-item border rounded p-3 mb-3';
    componentDiv.id = 'component-' + componentCount;
    
    componentDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0">Component #${componentCount}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeComponent(${componentCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Item</label>
                <select name="components[${componentCount}][item_id]" class="form-select item-select" onchange="updateItemDetails(${componentCount})">
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" 
                                data-unit-cost="{{ $item->unit_cost }}"
                                data-unit-of-measure="{{ $item->unit_of_measure }}">
                            {{ $item->item_code }} - {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="components[${componentCount}][quantity]" 
                       class="form-control quantity-input" value="1" min="0.001" step="0.001" 
                       onchange="calculateTotal(${componentCount})">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input type="text" name="components[${componentCount}][unit_of_measure]" 
                       class="form-control unit-input" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit Cost</label>
                <input type="number" name="components[${componentCount}][unit_cost]" 
                       class="form-control unit-cost-input" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Total Cost</label>
                <input type="number" name="components[${componentCount}][total_cost]" 
                       class="form-control total-cost-input" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Notes</label>
                <input type="text" name="components[${componentCount}][notes]" class="form-control">
            </div>
        </div>
    `;
    
    container.appendChild(componentDiv);
}

function removeComponent(id) {
    const component = document.getElementById('component-' + id);
    if (component) {
        component.remove();
        componentCount--;
        
        // Show no components message if all removed
        if (componentCount === 0) {
            document.getElementById('no-components').style.display = 'block';
        }
    }
}

function updateItemDetails(componentId) {
    const select = document.querySelector(`#component-${componentId} .item-select`);
    const selectedOption = select.options[select.selectedIndex];
    const unitCost = selectedOption.getAttribute('data-unit-cost');
    const unitOfMeasure = selectedOption.getAttribute('data-unit-of-measure');
    
    const unitCostInput = document.querySelector(`#component-${componentId} .unit-cost-input`);
    const unitInput = document.querySelector(`#component-${componentId} .unit-input`);
    
    if (unitCost && unitOfMeasure) {
        unitCostInput.value = unitCost;
        unitInput.value = unitOfMeasure;
        calculateTotal(componentId);
    }
}

function calculateTotal(componentId) {
    const quantity = document.querySelector(`#component-${componentId} .quantity-input`).value;
    const unitCost = document.querySelector(`#component-${componentId} .unit-cost-input`).value;
    const totalCostInput = document.querySelector(`#component-${componentId} .total-cost-input`);
    
    if (quantity && unitCost) {
        const total = parseFloat(quantity) * parseFloat(unitCost);
        totalCostInput.value = total.toFixed(2);
    }
}

function addItemToBOM(itemId, itemCode, itemName) {
    // Find if item already exists in components
    const existingSelects = document.querySelectorAll('.item-select');
    for (let select of existingSelects) {
        if (select.value == itemId) {
            alert('Item already added to BOM');
            return;
        }
    }
    
    // Add new component
    addComponent();
    
    // Set the item in the new component
    const newSelect = document.querySelector(`#component-${componentCount} .item-select`);
    newSelect.value = itemId;
    updateItemDetails(componentCount);
    
    // Set quantity to 1
    const quantityInput = document.querySelector(`#component-${componentCount} .quantity-input`);
    quantityInput.value = 1;
    calculateTotal(componentCount);
}

// Form validation
document.getElementById('bomForm').addEventListener('submit', function(e) {
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
    
    if (!valid) {
        e.preventDefault();
        alert('Please fill all required fields');
    } else {
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating BOM...';
    }
});
</script>
@endpush