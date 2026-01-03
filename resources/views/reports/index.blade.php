@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Reports & Analytics</h1>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Sales Orders Report -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Sales Orders Report</h5>
                    <p class="card-text text-muted">Generate detailed reports on sales orders, including revenue, customer analysis, and trends.</p>
                    <div class="mt-3">
                        <a href="{{ route('reports.export') }}?type=sales_orders" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOM Report -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-list-alt fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">BOM Analysis</h5>
                    <p class="card-text text-muted">Analyze BOM versions, approval times, component costs, and production efficiency.</p>
                    <div class="mt-3">
                        <a href="{{ route('reports.approval-times') }}" class="btn btn-success">
                            <i class="fas fa-clock"></i> Approval Times
                        </a>
                        <a href="{{ route('reports.export') }}?type=boms" class="btn btn-outline-success mt-2">
                            <i class="fas fa-file-export"></i> Export
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Report -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-boxes fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Inventory Report</h5>
                    <p class="card-text text-muted">Track item usage, cost changes, obsolete items, and inventory valuation.</p>
                    <div class="mt-3">
                        <a href="{{ route('reports.export') }}?type=items" class="btn btn-info">
                            <i class="fas fa-chart-pie"></i> View Report
                        </a>
                        <a href="{{ route('reports.obsolete-report') }}" class="btn btn-outline-info mt-2">
                            <i class="fas fa-trash"></i> Obsolete Items
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Creation Trends -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">Creation Trends</h5>
                    <p class="card-text text-muted">Analyze creation trends for sales orders, BOMs, items, and assemblies over time.</p>
                    <div class="mt-3">
                        <a href="{{ route('reports.creation-trends') }}" class="btn btn-warning">
                            <i class="fas fa-chart-line"></i> View Trends
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Report -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-sign-out-alt fa-3x text-danger"></i>
                    </div>
                    <h5 class="card-title">Checkout Analysis</h5>
                    <p class="card-text text-muted">Monitor checkout patterns, duration, and user activity for BOMs and items.</p>
                    <div class="mt-3">
                        <a href="{{ route('reports.checkout-report') }}" class="btn btn-danger">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consolidated Report -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-alt fa-3x text-secondary"></i>
                    </div>
                    <h5 class="card-title">Consolidated Report</h5>
                    <p class="card-text text-muted">Generate comprehensive reports combining data from multiple sources for strategic analysis.</p>
                    <div class="mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#consolidatedReportModal">
                            <i class="fas fa-file-download"></i> Generate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Exports -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Exports</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Report Type</th>
                            <th>Generated By</th>
                            <th>Date</th>
                            <th>Format</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- This would typically come from a database -->
                        <tr>
                            <td>Sales Orders</td>
                            <td>John Doe</td>
                            <td>Today, 10:30 AM</td>
                            <td><span class="badge bg-success">Excel</span></td>
                            <td>1.2 MB</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>BOM Approval Times</td>
                            <td>Jane Smith</td>
                            <td>Yesterday, 2:15 PM</td>
                            <td><span class="badge bg-info">PDF</span></td>
                            <td>850 KB</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Consolidated Report Modal -->
<div class="modal fade" id="consolidatedReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('reports.generate-consolidated') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Consolidated Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Report Format</label>
                            <select name="format" class="form-select" required>
                                <option value="pdf">PDF Document</option>
                                <option value="xlsx">Excel Spreadsheet</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Include Data From</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_sales_orders" id="includeSalesOrders" checked>
                                <label class="form-check-label" for="includeSalesOrders">Sales Orders</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_boms" id="includeBoms" checked>
                                <label class="form-check-label" for="includeBoms">BOMs</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_items" id="includeItems" checked>
                                <label class="form-check-label" for="includeItems">Items</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Report Title</label>
                            <input type="text" name="report_title" class="form-control" 
                                   placeholder="e.g., Monthly Performance Report - January 2024">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Any additional notes for this report..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-download"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection