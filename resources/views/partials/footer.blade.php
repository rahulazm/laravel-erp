<footer class="footer mt-auto py-3 bg-light border-top">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="text-muted">
                    <small>
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel ERP') }}. All rights reserved.
                        <span class="mx-2">|</span>
                        <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                        <span class="mx-2">|</span>
                        <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                    </small>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">
                    <i class="fas fa-code me-1"></i> Version 1.0.0
                    <span class="mx-2">|</span>
                    <i class="fas fa-server me-1"></i> Server: {{ gethostname() }}
                    <span class="mx-2">|</span>
                    <i class="fas fa-clock me-1"></i> {{ now()->format('H:i:s') }}
                </small>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('dashboard') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                    <a href="{{ route('sales-orders.index') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-shopping-cart me-1"></i> Sales Orders
                    </a>
                    <a href="{{ route('boms.index') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-list-alt me-1"></i> BOMs
                    </a>
                    <a href="{{ route('items.index') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-barcode me-1"></i> Items
                    </a>
                    <a href="{{ route('reports.export') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-chart-bar me-1"></i> Reports
                    </a>
                    <a href="{{ route('notifications.index') }}" class="text-muted small text-decoration-none">
                        <i class="fas fa-bell me-1"></i> Notifications
                    </a>
                    <a href="#" class="text-muted small text-decoration-none">
                        <i class="fas fa-question-circle me-1"></i> Help
                    </a>
                    <a href="#" class="text-muted small text-decoration-none">
                        <i class="fas fa-phone me-1"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
        
        <!-- System Status -->
        @php
            $systemStatus = [
                'database' => true,
                'email' => true,
                'storage' => '85%',
                'uptime' => '99.8%',
            ];
        @endphp
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <span class="badge bg-success">
                        <i class="fas fa-database me-1"></i> Database: Online
                    </span>
                    <span class="badge bg-success">
                        <i class="fas fa-envelope me-1"></i> Email: Online
                    </span>
                    <span class="badge bg-warning">
                        <i class="fas fa-hdd me-1"></i> Storage: {{ $systemStatus['storage'] }}
                    </span>
                    <span class="badge bg-info">
                        <i class="fas fa-chart-line me-1"></i> Uptime: {{ $systemStatus['uptime'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>