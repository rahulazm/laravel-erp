@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Report</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Report Configuration</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.update', $report->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Report Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name', $report->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Report Type *</label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="inventory" {{ old('type', $report->type) == 'inventory' ? 'selected' : '' }}>Inventory</option>
                                        <option value="sales" {{ old('type', $report->type) == 'sales' ? 'selected' : '' }}>Sales</option>
                                        <option value="purchase" {{ old('type', $report->type) == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                        <option value="financial" {{ old('type', $report->type) == 'financial' ? 'selected' : '' }}>Financial</option>
                                        <option value="custom" {{ old('type', $report->type) == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $report->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequency">Frequency</label>
                                    <select class="form-control @error('frequency') is-invalid @enderror" 
                                            id="frequency" name="frequency">
                                        <option value="once" {{ old('frequency', $report->frequency) == 'once' ? 'selected' : '' }}>One Time</option>
                                        <option value="daily" {{ old('frequency', $report->frequency) == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('frequency', $report->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('frequency', $report->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="quarterly" {{ old('frequency', $report->frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="yearly" {{ old('frequency', $report->frequency) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                    @error('frequency')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="format">Output Format</label>
                                    <select class="form-control @error('format') is-invalid @enderror" 
                                            id="format" name="format" multiple>
                                        <option value="pdf" {{ in_array('pdf', old('format', explode(',', $report->format))) ? 'selected' : '' }}>PDF</option>
                                        <option value="excel" {{ in_array('excel', old('format', explode(',', $report->format))) ? 'selected' : '' }}>Excel</option>
                                        <option value="csv" {{ in_array('csv', old('format', explode(',', $report->format))) ? 'selected' : '' }}>CSV</option>
                                        <option value="html" {{ in_array('html', old('format', explode(',', $report->format))) ? 'selected' : '' }}>HTML</option>
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple formats</small>
                                    @error('format')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="parameters">Report Parameters (JSON)</label>
                            <textarea class="form-control @error('parameters') is-invalid @enderror" 
                                      id="parameters" name="parameters" rows="6">{{ old('parameters', json_encode($report->parameters, JSON_PRETTY_PRINT)) }}</textarea>
                            @error('parameters')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_scheduled" name="is_scheduled" 
                                               value="1" {{ old('is_scheduled', $report->is_scheduled) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_scheduled">Schedule this report</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_public" name="is_public" 
                                               value="1" {{ old('is_public', $report->is_public) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_public">Make report public</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row schedule-fields" style="{{ old('is_scheduled', $report->is_scheduled) ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_time">Schedule Time</label>
                                    <input type="time" class="form-control @error('schedule_time') is-invalid @enderror" 
                                           id="schedule_time" name="schedule_time" 
                                           value="{{ old('schedule_time', $report->schedule_time) }}">
                                    @error('schedule_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="recipients">Recipients (comma-separated emails)</label>
                                    <input type="text" class="form-control @error('recipients') is-invalid @enderror" 
                                           id="recipients" name="recipients" 
                                           value="{{ old('recipients', $report->recipients) }}">
                                    @error('recipients')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $report->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Report
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="{{ route('reports.preview', $report->id) }}" 
                               class="btn btn-info" target="_blank">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Report Info</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Report ID:</dt>
                        <dd class="col-sm-6">{{ $report->id }}</dd>
                        
                        <dt class="col-sm-6">Created By:</dt>
                        <dd class="col-sm-6">{{ $report->creator->name ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-6">Created At:</dt>
                        <dd class="col-sm-6">{{ $report->created_at->format('d M Y') }}</dd>
                        
                        <dt class="col-sm-6">Last Run:</dt>
                        <dd class="col-sm-6">
                            @if($report->last_run_at)
                                {{ $report->last_run_at->format('d M Y, H:i') }}
                            @else
                                Never
                            @endif
                        </dd>
                        
                        <dt class="col-sm-6">Run Count:</dt>
                        <dd class="col-sm-6">{{ $report->run_count }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.run', $report->id) }}" 
                           class="btn btn-success mb-2">
                            <i class="fas fa-play"></i> Run Now
                        </a>
                        <a href="{{ route('reports.show', $report->id) }}" 
                           class="btn btn-info mb-2">
                            <i class="fas fa-chart-bar"></i> View Results
                        </a>
                        <button type="button" class="btn btn-warning mb-2" onclick="duplicateReport()">
                            <i class="fas fa-copy"></i> Duplicate
                        </button>
                        <form action="{{ route('reports.destroy', $report->id) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" 
                                    onclick="return confirm('Delete this report?')">
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
document.getElementById('is_scheduled').addEventListener('change', function() {
    document.querySelector('.schedule-fields').style.display = this.checked ? 'block' : 'none';
});

function duplicateReport() {
    if(confirm('Duplicate this report configuration?')) {
        window.location.href = "{{ route('reports.duplicate', $report->id) }}";
    }
}

// Initialize JSON editor for parameters
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('parameters');
    try {
        const json = JSON.parse(textarea.value);
        textarea.value = JSON.stringify(json, null, 2);
    } catch(e) {
        // Keep original value if not valid JSON
    }
});
</script>
@endpush