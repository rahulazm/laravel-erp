@extends('layouts.app')

@section('title', 'Items')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Items</h1>
                <div>
                    <a href="{{ route('items.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Item
                    </a>
                    <a href="{{ route('reports.export') }}?type=items" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('items.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($itemTypes as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="obsolete" {{ request('status') == 'obsolete' ? 'selected' : '' }}>Obsolete</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Material Grade</label>
                        <select name="material_grade" class="form-select">
                            <option value="">All Grades</option>
                            @foreach($materialGrades as $grade)
                                <option value="{{ $grade }}" {{ request('material_grade') == $grade ? 'selected' : '' }}>
                                    {{ $grade }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total Items</h6>
                            <h3>{{ $totalItems }}</h3>
                        </div>
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Active Items</h6>
                            <h3>{{ $activeItems }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Obsolete Items</h6>
                            <h3>{{ $obsoleteItems }}</h3>
                        </div>
                        <i class="fas fa-trash-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Unit of Measure</th>
                            <th>Unit Cost</th>
                            <th>Material Grade</th>
                            <th>Version</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('items.show', $item) }}" class="text-decoration-none fw-bold">
                                        {{ $item->item_code }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($item->name, 30) }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst(str_replace('_', ' ', $item->type)) }}
                                    </span>
                                </td>
                                <td>{{ $item->unit_of_measure }}</td>
                                <td>₹{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ $item->material_grade ?? '-' }}</td>
                                <td>v{{ $item->version }}</td>
                                <td>
                                    @if($item->is_obsolete)
                                        <span class="badge bg-warning">Obsolete</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $item)
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        @if(!$item->is_obsolete)
                                        <button type="button" class="btn btn-sm btn-danger" title="Mark Obsolete"
                                                data-bs-toggle="modal" data-bs-target="#obsoleteModal{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-success" title="Restore"
                                                data-bs-toggle="modal" data-bs-target="#restoreModal{{ $item->id }}">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Obsolete Modal -->
                            <div class="modal fade" id="obsoleteModal{{ $item->id }}" tabindex="-1">
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
                            <div class="modal fade" id="restoreModal{{ $item->id }}" tabindex="-1">
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
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5>No items found</h5>
                                    <p class="text-muted">Create your first item to get started</p>
                                    <a href="{{ route('items.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Item
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($items->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} entries
                    </div>
                    <nav>
                        {{ $items->withQueryString()->links() }}
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
        order: [[0, 'asc']],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>tip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search items..."
        }
    });
});
</script>
@endpush