<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel ERP') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #1a2530 100%);
            color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            margin: 0.1rem 0;
            border-radius: 0.375rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: var(--secondary-color);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        .stat-card {
            border-radius: 0.5rem;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .badge-pill {
            border-radius: 50rem;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .table th {
            font-weight: 600;
            color: var(--primary-color);
            border-top: none;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding-left: 2.5rem;
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
        }
        
        .pagination .page-link {
            color: var(--secondary-color);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .filter-box {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .export-btn {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .export-btn:hover {
            background-color: #219653;
            border-color: #219653;
        }
        
        .alert {
            border: none;
            border-radius: 0.5rem;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }
        
        .breadcrumb-item.active {
            color: var(--secondary-color);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.25rem;
        }
        
        .bg-status-draft { background-color: #6c757d; color: white; }
        .bg-status-pending { background-color: #ffc107; color: black; }
        .bg-status-confirmed { background-color: #17a2b8; color: white; }
        .bg-status-in_production { background-color: #007bff; color: white; }
        .bg-status-completed { background-color: #28a745; color: white; }
        .bg-status-cancelled { background-color: #dc3545; color: white; }
        .bg-status-approved { background-color: #28a745; color: white; }
        .bg-status-rejected { background-color: #dc3545; color: white; }
        
        .priority-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
        }
        
        .bg-priority-critical { background-color: #dc3545; color: white; }
        .bg-priority-high { background-color: #fd7e14; color: white; }
        .bg-priority-medium { background-color: #ffc107; color: black; }
        .bg-priority-low { background-color: #28a745; color: white; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-color);">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <i class="fas fa-industry me-2"></i>
                    {{ config('app.name', 'Laravel ERP') }}
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        @can('viewAny', App\Models\SalesOrder::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sales-orders.index') }}">
                                <i class="fas fa-shopping-cart"></i> Sales Orders
                            </a>
                        </li>
                        @endcan
                        @can('viewAny', App\Models\Bom::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('boms.index') }}">
                                <i class="fas fa-list-alt"></i> BOMs
                            </a>
                        </li>
                        @endcan
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unreadCount = auth()->user()->notifications()->whereNull('read_at')->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger notification-badge">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                <h6 class="dropdown-header">Notifications</h6>
                                @forelse(auth()->user()->notifications()->whereNull('read_at')->latest()->limit(5)->get() as $notification)
                                    <a class="dropdown-item" href="{{ route('notifications.show', $notification) }}">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas {{ $notification->type_icon }} text-{{ $notification->priority_class }}"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <small class="d-block">{{ $notification->title }}</small>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <a class="dropdown-item text-muted" href="#">No new notifications</a>
                                @endforelse
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                                    View All Notifications
                                </a>
                            </div>
                        </li>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ auth()->user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('notifications.settings') }}">
                                    <i class="fas fa-cog me-2"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer mt-auto py-3 bg-light border-top">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6 text-muted">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel ERP') }}. All rights reserved.
                    </div>
                    <div class="col-md-6 text-md-end text-muted">
                        v1.0.0
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1050; min-width: 300px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1050; min-width: 300px;">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 1050; min-width: 300px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Scripts -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Initialize tooltips
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
        
        // Initialize DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                pageLength: 25,
                responsive: true,
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "_MENU_"
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>