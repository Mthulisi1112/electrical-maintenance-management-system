<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\FaultController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\ContactController; 
use Illuminate\Support\Facades\Route;

// Static pages - PUBLIC (accessible to everyone, no authentication required)
Route::view('/privacy-policy', 'static.privacy')->name('privacy-policy');
Route::view('/terms-of-service', 'static.terms')->name('terms-of-service');
Route::view('/contact', 'static.contact')->name('contact');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit'); 

Route::middleware(['auth', 'verified'])->group(function () {

    // Admin User Management
    Route::prefix('admin')->name('admin.')->group(function () {
        // Middleware to check if user is admin
        Route::middleware(['can:viewAny,App\Models\User'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            
            // Email verification resend
            Route::post('/users/{user}/verify-email', [UserController::class, 'sendVerification'])->name('users.verify-email');
        });
    }); 
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Assets
    Route::resource('assets', AssetController::class);

    // QR Code 
    Route::get('/assets/{asset}/qrcode', [AssetController::class, 'qrCode'])->name('assets.qrcode');
    
    // Work Orders
    Route::resource('work-orders', WorkOrderController::class);
    Route::post('/work-orders/{workOrder}/start', [WorkOrderController::class, 'start'])->name('work-orders.start');
    Route::post('/work-orders/{workOrder}/complete', [WorkOrderController::class, 'complete'])->name('work-orders.complete');
    Route::post('/work-orders/{workOrder}/verify', [WorkOrderController::class, 'verify'])->name('work-orders.verify');
    
    // Faults
    Route::resource('faults', FaultController::class);
    Route::post('/faults/{fault}/assign', [FaultController::class, 'assign'])->name('faults.assign');
    Route::post('/faults/{fault}/resolve', [FaultController::class, 'resolve'])->name('faults.resolve');
    
    // Maintenance Schedules
    Route::resource('maintenance-schedules', MaintenanceScheduleController::class); 
    Route::patch('/maintenance-schedules/{maintenanceSchedule}/toggle-active', [MaintenanceScheduleController::class, 'toggleActive'])->name('maintenance-schedules.toggle-active');
    Route::post('/maintenance-schedules/{maintenanceSchedule}/generate-work-order', [MaintenanceScheduleController::class, 'generateWorkOrders'])->name('maintenance-schedules.generate-work-order');

    // Search 
     Route::get('/search', [SearchController::class, 'search'])->name('search');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/assets', [ReportController::class, 'assets'])->name('reports.assets');
    Route::get('/reports/maintenance', [ReportController::class, 'maintenance'])->name('reports.maintenance');
    Route::get('/reports/faults', [ReportController::class, 'faults'])->name('reports.faults');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Only allow admin to delete profiles
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy')
        ->middleware('can:delete-users');
}); 

require __DIR__.'/auth.php';