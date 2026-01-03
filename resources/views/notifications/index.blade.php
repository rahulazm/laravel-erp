@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Notifications</h1>
                <div class="btn-group">
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </button>
                    </form>
                    <a href="{{ route('notifications.settings') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Total</h6>
                            <h3>{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-bell fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Unread</h6>
                            <h3>{{ $stats['unread'] }}</h3>
                        </div>
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Read</h6>
                            <h3>{{ $stats['read'] }}</h3>
                        </div>
                        <i class="fas fa-envelope-open fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">Actionable</h6>
                            <h3>{{ $actionableCount }}</h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('notifications.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type->type }}" {{ request('type') == $type->type ? 'selected' : '' }}>
                                    {{ ucfirst($type->type) }} ({{ $type->count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="read_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Unread Only</option>
                            <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Read Only</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5>No Notifications</h5>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ $notification->is_unread ? 'bg-light' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1 me-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas {{ $notification->type_icon }} fa-2x text-{{ $notification->priority_class }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $notification->title }}</h5>
                                            <p class="mb-2">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                {{ $notification->created_at->format('M d, Y H:i') }}
                                                @if($notification->related_type && $notification->related_id)
                                                    • 
                                                    @php
                                                        $model = 'App\\Models\\' . ucfirst($notification->related_type);
                                                        $related = $model::find($notification->related_id);
                                                    @endphp
                                                    @if($related)
                                                        Related to: 
                                                        @if($notification->related_type == 'sales_order')
                                                            {{ $related->order_number }}
                                                        @elseif($notification->related_type == 'bom')
                                                            {{ $related->bom_number }}
                                                        @elseif($notification->related_type == 'item')
                                                            {{ $related->item_code }}
                                                        @endif
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge bg-{{ $notification->priority_class }} mb-2">
                                        {{ ucfirst($notification->priority) }}
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        @if($notification->is_unread)
                                            <form method="POST" action="{{ route('notifications.update', $notification) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="read">
                                                <button type="submit" class="btn btn-outline-success" title="Mark as Read">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('notifications.update', $notification) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="unread">
                                                <button type="submit" class="btn btn-outline-warning" title="Mark as Unread">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($notification->action_url)
                                            <a href="{{ $notification->action_url }}" class="btn btn-outline-primary" title="Take Action">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('notifications.update', $notification) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Delete this notification?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} entries
                        </div>
                        <nav>
                            {{ $notifications->withQueryString()->links() }}
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection