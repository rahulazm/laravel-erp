@extends('layouts.app')

@section('title', 'Sales Order - ' . $salesOrder->order_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales-orders.index') }}">Sales Orders</a></li>
                    <li class="breadcrumb-item active">{{ $salesOrder->order_number }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Sales Order: {{ $salesOrder->order_number }}</h1>
                    <p class="text-muted mb-0">{{ $salesOrder->customer_name }} • {{ $salesOrder->valve_type }}</p>
                </div>
                <div class="btn-group">
                    @can('update', $salesOrder)
                    <a href="{{ route('sales-orders.edit', $salesOrder) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endcan
                    @if($salesOrder->status == 'confirmed')
                    <a href="{{ route('sales-orders.work-order', $salesOrder) }}" class="btn btn-success">
                        <i class="fas fa-tools"></i> Create Work Order
                    </a>
                    @endif
                    <a href="{{ route('reports.export') }}?type=sales_orders&id={{ $salesOrder->id }}" class="btn btn-info">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-status-{{ $salesOrder->status }} fs-6 px-3 py-2">
                                {{ ucfirst($salesOrder->status) }}
                            </span>
                            @if($salesOrder->isOverdue())
                                <span class="badge bg-danger ms-2 fs-6 px-3 py-2">
                                    <i class="fas fa-exclamation-triangle"></i> Overdue
                                </span>
                            @endif
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Created: {{ $salesOrder->created_at->format('M d, Y') }}</small><br>
                            <small class="text-muted">Delivery: {{ $salesOrder->delivery_date->format('M d, Y') }}</small>
                        </div>
                    </div>
                    
                    <!-- Status Update Form -->
                    @can('update', $salesOrder)
                    <div class="mt-3">
                        <form method="POST" action="{{ route('sales-orders.update-status', $salesOrder) }}" class="row g-2">
                            @csrf
                            <div class="col-md-4">
                                <select name="status" class="form-select">
                                    <option value="draft" {{ $salesOrder->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending" {{ $salesOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ $salesOrder->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="in_production" {{ $salesOrder->status == 'in_production' ? 'selected' : '' }}>In Production</option>
                                    <option value="completed" {{ $salesOrder->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $salesOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="status_notes" class="form-control" placeholder="Status change notes...">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Update Status</button>
                            </div>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Order Details -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Customer Name:</th>
                                    <td>{{ $salesOrder->customer_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $salesOrder->customer_email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $salesOrder->customer_phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Shipping Address:</h6>
                            <p class="text-muted">{{ $salesOrder->shipping_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Product Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Valve Type:</th>
                                    <td>{{ $salesOrder->valve_type }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        @if($salesOrder->valveCategory)
                                            <span class="badge bg-primary">
                                                {{ $salesOrder->valveCategory->code }} - {{ $salesOrder->valveCategory->name }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Delivery Date:</th>
                                    <td>
                                        {{ $salesOrder->delivery_date->format('M d, Y') }}
                                        @if($salesOrder->isOverdue())
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Total Amount:</th>
                                    <td class="fs-4 fw-bold text-primary">₹{{ number_format($salesOrder->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $salesOrder->createdBy->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created On:</th>
                                    <td>{{ $salesOrder->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($salesOrder->notes)
                    <div class="mt-3">
                        <h6>Notes:</h6>
                        <div class="alert alert-light">
                            {{ $salesOrder->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- BOMs Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bill of Materials (BOMs)</h5>
                    <a href="{{ route('boms.create') }}?sales_order_id={{ $salesOrder->id }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add BOM
                    </a>
                </div>
                <div class="card-body">
                    <!-- BOM Statistics -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="text-center p-2 border rounded">
                                <div class="fs-4 fw-bold">{{ $bomStats['total'] }}</div>
                                <small class="text-muted">Total BOMs</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-2 border rounded">
                                <div class="fs-4 fw-bold text-success">{{ $bomStats['approved'] }}</div>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-2 border rounded">
                                <div class="fs-4 fw-bold text-warning">{{ $bomStats['pending'] }}</div>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-2 border rounded">
                                <div class="fs-4 fw-bold text-danger">{{ $bomStats['rejected'] }}</div>
                                <small class="text-muted">Rejected</small>
                            </div>
                        </div>
                    </div>

                    <!-- BOMs Table -->
                    @if($salesOrder->boms->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
                            <h5>No BOMs Created</h5>
                            <p class="text-muted">Create a BOM to get started with production</p>
                            <a href="{{ route('boms.create') }}?sales_order_id={{ $salesOrder->id }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create BOM
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>BOM #</th>
                                        <th>Name</th>
                                        <th>Assembly</th>
                                        <th>Status</th>
                                        <th>Version</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrder->boms as $bom)
                                        <tr>
                                            <td>
                                                <a href="{{ route('boms.show', $bom) }}" class="text-decoration-none">
                                                    {{ $bom->bom_number }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($bom->name, 30) }}</td>
                                            <td>{{ $bom->assembly->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-badge bg-status-{{ $bom->status }}">
                                                    {{ ucfirst($bom->status) }}
                                                </span>
                                            </td>
                                            <td>v{{ $bom->version }}</td>
                                            <td>{{ $bom->created_at->format('M d') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('boms.show', $bom) }}" class="btn btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('boms.edit', $bom) }}" class="btn btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Activity & Stats -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($salesOrder->status == 'confirmed')
                            <a href="{{ route('sales-orders.work-order', $salesOrder) }}" class="btn btn-success">
                                <i class="fas fa-tools"></i> Create Work Order
                            </a>
                        @endif
                        <a href="{{ route('boms.create') }}?sales_order_id={{ $salesOrder->id }}" class="btn btn-primary">
                            <i class="fas fa-list-alt"></i> Create New BOM
                        </a>
                        <a href="#" class="btn btn-info">
                            <i class="fas fa-file-invoice"></i> Generate Invoice
                        </a>
                        <a href="{{ route('reports.export') }}?type=sales_orders&id={{ $salesOrder->id }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Export Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($activities->isEmpty())
                        <p class="text-muted text-center">No recent activity</p>
                    @else
                        <div class="timeline">
                            @foreach($activities as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $activity->description }}</h6>
                                        <small class="text-muted">
                                            {{ $activity->causer->name ?? 'System' }} • 
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Similar Orders -->
            @if($similarOrders->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Similar Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($similarOrders as $order)
                                <a href="{{ route('sales-orders.show', $order) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $order->order_number }}</h6>
                                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                    </div>
                                    <small class="text-muted">{{ $order->customer_name }}</small>
                                    <div>
                                        <span class="badge bg-status-{{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    padding-left: 20px;
}
.timeline-marker {
    position: absolute;
    left: -6px;
    top: 6px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    padding-bottom: 10px;
    border-left: 2px solid #e9ecef;
    padding-left: 15px;
}
.timeline-item:last-child .timeline-content {
    border-left: none;
}
</style>
@endsection