@props([
    'status' => null,
    'text' => null,
    'icon' => null,
])

@php
    $statusConfig = [
        'draft' => ['class' => 'bg-secondary', 'icon' => 'edit', 'label' => 'Draft'],
        'pending' => ['class' => 'bg-warning', 'icon' => 'clock', 'label' => 'Pending'],
        'confirmed' => ['class' => 'bg-info', 'icon' => 'check-circle', 'label' => 'Confirmed'],
        'in_production' => ['class' => 'bg-primary', 'icon' => 'industry', 'label' => 'In Production'],
        'completed' => ['class' => 'bg-success', 'icon' => 'check-double', 'label' => 'Completed'],
        'cancelled' => ['class' => 'bg-danger', 'icon' => 'times', 'label' => 'Cancelled'],
        'pending_approval' => ['class' => 'bg-warning', 'icon' => 'hourglass-half', 'label' => 'Pending Approval'],
        'approved' => ['class' => 'bg-success', 'icon' => 'check', 'label' => 'Approved'],
        'rejected' => ['class' => 'bg-danger', 'icon' => 'times', 'label' => 'Rejected'],
        'obsolete' => ['class' => 'bg-dark', 'icon' => 'trash', 'label' => 'Obsolete'],
        'active' => ['class' => 'bg-success', 'icon' => 'check-circle', 'label' => 'Active'],
        'inactive' => ['class' => 'bg-secondary', 'icon' => 'pause-circle', 'label' => 'Inactive'],
    ];
    
    $config = $statusConfig[$status] ?? ['class' => 'bg-secondary', 'icon' => 'circle', 'label' => $status];
    
    $badgeClass = 'badge ' . $config['class'];
    $badgeIcon = $icon ?? $config['icon'];
    $badgeText = $text ?? $config['label'];
@endphp

<span {{ $attributes->merge(['class' => $badgeClass]) }}>
    @if($badgeIcon)
        <i class="fas fa-{{ $badgeIcon }} me-1"></i>
    @endif
    {{ $badgeText }}
</span>