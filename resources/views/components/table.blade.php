@props([
    'headers' => [],
    'data' => [],
    'emptyText' => 'No data available',
    'striped' => true,
    'hover' => true,
    'bordered' => false,
    'small' => false,
    'responsive' => true,
])

@php
    $tableClass = 'table';
    
    if ($striped) {
        $tableClass .= ' table-striped';
    }
    
    if ($hover) {
        $tableClass .= ' table-hover';
    }
    
    if ($bordered) {
        $tableClass .= ' table-bordered';
    }
    
    if ($small) {
        $tableClass .= ' table-sm';
    }
@endphp

@if($responsive)
    <div class="table-responsive">
@endif

<table {{ $attributes->merge(['class' => $tableClass]) }}>
    @if(!empty($headers))
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
    @endif
    
    <tbody>
        @forelse($data as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($headers) }}" class="text-center py-4">
                    <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-0">{{ $emptyText }}</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@if($responsive)
    </div>
@endif