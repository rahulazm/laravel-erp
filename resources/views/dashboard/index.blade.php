@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Navigation -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
            <div class="position-sticky pt-3">
                <!-- Search Section -->
                <div class="mb-4">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                        <span>Global Search</span>
                    </h6>
                    <form id="globalSearchForm" class="px-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="globalSearch" placeholder="Search everything...">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <select class="form-select form-select-sm" id="searchType">
                                <option value="all">All Types</option>
                                <option value="sales_order">Sales Orders</option>
                                <option value="valve_category">Valve Categories</option>
                                <option value="bom">BOMs/Assemblies</option>
                                <option value="item">Item Codes</option>
                            </select>
                        </div>
                    </form>
                    <div id="searchResults" class="mt-2 px-3 d-none"></div>
                </div>

                <!-- Checked Out Items -->
                <div class="mb-4">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                        <span>Checked Out by Me</span>
                        <span class="badge bg-primary rounded-pill">{{ $checkedOutItems->count() }}</span>
                    </h6>
                    <ul class="nav flex-column">
                        @forelse($checkedOutItems as $checkout)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route(strtolower(class_basename($checkout->checkoutable_type)) . 's.show', $checkout->checkoutable) }}">
                                    <i class="fas fa-file-alt"></i>
                                    @if($checkout->checkoutable_type === 'App\\Models\\Bom')
                                        {{ $checkout->checkoutable->bom_number }}
                                    @else
                                        {{ $checkout->checkoutable->item_code }}
                                    @endif
                                </a>
                            </li>
                        @empty
                            <li class="nav-item px-3">
                                <small class="text-muted">No items checked out</small>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Pending Tasks -->
                <div class="mb-4">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                        <span>Pending Actions</span>
                        <span class="badge bg-warning rounded-pill">{{ $pendingTasks->count() }}</span>
                    </h6>
                    <ul class="nav flex-column">
                        @forelse($pendingTasks as $task)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tasks.show', $task) }}">
                                    <i class="fas fa-tasks"></i>
                                    {{ Str::limit($task->title, 20) }}
                                    <small class="text-muted d-block">Due: {{ $task->due_date->format('M d') }}</small>
                                </a>
                            </li>
                        @empty
                            <li class="nav-item px-3">
                                <small class="text-muted">No pending tasks</small>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Notifications -->
                @if(auth()->user()->unreadNotifications->count())
    {{-- notifications UI --}}
