@extends('layouts.app')

@section('title', 'Create Sales Order')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">Sales Orders</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Create Sales Order</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('sales-orders.store') }}" id="salesOrderForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Order Number</label>
                                <input type="text" class="form-control" value="{{ $nextOrderNumber }}" readonly>
                                <small class="text-muted">Auto-generated</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Customer Email</label>
                                <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Customer Phone</label>
                                <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label required">Shipping Address</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Product Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Valve Type</label>
                                <input type="text" name="valve_type" class="form-control" 
                                       value="{{ old('valve_type') }}" 
                                       list="valveTypeSuggestions" required>
                                <datalist id="valveTypeSuggestions">
                                    @foreach($existingValveTypes as $type)
                                        <option value="{{ $type }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Valve Category</label>
                                <select name="valve_category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($valveCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('valve_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" 
                                       value="{{ old('delivery_date') }}" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <input type="number" name="total_amount" class="form-control" 
                                       value="{{ old('total_amount', 0) }}" step="0.01" min="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optional BOM Creation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">BOM Creation</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="create_bom" id="createBomSwitch" value="1">
                            <label class="form-check-label" for="createBomSwitch">
                                Create BOM for this sales order
                            </label>
                        </div>
                        <div id="bomDetails" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> A draft BOM will be created along with this sales order.
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
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Submit Order
                            </button>
                            <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Initial Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" selected>Draft</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                            </select>
                        </div>
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                Draft orders can be edited later. Submitted orders will go through approval process.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Orders
                                <span class="badge bg-primary rounded-pill">{{ $totalOrders }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pending Approval
                                <span class="badge bg-warning rounded-pill">{{ $pendingOrders }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                In Production
                                <span class="badge bg-info rounded-pill">{{ $inProductionOrders }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle BOM details
    $('#createBomSwitch').change(function() {
        if ($(this).is(':checked')) {
            $('#bomDetails').show();
        } else {
            $('#bomDetails').hide();
        }
    });

    // Calculate delivery date minimum (tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    $('input[name="delivery_date"]').attr('min', minDate);

    // Form validation
    $('#salesOrderForm').submit(function(e) {
        const form = $(this);
        const requiredFields = form.find('[required]');
        let valid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill all required fields');
            return false;
        }
        
        // Show loading state
        $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
});
</script>
@endpush