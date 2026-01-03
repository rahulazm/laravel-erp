<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container-fluid">
            <!-- Sidebar Toggle -->
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Brand Logo -->
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-industry me-2 text-primary"></i>
                <span class="fw-bold">{{ config('app.name', 'Laravel ERP') }}</span>
            </a>
            
            <!-- Right Side Items -->
            <div class="d-flex align-items-center ms-auto">
                <!-- Quick Search -->
                <div class="search-box me-3 d-none d-md-block">
                    <form method="GET" action="{{ route('dashboard.search') }}" class="position-relative">
                        <input type="text" class="form-control search-input" name="query" placeholder="Search...">
                        <button class="btn search-btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Notifications -->
                <div class="dropdown me-3">
                    <a href="#" class="nav-link position-relative" id="notificationsDropdown" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-lg"></i>
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
                                    <div class="flex-shrink-0 me-2">
                                        <i class="fas {{ $notification->type_icon }} text-{{ $notification->priority_class }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small">{{ $notification->title }}</div>
                                        <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <a class="dropdown-item text-muted" href="#">
                                <div class="text-center py-2">No new notifications</div>
                            </a>
                        @endforelse
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                            View All Notifications
                        </a>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" 
                       id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="me-2 d-none d-md-block">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->role }}</small>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" 
                             alt="{{ auth()->user()->name }}" class="rounded-circle" width="32" height="32">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('notifications.settings') }}">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
.header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: white;
}

.search-box {
    position: relative;
    width: 300px;
}

.search-input {
    padding-left: 40px;
    border-radius: 20px;
    border: 1px solid #dee2e6;
}

.search-btn {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #6c757d;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 0.7rem;
    padding: 0.25em 0.4em;
}

.dropdown-menu {
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .search-box {
        width: 200px;
    }
}
</style>