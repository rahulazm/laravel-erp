@extends('layouts.app')

@section('title', 'Edit Sales Order - ' . $salesOrder->order_number)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">Sales Orders</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sales-orders.show', $salesOrder) }}">{{ $salesOrder->order_number }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Edit Sales Order: {{ $salesOrder->order_number }}</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('sales-orders.update', $salesOrder) }}" id="editSalesOrderForm">
        @csrf
        @method('PUT')
        
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
                                <label class="form-label">Order Number</label>
                                <input type="text" class="form-control" value="{{ $salesOrder->order_number }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" 
                                       value="{{ old('customer_name', $salesOrder->customer_name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Customer Email</label>
                                <input type="email" name="customer_email" class="form-control" 
                                       value="{{ old('customer_email', $salesOrder->customer_email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Customer Phone</label>
                                <input type="text" name="customer_phone" class="form-control" 
                                       value="{{ old('customer_phone', $salesOrder->customer_phone) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label required">Shipping Address</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address', $salesOrder->shipping_address) }}</textarea>
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
                                       value="{{ old('valve_type', $salesOrder->valve_type) }}" 
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
                                        <option value="{{ $category->id }}" 
                                                {{ (old('valve_category_id', $salesOrder->valve_category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" 
                                       value="{{ old('delivery_date', $salesOrder->delivery_date->format('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <input type="number" name="total_amount" class="form-control" 
                                       value="{{ old('total_amount', $salesOrder->total_amount) }}" step="0.01" min="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $salesOrder->notes) }}</textarea>
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
                                <i class="fas fa-save"></i> Update Sales Order
                            </button>
                            <a href="{{ route('sales-orders.show', $salesOrder) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-status-{{ $salesOrder->status }} fs-6 w-100 py-2">
                                {{ ucfirst($salesOrder->status) }}
                            </span>
                        </div>
                        <p class="small text-muted">
                            <i class="fas fa-info-circle"></i>
                            Created on {{ $salesOrder->created_at->format('M d, Y') }} by {{ $salesOrder->createdBy->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Change Log -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Changes</h5>
                    </div>
                    <div class="card-body">
                        @if($salesOrder->activities->isEmpty())
                            <p class="text-muted small">No changes recorded yet</p>
                        @else
                            <ul class="list-unstyled mb-0">
                                @foreach($salesOrder->activities->take(3) as $activity)
                                    <li class="mb-2">
                                        <small class="d-block text-muted">{{ $activity->description }}</small>
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
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
    // Form validation
    $('#editSalesOrderForm').submit(function(e) {
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
        $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    });
});
</script>
@endpush