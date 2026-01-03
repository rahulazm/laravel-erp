@extends('layouts.app')

@section('title', 'Bill of Materials (BOMs)')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Bill of Materials</h1>
                <div>
                    <a href="{{ route('boms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create BOM
                    </a>
                    <a href="{{ route('reports.export') }}?type=boms" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('boms.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="obsolete" {{ request('status') == 'obsolete' ? 'selected' : '' }}>Obsolete</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Assembly</label>
                        <select name="assembly_id" class="form-select">
                            <option value="">All Assemblies</option>
                            @foreach($assemblies as $assembly)
                                <option value="{{ $assembly->id }}" {{ request('assembly_id') == $assembly->id ? 'selected' : '' }}>
                                    {{ $assembly->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search BOMs..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('boms.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- BOMs Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>BOM #</th>
                            <th>Name</th>
                            <th>Assembly</th>
                            <th>Sales Order</th>
                            <th>Status</th>
                            <th>Version</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($boms as $bom)
                            <tr>
                                <td>
                                    <a href="{{ route('boms.show', $bom) }}" class="text-decoration-none fw-bold">
                                        {{ $bom->bom_number }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($bom->name, 30) }}</td>
                                <td>{{ $bom->assembly->name ?? 'N/A' }}</td>
                                <td>
                                    @if($bom->salesOrder)
                                        <a href="{{ route('sales-orders.show', $bom->salesOrder) }}" class="text-decoration-none">
                                            {{ $bom->salesOrder->order_number }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge bg-status-{{ $bom->status }}">
                                        {{ ucfirst($bom->status) }}
                                    </span>
                                </td>
                                <td>v{{ $bom->version }}</td>
                                <td>{{ $bom->createdBy->name ?? 'N/A' }}</td>
                                <td>{{ $bom->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('boms.show', $bom) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $bom)
                                        <a href="{{ route('boms.edit', $bom) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @if($bom->status == 'pending_approval' && auth()->user()->canApproveBoms())
                                        <a href="{{ route('boms.approve', $bom) }}" class="btn btn-sm btn-success" title="Approve"
                                           onclick="return confirm('Approve this BOM?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
                                    <h5>No BOMs found</h5>
                                    <p class="text-muted">Create your first BOM to get started</p>
                                    <a href="{{ route('boms.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create BOM
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($boms->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $boms->firstItem() }} to {{ $boms->lastItem() }} of {{ $boms->total() }} entries
                    </div>
                    <nav>
                        {{ $boms->withQueryString()->links() }}
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
            searchPlaceholder: "Search BOMs..."
        }
    });
});
</script>
@endpush