@endif
                <div class="mb-4">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                        <span>Recent Notifications</span>
                        <span class="badge bg-danger rounded-pill">{{ $notifications->count() }}</span>
                    </h6>
                    <ul class="nav flex-column">
                        @forelse($notifications as $notification)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('notifications.show', $notification) }}">
                                    <i class="fas {{ $notification->type_icon }}"></i>
                                    {{ Str::limit($notification->title, 20) }}
                                    <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                </a>
                            </li>
                        @empty
                            <li class="nav-item px-3">
                                <small class="text-muted">No notifications</small>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Reports Section -->
                <div class="mb-4">
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mb-1 text-muted">
                        <span>Reports</span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.export') }}">
                                <i class="fas fa-file-excel"></i> Export Data
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.approval-times') }}">
                                <i class="fas fa-clock"></i> Approval Times
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.creation-trends') }}">
                                <i class="fas fa-chart-line"></i> Creation Trends
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.checkout-report') }}">
                                <i class="fas fa-sign-out-alt"></i> Checkout Report
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateCharts('today')">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateCharts('week')">This Week</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateCharts('month')">This Month</button>
                    </div>
                    <button class="btn btn-sm btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Sales Orders
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $salesOrdersCount }}</div>
                                    <div class="mt-2">
                                        <a href="{{ route('sales-orders.create') }}" class="text-primary small">
                                            <i class="fas fa-plus"></i> Create New
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        BOMs (With Versions)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bomsCount }}</div>
                                    <div class="mt-2">
                                        <a href="{{ route('boms.create') }}" class="text-success small">
                                            <i class="fas fa-plus"></i> Create New
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list-alt fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Item Codes (With Versions)
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $itemsCount }}</div>
                                    <div class="mt-2">
                                        <a href="{{ route('items.create') }}" class="text-info small">
                                            <i class="fas fa-plus"></i> Create New
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-barcode fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stat-card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Obsolete Items
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $obsoleteItemsCount }}</div>
                                    <div class="mt-2">
                                        <a href="{{ route('items.obsolete-list') }}" class="text-warning small">
                                            <i class="fas fa-eye"></i> View All
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-trash-alt fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Monthly Creation Trends</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="monthlyChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Average Approval Time</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <i class="fas fa-clock fa-3x text-primary me-3"></i>
                                <div>
                                    <h1 class="display-4">{{ number_format($averageApprovalTime, 1) }}</h1>
                                    <p class="text-muted mb-0">Hours</p>
                                </div>
                            </div>
                            <small class="text-muted">Average time taken for BOM approval</small>
                            <div class="mt-3">
                                <a href="{{ route('reports.approval-times') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-chart-bar"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Sales Orders</h6>
                            <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentSalesOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('sales-orders.show', $order) }}" class="text-decoration-none">
                                                        {{ $order->order_number }}
                                                    </a>
                                                </td>
                                                <td>{{ Str::limit($order->customer_name, 15) }}</td>
                                                <td>
                                                    <span class="status-badge bg-status-{{ $order->status }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->created_at->format('M d') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Recent BOMs</h6>
                            <a href="{{ route('boms.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>BOM #</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Version</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentBoms as $bom)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('boms.show', $bom) }}" class="text-decoration-none">
                                                        {{ $bom->bom_number }}
                                                    </a>
                                                </td>
                                                <td>{{ Str::limit($bom->name, 20) }}</td>
                                                <td>
                                                    <span class="status-badge bg-status-{{ $bom->status }}">
                                                        {{ ucfirst($bom->status) }}
                                                    </span>
                                                </td>
                                                <td>v{{ $bom->version }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global Search
document.getElementById('globalSearchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const query = document.getElementById('globalSearch').value;
    const type = document.getElementById('searchType').value;
    
    if (!query.trim()) return;
    
    fetch(`{{ route("dashboard.search") }}?query=${encodeURIComponent(query)}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('searchResults');
            resultsDiv.innerHTML = '';
            resultsDiv.classList.remove('d-none');
            
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="alert alert-info py-2">No results found</div>';
                return;
            }
            
            const list = document.createElement('div');
            list.className = 'list-group';
            
            data.forEach(item => {
                const link = document.createElement('a');
                link.href = item.route;
                link.className = 'list-group-item list-group-item-action py-2';
                link.innerHTML = `
                    <div class="d-flex w-100 justify-content-between">
                        <small class="text-muted">${item.type}</small>
                        <small class="text-muted">${item.created_at}</small>
                    </div>
                    <div class="fw-bold">${item.name || item.order_number || item.item_code}</div>
                    ${item.customer_name ? `<small class="text-muted">${item.customer_name}</small>` : ''}
                `;
                list.appendChild(link);
            });
            
            resultsDiv.appendChild(list);
        })
        .catch(error => {
            console.error('Search error:', error);
        });
});

// Monthly Chart
const monthlyChartCtx = document.getElementById('monthlyChart').getContext('2d');

// Prepare data for all 12 months
const allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// Initialize with zeros
const salesOrderData = Array(12).fill(0);
const bomData = Array(12).fill(0);
const itemData = Array(12).fill(0);

// Fill actual data
@foreach($monthlySalesOrders as $month => $count)
    salesOrderData[{{ $month }} - 1] = {{ $count }};
@endforeach

@foreach($monthlyBoms as $month => $count)
    bomData[{{ $month }} - 1] = {{ $count }};
@endforeach

@foreach($monthlyItems as $month => $count)
    itemData[{{ $month }} - 1] = {{ $count }};
@endforeach

const monthlyChart = new Chart(monthlyChartCtx, {
    type: 'line',
    data: {
        labels: allMonths,
        datasets: [
            {
                label: 'Sales Orders',
                data: salesOrderData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'BOMs',
                data: bomData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Items',
                data: itemData,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                grid: {
                    borderDash: [2, 2]
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'nearest'
        }
    }
});

// Function to update charts based on period
function updateCharts(period) {
    fetch(`/dashboard/stats?period=${period}`)
        .then(response => response.json())
        .then(data => {
            // Update statistics cards
            document.querySelector('.stat-card:nth-child(1) .h5').textContent = data.sales_orders.total;
            document.querySelector('.stat-card:nth-child(2) .h5').textContent = data.boms.total;
            document.querySelector('.stat-card:nth-child(3) .h5').textContent = data.items.total;
            document.querySelector('.stat-card:nth-child(4) .h5').textContent = data.obsolete_items.total;
            
            // Update approval time
            document.querySelector('.display-4').textContent = data.approval_time.avg_hours.toFixed(1);
            
            // Update chart data
            // You would need to implement this based on your data structure
            console.log('Chart data updated for period:', period);
        });
}

// Auto-refresh notifications
setInterval(function() {
    fetch('/notifications/quick-actions')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    // Create badge if it doesn't exist
                    const bell = document.querySelector('.fa-bell').parentElement;
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge bg-danger notification-badge';
                    newBadge.textContent = data.count;
                    bell.appendChild(newBadge);
                }
            } else if (badge) {
                badge.remove();
            }
        });
}, 30000); // Update every 30 seconds
</script>
@endpush