<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('audit', \App\Http\Controllers\Admin\AuditController::class)->only(['index', 'show']);

});

Route::middleware(['auth'])->group(function () {
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('purchase-orders/{purchaseOrder}/download', [\App\Http\Controllers\PurchaseOrderController::class, 'download'])
        ->name('purchase-orders.download');
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class)
        ->parameters(['purchase-orders' => 'purchaseOrder']);
    Route::get('invoice-settings', [\App\Http\Controllers\InvoiceSettingController::class, 'edit'])
        ->name('invoice-settings.edit');
    Route::put('invoice-settings', [\App\Http\Controllers\InvoiceSettingController::class, 'update'])
        ->name('invoice-settings.update');
});

Route::middleware(['auth'])->prefix('invoices')->as('invoices.')->group(function () {
    Route::get('/', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('index');
    Route::post('generate', [\App\Http\Controllers\InvoiceController::class, 'generate'])->name('generate');
    Route::get('{invoice}/download/{type}', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('download');
    Route::post('{invoice}/send', [\App\Http\Controllers\InvoiceController::class, 'send'])->name('send');
    Route::get('{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('show');
    Route::delete('{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('bayi', \App\Http\Controllers\BayiController::class);
    Route::post('bayi/{id}/restore', [\App\Http\Controllers\BayiController::class, 'restore'])->name('bayi.restore');
});

require __DIR__.'/auth.php';
