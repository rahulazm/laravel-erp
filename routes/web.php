<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ValveCategoryController;
use App\Http\Controllers\AssemblyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskController;


Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [DashboardController::class, 'search'])->name('dashboard.search');
    
    // Sales Orders
    Route::resource('sales-orders', SalesOrderController::class);
    Route::post('sales-orders/{sales_order}/work-order', [SalesOrderController::class, 'createWorkOrder'])->name('sales-orders.work-order');
    
    // BOMs
    Route::resource('boms', BomController::class);
    Route::post('boms/{bom}/checkout', [BomController::class, 'checkout'])->name('boms.checkout');
    Route::post('boms/{bom}/checkin', [BomController::class, 'checkin'])->name('boms.checkin');
    Route::post('boms/{bom}/approve', [BomController::class, 'approve'])->name('boms.approve');
    Route::post('boms/{bom}/reject', [BomController::class, 'reject'])->name('boms.reject');
    Route::get('boms/{bom}/versions', [BomController::class, 'versions'])->name('boms.versions');
    Route::post('boms/{bom}/restore/{version}', [BomController::class, 'restoreVersion'])->name('boms.restore-version');
    
    // Items
    Route::resource('items', ItemController::class);
    Route::post('items/{item}/checkout', [ItemController::class, 'checkout'])->name('items.checkout');
    Route::post('items/{item}/checkin', [ItemController::class, 'checkin'])->name('items.checkin');
    Route::post('items/{item}/obsolete', [ItemController::class, 'markAsObsolete'])->name('items.obsolete');
    Route::post('items/{item}/restore', [ItemController::class, 'restoreFromObsolete'])->name('items.restore');
    Route::get('items/{item}/versions', [ItemController::class, 'versions'])->name('items.versions');
    Route::post('items/{item}/restore-version/{version}', [ItemController::class, 'restoreVersion'])->name('items.restore-version');
    Route::get('obsolete-items', [ItemController::class, 'obsolete'])->name('items.obsolete-list');
    
    // Valve Categories
    Route::resource('valve-categories', ValveCategoryController::class);
    
    // Assemblies
    Route::resource('assemblies', AssemblyController::class);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/export', [ReportController::class, 'export'])->name('reports.export');
        Route::post('/export-data', [ReportController::class, 'exportData'])->name('reports.export-data');
        Route::get('/approval-times', [ReportController::class, 'approvalTimes'])->name('reports.approval-times');
        Route::get('/creation-trends', [ReportController::class, 'creationTrends'])->name('reports.creation-trends');
    });
    
    // Notifications
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'update']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::middleware(['auth'])->group(function () {

    Route::view('/reports/checkout-report', 'placeholder')
        ->name('reports.checkout-report');

    Route::view('/reports/sales-report', 'placeholder')
        ->name('reports.sales-report');

    Route::view('/reports/inventory-report', 'placeholder')
        ->name('reports.inventory-report');

});

});

require __DIR__.'/auth.php';