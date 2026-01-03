@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Search Results for "{{ request('query') }}"</h5>
                </div>
                <div class="card-body">
                    @if($results->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>No results found</h4>
                            <p class="text-muted">Try different search terms</p>
                        </div>
                    @else
                        <div class="row">
                            @foreach($results as $result)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-primary">{{ $result['type'] }}</span>
                                                <small class="text-muted">{{ $result['created_at']->diffForHumans() }}</small>
                                            </div>
                                            <h6 class="card-title">{{ $result['name'] ?? $result['order_number'] ?? $result['item_code'] }}</h6>
                                            @if(isset($result['customer_name']))
                                                <p class="card-text text-muted small">{{ $result['customer_name'] }}</p>
                                            @endif
                                            @if(isset($result['description']))
                                                <p class="card-text small">{{ Str::limit($result['description'], 100) }}</p>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ $result['route'] }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection