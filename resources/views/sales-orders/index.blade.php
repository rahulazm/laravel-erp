@extends('layouts.app')

@section('title', 'Sales Orders')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Sales Orders</h1>
                <div>
                    <a href="{{ route('sales-orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Sales Order
                    </a>
                    <a href="{{ route('reports.export') }}?type=sales_orders" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sales-orders.index') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="in_production" {{ request('status') == 'in_production' ? 'selected' : '' }}>In Production</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Valve Category</label>
                        <select name="valve_category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($valveCategories as $category)
                                <option value="{{ $category->id }}" {{ request('valve_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Valve Type</label>
                        <select name="valve_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($valveTypes as $type)
                                <option value="{{ $type }}" {{ request('valve_type') == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Orders</h6>
                            <h3>{{ $totalOrders }}</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Pending</h6>
                            <h3>{{ $pendingOrders }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Completed</h6>
                            <h3>{{ $completedOrders }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Cancelled</h6>
                            <h3>{{ $cancelledOrders }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Orders Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Valve Type</th>
                            <th>Category</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Delivery Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salesOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('sales-orders.show', $order) }}" class="text-decoration-none fw-bold">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($order->customer_name, 20) }}</td>
                                <td>{{ $order->valve_type }}</td>
                                <td>{{ $order->valveCategory->code ?? 'N/A' }}</td>
                                <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="status-badge bg-status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->delivery_date->format('M d, Y') }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('sales-orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $order)
                                        <a href="{{ route('sales-orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @if($order->status == 'confirmed')
                                        <a href="{{ route('sales-orders.work-order', $order) }}" class="btn btn-sm btn-success" title="Create Work Order">
                                            <i class="fas fa-tools"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5>No sales orders found</h5>
                                    <p class="text-muted">Create your first sales order to get started</p>
                                    <a href="{{ route('sales-orders.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Sales Order
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($salesOrders->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $salesOrders->firstItem() }} to {{ $salesOrders->lastItem() }} of {{ $salesOrders->total() }} entries
                    </div>
                    <nav>
                        {{ $salesOrders->withQueryString()->links() }}
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('table').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search sales orders..."
        }
    });
});
</script>
@endpush