@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Obsolete Item</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('obsolete-items.index') }}">Obsolete Items</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Item Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('obsolete-items.update', $obsoleteItem->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_code">Item Code *</label>
                                    <input type="text" class="form-control @error('item_code') is-invalid @enderror" 
                                           id="item_code" name="item_code" 
                                           value="{{ old('item_code', $obsoleteItem->item_code) }}" required>
                                    @error('item_code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Item Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name', $obsoleteItem->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Category</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $obsoleteItem->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" min="0"
                                           value="{{ old('quantity', $obsoleteItem->quantity) }}">
                                    @error('quantity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_of_measure">Unit of Measure</label>
                                    <input type="text" class="form-control @error('unit_of_measure') is-invalid @enderror" 
                                           id="unit_of_measure" name="unit_of_measure" 
                                           value="{{ old('unit_of_measure', $obsoleteItem->unit_of_measure) }}">
                                    @error('unit_of_measure')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason for Obsolescence *</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="3" required>{{ old('reason', $obsoleteItem->reason) }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="obsolete_date">Obsolete Date *</label>
                                    <input type="date" class="form-control @error('obsolete_date') is-invalid @enderror" 
                                           id="obsolete_date" name="obsolete_date" 
                                           value="{{ old('obsolete_date', $obsoleteItem->obsolete_date->format('Y-m-d')) }}" required>
                                    @error('obsolete_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="disposal_method">Disposal Method</label>
                                    <select class="form-control @error('disposal_method') is-invalid @enderror" 
                                            id="disposal_method" name="disposal_method">
                                        <option value="">Select Method</option>
                                        <option value="recycle" {{ old('disposal_method', $obsoleteItem->disposal_method) == 'recycle' ? 'selected' : '' }}>Recycle</option>
                                        <option value="destroy" {{ old('disposal_method', $obsoleteItem->disposal_method) == 'destroy' ? 'selected' : '' }}>Destroy</option>
                                        <option value="donate" {{ old('disposal_method', $obsoleteItem->disposal_method) == 'donate' ? 'selected' : '' }}>Donate</option>
                                        <option value="sell" {{ old('disposal_method', $obsoleteItem->disposal_method) == 'sell' ? 'selected' : '' }}>Sell</option>
                                    </select>
                                    @error('disposal_method')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $obsoleteItem->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="pending" {{ old('status', $obsoleteItem->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $obsoleteItem->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="disposed" {{ old('status', $obsoleteItem->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                                <option value="cancelled" {{ old('status', $obsoleteItem->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="attachments">Attachments</label>
                            <input type="file" class="form-control-file @error('attachments') is-invalid @enderror" 
                                   id="attachments" name="attachments[]" multiple>
                            @error('attachments')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if($obsoleteItem->attachments->count() > 0)
                                <div class="mt-2">
                                    <small>Current attachments:</small>
                                    <ul>
                                        @foreach($obsoleteItem->attachments as $attachment)
                                            <li>
                                                <a href="{{ Storage::url($attachment->path) }}" target="_blank">
                                                    {{ $attachment->original_name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Obsolete Item
                            </button>
                            <a href="{{ route('obsolete-items.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Item Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $obsoleteItem->creator->name ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-5">Created At:</dt>
                        <dd class="col-sm-7">{{ $obsoleteItem->created_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ $obsoleteItem->updated_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Original Cost:</dt>
                        <dd class="col-sm-7">{{ $obsoleteItem->original_cost ? format_currency($obsoleteItem->original_cost) : 'N/A' }}</dd>
                        
                        <dt class="col-sm-5">Current Value:</dt>
                        <dd class="col-sm-7">{{ $obsoleteItem->current_value ? format_currency($obsoleteItem->current_value) : 'N/A' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical w-100">
                        <a href="{{ route('obsolete-items.show', $obsoleteItem->id) }}" 
                           class="btn btn-info mb-2">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <button type="button" class="btn btn-warning mb-2" onclick="duplicateItem()">
                            <i class="fas fa-copy"></i> Duplicate
                        </button>
                        <form action="{{ route('obsolete-items.destroy', $obsoleteItem->id) }}" 
                              method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this obsolete item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function duplicateItem() {
    if(confirm('Duplicate this obsolete item?')) {
        window.location.href = "{{ route('obsolete-items.duplicate', $obsoleteItem->id) }}";
    }
}
</script>
@endpush