@extends('layouts.app')

@section('title', 'BOM Versions - ' . $bom->bom_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('boms.index') }}">BOMs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('boms.show', $bom) }}">{{ $bom->bom_number }}</a></li>
                    <li class="breadcrumb-item active">Versions</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Version History: {{ $bom->bom_number }}</h1>
                    <p class="text-muted mb-0">{{ $bom->name }} • Current Version: v{{ $bom->version }}</p>
                </div>
                <a href="{{ route('boms.show', $bom) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to BOM
                </a>
            </div>
        </div>
    </div>

    <!-- Versions List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Versions</h5>
        </div>
        <div class="card-body">
            @if($versions->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5>No Version History</h5>
                    <p class="text-muted">This BOM doesn't have any previous versions</p>
                </div>
            @else
                <div class="timeline">
                    @foreach($versions as $version)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker {{ $version->version == $bom->version ? 'bg-success' : 'bg-primary' }}"></div>
                            <div class="timeline-content">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="mb-1">
                                                    Version {{ $version->version }}
                                                    @if($version->version == $bom->version)
                                                        <span class="badge bg-success">Current</span>
                                                    @endif
                                                </h5>
                                                <p class="text-muted mb-0">
                                                    Created {{ $version->created_at->diffForHumans() }} by {{ $version->createdBy->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                            @if($version->version != $bom->version)
                                                <form method="POST" action="{{ route('boms.restore-version', [$bom, $version->version]) }}" 
                                                      onsubmit="return confirm('Restore version {{ $version->version }}? This will create a new version from this data.')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-redo"></i> Restore
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        
                                        @if($version->changes_description)
                                            <div class="alert alert-light">
                                                <strong>Change Description:</strong><br>
                                                {{ $version->changes_description }}
                                            </div>
                                        @endif
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <h6>Data Snapshot:</h6>
                                                <div class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                                                    @php
                                                        $data = json_decode($version->data, true);
                                                    @endphp
                                                    @if($data && is_array($data))
                                                        <pre class="mb-0 small">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        <p class="text-muted mb-0">No data available</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Changes from Previous:</h6>
                                                @php
                                                    $changes = $version->compareWithPrevious();
                                                @endphp
                                                @if(empty($changes))
                                                    <p class="text-muted">No changes detected from previous version</p>
                                                @else
                                                    <ul class="list-unstyled">
                                                        @foreach($changes as $field => $change)
                                                            <li class="mb-1">
                                                                <strong>{{ $field }}:</strong>
                                                                <span class="text-danger">{{ $change['from'] ?? 'null' }}</span>
                                                                <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                                                <span class="text-success">{{ $change['to'] ?? 'null' }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    padding-left: 20px;
}
.timeline-marker {
    position: absolute;
    left: -6px;
    top: 20px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    padding-bottom: 20px;
}
</style>
@endsection