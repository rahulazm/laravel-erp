@extends('layouts.app')

@section('title', 'Assemblies')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Assemblies</h1>
                <div>
                    <a href="{{ route('assemblies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Assembly
                    </a>
                    <a href="{{ route('reports.export') }}?type=assemblies" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('assemblies.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="main" {{ request('type') == 'main' ? 'selected' : '' }}>Main Assembly</option>
                            <option value="sub" {{ request('type') == 'sub' ? 'selected' : '' }}>Sub Assembly</option>
                            <option value="component" {{ request('type') == 'component' ? 'selected' : '' }}>Component</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search assemblies..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active_only" id="activeOnly" value="1" {{ request('active_only') ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeOnly">Show Active Only</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assemblies Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Drawing #</th>
                            <th>Revision</th>
                            <th>Components</th>
                            <th>BOMs</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assemblies as $assembly)
                            <tr>
                                <td>
                                    <a href="{{ route('assemblies.show', $assembly) }}" class="text-decoration-none fw-bold">
                                        {{ $assembly->code }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($assembly->name, 30) }}</td>
                                <td>
                                    <span class="badge {{ $assembly->type == 'main' ? 'bg-primary' : ($assembly->type == 'sub' ? 'bg-warning' : 'bg-info') }}">
                                        {{ ucfirst($assembly->type) }}
                                    </span>
                                </td>
                                <td>{{ $assembly->parent->code ?? '-' }}</td>
                                <td>{{ $assembly->drawing_number ?? '-' }}</td>
                                <td>{{ $assembly->revision ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $assembly->component_count }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $assembly->boms_count }}</span>
                                </td>
                                <td>
                                    @if($assembly->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('assemblies.show', $assembly) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $assembly)
                                        <a href="{{ route('assemblies.edit', $assembly) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        <a href="{{ route('assemblies.export-bom', $assembly) }}" class="btn btn-sm btn-success" title="Export BOM">
                                            <i class="fas fa-file-export"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                                    <h5>No Assemblies Found</h5>
                                    <p class="text-muted">Create your first assembly to get started</p>
                                    <a href="{{ route('assemblies.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Assembly
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assemblies->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $assemblies->firstItem() }} to {{ $assemblies->lastItem() }} of {{ $assemblies->total() }} entries
                    </div>
                    <nav>
                        {{ $assemblies->withQueryString()->links() }}
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
            searchPlaceholder: "Search assemblies..."
        }
    });
});
</script>
@endpush