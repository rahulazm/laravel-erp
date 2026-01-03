@php
    $currentRoute = Route::currentRouteName();
    
    $menuItems = [
        [
            'title' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'tachometer-alt',
            'active' => $currentRoute === 'dashboard',
        ],
        [
            'title' => 'Sales Orders',
            'route' => 'sales-orders.index',
            'icon' => 'shopping-cart',
            'active' => str_starts_with($currentRoute, 'sales-orders'),
            'permission' => 'viewAny,App\\Models\\SalesOrder',
        ],
        [
            'title' => 'Valve Categories',
            'route' => 'valve-categories.index',
            'icon' => 'filter',
            'active' => str_starts_with($currentRoute, 'valve-categories'),
            'permission' => 'viewAny,App\\Models\\ValveCategory',
        ],
        [
            'title' => 'BOM Management',
            'icon' => 'list-alt',
            'children' => [
                [
                    'title' => 'BOMs',
                    'route' => 'boms.index',
                    'icon' => 'list',
                    'active' => str_starts_with($currentRoute, 'boms'),
                    'permission' => 'viewAny,App\\Models\\Bom',
                ],
                [
                    'title' => 'Assemblies',
                    'route' => 'assemblies.index',
                    'icon' => 'cogs',
                    'active' => str_starts_with($currentRoute, 'assemblies'),
                    'permission' => 'viewAny,App\\Models\\Assembly',
                ],
            ],
        ],
        [
            'title' => 'Inventory',
            'icon' => 'boxes',
            'children' => [
                [
                    'title' => 'Items',
                    'route' => 'items.index',
                    'icon' => 'barcode',
                    'active' => str_starts_with($currentRoute, 'items'),
                    'permission' => 'viewAny,App\\Models\\Item',
                ],
                [
                    'title' => 'Obsolete Items',
                    'route' => 'items.obsolete-list',
                    'icon' => 'trash',
                    'active' => $currentRoute === 'items.obsolete-list',
                    'permission' => 'viewAny,App\\Models\\Item',
                ],
            ],
        ],
        [
            'title' => 'Tasks',
            'route' => 'tasks.index',
            'icon' => 'tasks',
            'active' => str_starts_with($currentRoute, 'tasks'),
            'permission' => 'viewAny,App\\Models\\Task',
        ],
        [
            'title' => 'Notifications',
            'route' => 'notifications.index',
            'icon' => 'bell',
            'active' => str_starts_with($currentRoute, 'notifications'),
            'badge' => auth()->user()->notifications()->whereNull('read_at')->count(),
        ],
        [
            'title' => 'Reports',
            'icon' => 'chart-bar',
            'children' => [
                [
                    'title' => 'Export Data',
                    'route' => 'reports.export',
                    'icon' => 'file-export',
                    'active' => $currentRoute === 'reports.export',
                ],
                [
                    'title' => 'Approval Times',
                    'route' => 'reports.approval-times',
                    'icon' => 'clock',
                    'active' => $currentRoute === 'reports.approval-times',
                ],
                [
                    'title' => 'Creation Trends',
                    'route' => 'reports.creation-trends',
                    'icon' => 'chart-line',
                    'active' => $currentRoute === 'reports.creation-trends',
                ],
                [
                    'title' => 'Checkout Report',
                    'route' => 'reports.checkout-report',
                    'icon' => 'sign-out-alt',
                    'active' => $currentRoute === 'reports.checkout-report',
                ],
            ],
        ],
    ];
@endphp

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <!-- Search Box -->
        <div class="sidebar-widget search-widget">
            <form class="search-form" method="GET" action="{{ route('dashboard.search') }}">
                <div class="input-group">
                    <input type="text" class="form-control" name="query" placeholder="Search...">
                    <button class="btn" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Navigation Menu -->
        <nav class="sidebar-menu">
            <ul class="list-unstyled">
                @foreach($menuItems as $item)
                    @if(isset($item['permission']) && !Gate::allows($item['permission']))
                        @continue
                    @endif
                    
                    @if(isset($item['children']))
                        <li class="has-submenu {{ $item['active'] ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="sidebar-link">
                                <i class="fas fa-{{ $item['icon'] }}"></i>
                                <span>{{ $item['title'] }}</span>
                                <i class="fas fa-chevron-down arrow"></i>
                            </a>
                            <ul class="submenu">
                                @foreach($item['children'] as $child)
                                    @if(isset($child['permission']) && !Gate::allows($child['permission']))
                                        @continue
                                    @endif
                                    
                                    <li class="{{ $child['active'] ? 'active' : '' }}">
                                        <a href="{{ route($child['route']) }}">
                                            <i class="fas fa-{{ $child['icon'] }}"></i>
                                            {{ $child['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="{{ $item['active'] ? 'active' : '' }}">
                            <a href="{{ route($item['route']) }}" class="sidebar-link">
                                <i class="fas fa-{{ $item['icon'] }}"></i>
                                <span>{{ $item['title'] }}</span>
                                @if(isset($item['badge']) && $item['badge'] > 0)
                                    <span class="badge bg-danger float-end">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" 
                         alt="{{ auth()->user()->name }}">
                </div>
                <div class="user-details">
                    <h6 class="user-name mb-0">{{ auth()->user()->name }}</h6>
                    <small class="user-role text-muted">{{ auth()->user()->designation }}</small>
                </div>
            </div>
            
            <div class="sidebar-actions mt-3">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1000;
    transition: all 0.3s;
}

.sidebar-inner {
    padding: 20px 0;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.search-widget {
    padding: 0 20px 20px;
}

.search-form .input-group {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
    overflow: hidden;
}

.search-form input {
    background: transparent;
    border: none;
    color: #fff;
    padding: 10px 15px;
}

.search-form input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-form button {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.5);
    padding: 0 15px;
}

.sidebar-menu {
    flex: 1;
    overflow-y: auto;
}

.sidebar-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu li {
    position: relative;
}

.sidebar-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s;
}

.sidebar-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-link.active {
    color: #fff;
    background: rgba(255, 255, 255, 0.15);
    border-left: 3px solid #3498db;
}

.sidebar-link i:first-child {
    width: 20px;
    margin-right: 10px;
    text-align: center;
}

.arrow {
    margin-left: auto;
    transition: transform 0.3s;
}

.has-submenu.active .arrow {
    transform: rotate(180deg);
}

.submenu {
    display: none;
    background: rgba(0, 0, 0, 0.2);
}

.has-submenu.active .submenu {
    display: block;
}

.submenu li a {
    padding: 10px 20px 10px 50px;
    font-size: 0.9em;
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 10px;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-details h6 {
    font-size: 0.9em;
    margin-bottom: 2px;
}

.user-details small {
    font-size: 0.8em;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle submenus
    document.querySelectorAll('.has-submenu > .sidebar-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('active');
        });
    });
    
    // Auto-close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('[data-bs-toggle="sidebar"]');
            
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        }
    });
});
</script>