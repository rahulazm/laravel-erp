@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Export Configuration</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exports.index') }}">Exports</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Export Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('exports.update', $export->id) }}" method="POST" id="export-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Export Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name', $export->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model_type">Data Source *</label>
                                    <select class="form-control @error('model_type') is-invalid @enderror" 
                                            id="model_type" name="model_type" required
                                            onchange="updateColumns()">
                                        <option value="">Select Data Source</option>
                                        <option value="App\Models\Inventory" {{ old('model_type', $export->model_type) == 'App\Models\Inventory' ? 'selected' : '' }}>Inventory</option>
                                        <option value="App\Models\SalesOrder" {{ old('model_type', $export->model_type) == 'App\Models\SalesOrder' ? 'selected' : '' }}>Sales Orders</option>
                                        <option value="App\Models\PurchaseOrder" {{ old('model_type', $export->model_type) == 'App\Models\PurchaseOrder' ? 'selected' : '' }}>Purchase Orders</option>
                                        <option value="App\Models\Customer" {{ old('model_type', $export->model_type) == 'App\Models\Customer' ? 'selected' : '' }}>Customers</option>
                                        <option value="App\Models\Supplier" {{ old('model_type', $export->model_type) == 'App\Models\Supplier' ? 'selected' : '' }}>Suppliers</option>
                                        <option value="App\Models\Product" {{ old('model_type', $export->model_type) == 'App\Models\Product' ? 'selected' : '' }}>Products</option>
                                        <option value="App\Models\Employee" {{ old('model_type', $export->model_type) == 'App\Models\Employee' ? 'selected' : '' }}>Employees</option>
                                        <option value="custom" {{ old('model_type', $export->model_type) == 'custom' ? 'selected' : '' }}>Custom Query</option>
                                    </select>
                                    @error('model_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2">{{ old('description', $export->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div id="custom-query-section" style="{{ old('model_type', $export->model_type) == 'custom' ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="custom_query">Custom SQL Query</label>
                                <textarea class="form-control @error('custom_query') is-invalid @enderror" 
                                          id="custom_query" name="custom_query" rows="5">{{ old('custom_query', $export->custom_query) }}</textarea>
                                @error('custom_query')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Write your SQL query here. Use parameter placeholders like :start_date, :end_date for dynamic filtering.
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="export_format">Export Format *</label>
                                    <select class="form-control @error('export_format') is-invalid @enderror" 
                                            id="export_format" name="export_format" required>
                                        <option value="csv" {{ old('export_format', $export->export_format) == 'csv' ? 'selected' : '' }}>CSV</option>
                                        <option value="excel" {{ old('export_format', $export->export_format) == 'excel' ? 'selected' : '' }}>Excel (XLSX)</option>
                                        <option value="pdf" {{ old('export_format', $export->export_format) == 'pdf' ? 'selected' : '' }}>PDF</option>
                                        <option value="json" {{ old('export_format', $export->export_format) == 'json' ? 'selected' : '' }}>JSON</option>
                                        <option value="xml" {{ old('export_format', $export->export_format) == 'xml' ? 'selected' : '' }}>XML</option>
                                    </select>
                                    @error('export_format')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delimiter">CSV Delimiter</label>
                                    <select class="form-control @error('delimiter') is-invalid @enderror" 
                                            id="delimiter" name="delimiter">
                                        <option value="," {{ old('delimiter', $export->delimiter) == ',' ? 'selected' : '' }}>Comma (,)</option>
                                        <option value=";" {{ old('delimiter', $export->delimiter) == ';' ? 'selected' : '' }}>Semicolon (;)</option>
                                        <option value="|" {{ old('delimiter', $export->delimiter) == '|' ? 'selected' : '' }}>Pipe (|)</option>
                                        <option value="tab" {{ old('delimiter', $export->delimiter) == 'tab' ? 'selected' : '' }}>Tab</option>
                                    </select>
                                    @error('delimiter')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="columns">Columns to Export *</label>
                            <div class="border p-3 rounded bg-light">
                                <div class="row">
                                    @php
                                        $selectedColumns = old('columns', json_decode($export->columns, true) ?? []);
                                        $allColumns = $availableColumns ?? [];
                                    @endphp
                                    @foreach($allColumns as $column => $label)
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input column-checkbox" 
                                                   name="columns[]" value="{{ $column }}" 
                                                   id="column_{{ $loop->index }}"
                                                   {{ in_array($column, $selectedColumns) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="column_{{ $loop->index }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="selectAllColumns()">Select All</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="deselectAllColumns()">Deselect All</button>
                                </div>
                            </div>
                            @error('columns')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="filters">Filters (JSON)</label>
                            <textarea class="form-control @error('filters') is-invalid @enderror" 
                                      id="filters" name="filters" rows="4">{{ old('filters', json_encode($export->filters, JSON_PRETTY_PRINT)) }}</textarea>
                            @error('filters')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">
                                Example: {"status": "active", "category_id": [1,2,3]}
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_by">Sort By</label>
                                    <input type="text" class="form-control @error('sort_by') is-invalid @enderror" 
                                           id="sort_by" name="sort_by" 
                                           value="{{ old('sort_by', $export->sort_by) }}"
                                           placeholder="e.g., created_at">
                                    @error('sort_by')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order">Sort Order</label>
                                    <select class="form-control @error('sort_order') is-invalid @enderror" 
                                            id="sort_order" name="sort_order">
                                        <option value="asc" {{ old('sort_order', $export->sort_order) == 'asc' ? 'selected' : '' }}>Ascending</option>
                                        <option value="desc" {{ old('sort_order', $export->sort_order) == 'desc' ? 'selected' : '' }}>Descending</option>
                                    </select>
                                    @error('sort_order')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="limit">Row Limit</label>
                                    <input type="number" class="form-control @error('limit') is-invalid @enderror" 
                                           id="limit" name="limit" min="0"
                                           value="{{ old('limit', $export->limit) }}"
                                           placeholder="0 for no limit">
                                    @error('limit')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="encoding">File Encoding</label>
                                    <select class="form-control @error('encoding') is-invalid @enderror" 
                                            id="encoding" name="encoding">
                                        <option value="UTF-8" {{ old('encoding', $export->encoding) == 'UTF-8' ? 'selected' : '' }}>UTF-8</option>
                                        <option value="UTF-16" {{ old('encoding', $export->encoding) == 'UTF-16' ? 'selected' : '' }}>UTF-16</option>
                                        <option value="ASCII" {{ old('encoding', $export->encoding) == 'ASCII' ? 'selected' : '' }}>ASCII</option>
                                        <option value="ISO-8859-1" {{ old('encoding', $export->encoding) == 'ISO-8859-1' ? 'selected' : '' }}>ISO-8859-1</option>
                                    </select>
                                    @error('encoding')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="include_headers" name="include_headers" 
                                               value="1" {{ old('include_headers', $export->include_headers) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_headers">Include Headers</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_scheduled" name="is_scheduled" 
                                               value="1" {{ old('is_scheduled', $export->is_scheduled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_scheduled">Schedule Export</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="compress" name="compress" 
                                               value="1" {{ old('compress', $export->compress) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="compress">Compress (ZIP)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row schedule-fields" style="{{ old('is_scheduled', $export->is_scheduled) ? '' : 'display: none;' }}">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="schedule_frequency">Frequency</label>
                                    <select class="form-control @error('schedule_frequency') is-invalid @enderror" 
                                            id="schedule_frequency" name="schedule_frequency">
                                        <option value="daily" {{ old('schedule_frequency', $export->schedule_frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('schedule_frequency', $export->schedule_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('schedule_frequency', $export->schedule_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    </select>
                                    @error('schedule_frequency')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="schedule_time">Time</label>
                                    <input type="time" class="form-control @error('schedule_time') is-invalid @enderror" 
                                           id="schedule_time" name="schedule_time" 
                                           value="{{ old('schedule_time', $export->schedule_time) }}">
                                    @error('schedule_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="recipients">Recipients</label>
                                    <input type="text" class="form-control @error('recipients') is-invalid @enderror" 
                                           id="recipients" name="recipients" 
                                           value="{{ old('recipients', $export->recipients) }}"
                                           placeholder="Comma-separated emails">
                                    @error('recipients')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $export->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Export
                            </button>
                            <a href="{{ route('exports.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-info" onclick="previewExport()">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Export Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Export ID:</dt>
                        <dd class="col-sm-7">{{ $export->id }}</dd>
                        
                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $export->creator->name ?? 'System' }}</dd>
                        
                        <dt class="col-sm-5">Created At:</dt>
                        <dd class="col-sm-7">{{ $export->created_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Last Run:</dt>
                        <dd class="col-sm-7">
                            @if($export->last_run_at)
                                {{ $export->last_run_at->format('d M Y, H:i') }}
                            @else
                                Never
                            @endif
                        </dd>
                        
                        <dt class="col-sm-5">Run Count:</dt>
                        <dd class="col-sm-7">{{ $export->run_count }}</dd>
                        
                        <dt class="col-sm-5">Last File Size:</dt>
                        <dd class="col-sm-7">
                            @if($export->last_file_size)
                                {{ format_bytes($export->last_file_size) }}
                            @else
                                N/A
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('exports.run', $export->id) }}" 
                           class="btn btn-success mb-2" onclick="return confirm('Run this export now?');">
                            <i class="fas fa-play"></i> Run Export Now
                        </a>
                        
                        <a href="{{ route('exports.download', $export->id) }}" 
                           class="btn btn-primary mb-2">
                            <i class="fas fa-download"></i> Download Last Export
                        </a>
                        
                        <button type="button" class="btn btn-warning mb-2" onclick="duplicateExport()">
                            <i class="fas fa-copy"></i> Duplicate Export
                        </button>
                        
                        <form action="{{ route('exports.destroy', $export->id) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Delete this export configuration? This action cannot be undone.');">
                                <i class="fas fa-trash"></i> Delete Export
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Recent Exports</h5>
                </div>
                <div class="card-body">
                    @if($export->exportLogs->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($export->exportLogs->take(5) as $log)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <small>{{ $log->created_at->format('d M H:i') }}</small><br>
                                    <span class="badge bg-{{ $log->status == 'success' ? 'success' : 'danger' }}">
                                        {{ $log->status }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <small>{{ format_bytes($log->file_size) }}</small><br>
                                    <a href="{{ route('exports.logs.download', $log->id) }}" class="btn btn-sm btn-link p-0">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @if($export->exportLogs->count() > 5)
                            <a href="{{ route('exports.logs', $export->id) }}" class="btn btn-sm btn-link w-100 mt-2">
                                View All Logs
                            </a>
                        @endif
                    @else
                        <p class="text-muted mb-0">No export history</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('model_type').addEventListener('change', function() {
    const customQuerySection = document.getElementById('custom-query-section');
    customQuerySection.style.display = this.value === 'custom' ? 'block' : 'none';
});

document.getElementById('is_scheduled').addEventListener('change', function() {
    document.querySelector('.schedule-fields').style.display = this.checked ? 'block' : 'none';
});

function selectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllColumns() {
    document.querySelectorAll('.column-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function previewExport() {
    const form = document.getElementById('export-form');
    const originalAction = form.action;
    form.action = "{{ route('exports.preview', $export->id) }}";
    form.target = '_blank';
    form.submit();
    form.action = originalAction;
    form.target = '_self';
}

function duplicateExport() {
    if(confirm('Duplicate this export configuration?')) {
        window.location.href = "{{ route('exports.duplicate', $export->id) }}";
    }
}

// Format JSON in filters field
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('filters');
    if(textarea.value) {
        try {
            const json = JSON.parse(textarea.value);
            textarea.value = JSON.stringify(json, null, 2);
        } catch(e) {
            // Keep original value
        }
    }
});
</script>
@endpush