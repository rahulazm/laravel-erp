@extends('layouts.app')

@section('title', 'BOM - ' . $bom->bom_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('boms.index') }}">BOMs</a></li>
                    <li class="breadcrumb-item active">{{ $bom->bom_number }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">BOM: {{ $bom->bom_number }}</h1>
                    <p class="text-muted mb-0">{{ $bom->name }} • Version {{ $bom->version }}</p>
                </div>
                <div class="btn-group">
                    @if(!$checkout || $checkout->user_id == auth()->id())
                        @if(!$checkout)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                <i class="fas fa-sign-out-alt"></i> Check Out
                            </button>
                        @else
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkinModal">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </button>
                        @endif
                    @endif
                    @can('update', $bom)
                        <a href="{{ route('boms.edit', $bom) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                    <a href="{{ route('reports.export') }}?type=boms&id={{ $bom->id }}" class="btn btn-info">
                        <i class="fas fa-print"></i> Print
                    </a>
                    <a href="{{ route('boms.versions', $bom) }}" class="btn btn-secondary">
                        <i class="fas fa-history"></i> Versions
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Info Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-status-{{ $bom->status }} fs-6 px-3 py-2">
                                        {{ ucfirst($bom->status) }}
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Version</small>
                                    <strong class="fs-5">v{{ $bom->version }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Assembly</small>
                            <strong>{{ $bom->assembly->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Sales Order</small>
                            @if($bom->salesOrder)
                                <a href="{{ route('sales-orders.show', $bom->salesOrder) }}" class="text-decoration-none">
                                    {{ $bom->salesOrder->order_number }}
                                </a>
                            @else
                                <span>N/A</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Created By</small>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <strong>{{ $bom->createdBy->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $bom->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($checkout)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            This BOM is checked out by {{ $checkout->user->name }} since 
                            {{ $checkout->checked_out_at->format('M d, Y H:i') }}
                            @if($checkout->purpose)
                                <br><small>Purpose: {{ $checkout->purpose }}</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - BOM Details -->
        <div class="col-lg-8">
            <!-- BOM Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">BOM Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">BOM Number:</th>
                                    <td>{{ $bom->bom_number }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $bom->name }}</td>
                                </tr>
                                <tr>
                                    <th>Assembly:</th>
                                    <td>{{ $bom->assembly->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Sales Order:</th>
                                    <td>
                                        @if($bom->salesOrder)
                                            <a href="{{ route('sales-orders.show', $bom->salesOrder) }}" class="text-decoration-none">
                                                {{ $bom->salesOrder->order_number }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status:</th>
                                    <td>
                                        <span class="badge bg-status-{{ $bom->status }}">
                                            {{ ucfirst($bom->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Version:</th>
                                    <td>v{{ $bom->version }}</td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $bom->createdBy->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created On:</th>
                                    <td>{{ $bom->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @if($bom->approved_by)
                                    <tr>
                                        <th>Approved By:</th>
                                        <td>{{ $bom->approvedBy->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved On:</th>
                                        <td>{{ $bom->approved_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($bom->description)
                        <div class="mt-3">
                            <h6>Description:</h6>
                            <div class="alert alert-light">
                                {{ $bom->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Components Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Components</h5>
                </div>
                <div class="card-body">
                    @if($bom->assembly && $bom->assembly->items->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Unit Cost</th>
                                        <th>Total Cost</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalCost = 0;
                                    @endphp
                                    @foreach($bom->assembly->items as $index => $item)
                                        @php
                                            $quantity = $item->pivot->quantity;
                                            $unitCost = $item->unit_cost;
                                            $itemTotal = $quantity * $unitCost;
                                            $totalCost += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('items.show', $item) }}" class="text-decoration-none">
                                                    {{ $item->item_code }}
                                                </a>
                                            </td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $quantity }}</td>
                                            <td>{{ $item->pivot->unit_of_measure }}</td>
                                            <td>₹{{ number_format($unitCost, 2) }}</td>
                                            <td>₹{{ number_format($itemTotal, 2) }}</td>
                                            <td>{{ $item->pivot->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-end">Total Cost:</th>
                                        <th colspan="2">₹{{ number_format($totalCost, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5>No Components</h5>
                            <p class="text-muted">This BOM doesn't have any components defined</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & History -->
        <div class="col-lg-4">
            <!-- Approval Actions -->
            @if($bom->status == 'pending_approval' && auth()->user()->canApproveBoms())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Approval Actions</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('boms.approve', $bom) }}" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-check"></i> Approve BOM
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times"></i> Reject BOM
                        </button>
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$checkout)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                <i class="fas fa-sign-out-alt"></i> Check Out BOM
                            </button>
                        @elseif($checkout->user_id == auth()->id())
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkinModal">
                                <i class="fas fa-sign-in-alt"></i> Check In BOM
                            </button>
                            <a href="{{ route('boms.edit', $bom) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit BOM
                            </a>
                        @endif
                        <a href="{{ route('boms.versions', $bom) }}" class="btn btn-info">
                            <i class="fas fa-history"></i> View Versions
                        </a>
                        <a href="{{ route('reports.export') }}?type=boms&id={{ $bom->id }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Export BOM
                        </a>
                    </div>
                </div>
            </div>

            <!-- Version History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Versions</h5>
                </div>
                <div class="card-body">
                    @if($bom->versions->isEmpty())
                        <p class="text-muted text-center">No version history</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($bom->versions->take(5) as $version)
                                <div class="list-group-item px-0">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Version {{ $version->version }}</h6>
                                        <small class="text-muted">{{ $version->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small">{{ Str::limit($version->changes_description, 50) }}</p>
                                    <small class="text-muted">By {{ $version->createdBy->name ?? 'N/A' }}</small>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('boms.versions', $bom) }}" class="btn btn-sm btn-outline-primary">
                                View All Versions
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('boms.checkout', $bom) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check Out BOM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <textarea name="purpose" class="form-control" rows="3" 
                                  placeholder="Why are you checking out this BOM?"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Checking out prevents others from editing this BOM while you work on it.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Check Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Checkin Modal -->
<div class="modal fade" id="checkinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('boms.checkin', $bom) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check In BOM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Are you sure you want to check in this BOM? This will allow others to check it out.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Check In</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('boms.reject', $bom) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject BOM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required 
                                  placeholder="Please provide a reason for rejecting this BOM..."></textarea>
                    </div>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Rejecting this BOM will require it to be revised and resubmitted for approval.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject BOM</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection