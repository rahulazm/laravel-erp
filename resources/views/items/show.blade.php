@extends('layouts.app')

@section('title', 'Item - ' . $item->item_code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">{{ $item->item_code }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Item: {{ $item->item_code }}</h1>
                    <p class="text-muted mb-0">{{ $item->name }} • Version {{ $item->version }}</p>
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
                    @can('update', $item)
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endcan
                    @if(!$item->is_obsolete)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#obsoleteModal">
                            <i class="fas fa-trash"></i> Mark Obsolete
                        </button>
                    @else
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#restoreModal">
                            <i class="fas fa-undo"></i> Restore
                        </button>
                    @endif
                    <a href="{{ route('items.versions', $item) }}" class="btn btn-info">
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
                                    @if($item->is_obsolete)
                                        <span class="badge bg-warning fs-6 px-3 py-2">
                                            <i class="fas fa-trash"></i> Obsolete
                                        </span>
                                    @else
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fas fa-check"></i> Active
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <small class="text-muted d-block">Version</small>
                                    <strong class="fs-5">v{{ $item->version }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Type</small>
                            <span class="badge bg-info">
                                {{ ucfirst(str_replace('_', ' ', $item->type)) }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Unit Cost</small>
                            <strong class="fs-5">₹{{ number_format($item->unit_cost, 2) }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Created By</small>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <strong>{{ $item->createdBy->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $item->created_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($checkout)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            This item is checked out by {{ $checkout->user->name }} since 
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
        <!-- Left Column - Item Details -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Item Code:</th>
                                    <td>{{ $item->item_code }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $item->name }}</td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst(str_replace('_', ' ', $item->type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unit of Measure:</th>
                                    <td>{{ $item->unit_of_measure }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Unit Cost:</th>
                                    <td class="fs-4 fw-bold text-primary">₹{{ number_format($item->unit_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Weight:</th>
                                    <td>{{ $item->weight ? $item->weight . ' kg' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Material Grade:</th>
                                    <td>{{ $item->material_grade ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($item->is_obsolete)
                                            <span class="badge bg-warning">Obsolete</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($item->description)
                        <div class="mt-3">
                            <h6>Description:</h6>
                            <div class="alert alert-light">
                                {{ $item->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Specifications -->
            @if($item->specifications)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Specifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $specs = json_decode($item->specifications, true);
                            @endphp
                            @if($specs && is_array($specs))
                                @foreach($specs as $key => $value)
                                    <div class="col-md-6 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <small class="text-muted d-block">{{ $key }}</small>
                                                <strong>{{ $value }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-list fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No specifications defined</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- BOM Usage -->
            @if($bomUsage->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">BOM Usage</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>BOM #</th>
                                        <th>BOM Name</th>
                                        <th>Quantity</th>
                                        <th>Assembly</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bomUsage as $usage)
                                        <tr>
                                            <td>
                                                <a href="{{ route('boms.show', $usage) }}" class="text-decoration-none">
                                                    {{ $usage->bom_number }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($usage->name, 30) }}</td>
                                            <td>{{ $usage->quantity }}</td>
                                            <td>{{ $usage->assembly->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-badge bg-status-{{ $usage->status }}">
                                                    {{ ucfirst($usage->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('boms.show', $usage) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Actions & History -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$checkout)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                <i class="fas fa-sign-out-alt"></i> Check Out Item
                            </button>
                        @elseif($checkout->user_id == auth()->id())
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#checkinModal">
                                <i class="fas fa-sign-in-alt"></i> Check In Item
                            </button>
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Item
                            </a>
                        @endif
                        <a href="{{ route('items.versions', $item) }}" class="btn btn-info">
                            <i class="fas fa-history"></i> View Versions
                        </a>
                        <a href="{{ route('reports.export') }}?type=items&id={{ $item->id }}" class="btn btn-secondary">
                            <i class="fas fa-download"></i> Export Details
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
                    @if($recentVersions->isEmpty())
                        <p class="text-muted text-center">No version history</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentVersions as $version)
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
                            <a href="{{ route('items.versions', $item) }}" class="btn btn-sm btn-outline-primary">
                                View All Versions
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cost History -->
            @if($item->versions->count() > 1)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Cost History</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($item->versions->sortByDesc('version')->take(5) as $version)
                                @php
                                    $versionData = json_decode($version->data, true);
                                    $cost = $versionData['unit_cost'] ?? null;
                                @endphp
                                @if($cost !== null)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <strong>v{{ $version->version }}</strong>
                                            <span class="text-primary">₹{{ number_format($cost, 2) }}</span>
                                        </div>
                                        <small class="text-muted">{{ $version->created_at->format('M d, Y') }}</small>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('items.checkout', $item) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check Out Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <textarea name="purpose" class="form-control" rows="3" 
                                  placeholder="Why are you checking out this item?"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Checking out prevents others from editing this item while you work on it.
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
            <form method="POST" action="{{ route('items.checkin', $item) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Check In Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Are you sure you want to check in this item? This will allow others to check it out.
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

<!-- Obsolete Modal -->
<div class="modal fade" id="obsoleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('items.obsolete', $item) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Item as Obsolete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Reason for Obsolete</label>
                        <textarea name="obsolete_reason" class="form-control" rows="4" required 
                                  placeholder="Why is this item being marked as obsolete?"></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        This item will be marked as obsolete and will no longer be available for new BOMs.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Mark as Obsolete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('items.restore', $item) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Restore Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Reason for Restore</label>
                        <textarea name="restore_reason" class="form-control" rows="4" required 
                                  placeholder="Why is this item being restored?"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This item will be restored and will be available for new BOMs.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Restore Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection