@extends('layouts.app')

@section('title', 'Valve Categories')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Valve Categories</h1>
                <div>
                    <a href="{{ route('valve-categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Category
                    </a>
                    <a href="{{ route('reports.export') }}?type=valve_categories" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('valve-categories.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="active_only" id="activeOnly" value="1" {{ request('active_only') ? 'checked' : '' }}>
                            <label class="form-check-label" for="activeOnly">Show Active Only</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @forelse($valveCategories as $category)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('valve-categories.show', $category) }}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </a>
                                    </li>
                                    @can('update', $category)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('valve-categories.edit', $category) }}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a>
                                    </li>
                                    @endcan
                                    @can('delete', $category)
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" 
                                           onclick="if(confirm('Delete this category?')) document.getElementById('delete-form-{{ $category->id }}').submit();">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </a>
                                        <form id="delete-form-{{ $category->id }}" 
                                              action="{{ route('valve-categories.destroy', $category) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </div>
                        
                        <h5 class="card-title">{{ $category->code }}</h5>
                        <h6 class="card-subtitle mb-3 text-muted">{{ $category->name }}</h6>
                        
                        <p class="card-text small text-muted">{{ Str::limit($category->description, 100) }}</p>
                        
                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <div class="text-center p-2 border rounded">
                                    <div class="fs-5 fw-bold">{{ $category->sales_orders_count }}</div>
                                    <small class="text-muted">Orders</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 border rounded">
                                    @if($category->material)
                                        <small class="text-muted d-block">Material</small>
                                        <small>{{ $category->material }}</small>
                                    @else
                                        <small class="text-muted">No Material</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            @if($category->pressure_rating)
                                <span class="badge bg-info me-1">{{ $category->pressure_rating }}</span>
                            @endif
                            @if($category->temperature_rating)
                                <span class="badge bg-warning me-1">{{ $category->temperature_rating }}</span>
                            @endif
                            @if($category->standard)
                                <span class="badge bg-primary">{{ $category->standard }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('valve-categories.show', $category) }}" class="btn btn-sm btn-outline-primary w-100">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                        <h5>No Valve Categories Found</h5>
                        <p class="text-muted">Create your first valve category to get started</p>
                        <a href="{{ route('valve-categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Category
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($valveCategories->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $valveCategories->firstItem() }} to {{ $valveCategories->lastItem() }} of {{ $valveCategories->total() }} entries
            </div>
            <nav>
                {{ $valveCategories->withQueryString()->links() }}
            </nav>
        </div>
    @endif
</div>
@endsection