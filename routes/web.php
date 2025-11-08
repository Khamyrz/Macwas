<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\PlumberController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoginMonitoringController;
use App\Http\Controllers\Auth\AdminRegisteredUserController;
use App\Models\SetupRequest;
use Illuminate\Support\Facades\Route;


// Redirect root URL to welcome page
Route::get('/', function () {
    return view('auth.welcome');
});

// Removed admin registration routes per request

// Admin self-registration (email + password only)
Route::middleware('guest')->group(function () {
    Route::get('/admin/register', [AdminRegisteredUserController::class, 'create'])
        ->name('admin.register');
    Route::post('/admin/register', [AdminRegisteredUserController::class, 'store'])
        ->name('admin.register.store');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Refresh user from database to ensure we have the correct role
    $user->refresh();
    
    // Explicitly check each role and redirect to correct dashboard
    // Order matters - check most specific roles first
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'accountant') {
        return redirect()->route('accountant.dashboard');
    } elseif ($user->role === 'plumber') {
        return redirect()->route('plumber.dashboard');
    } elseif ($user->role === 'customer') {
        return redirect()->route('customer.dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/operations', [AdminController::class, 'operations'])->name('operations');
    Route::get('/monthly-bills', [AdminController::class, 'monthlyBillsPage'])->name('monthly-bills');
    Route::get('/pending-accounts', [AdminController::class, 'pendingAccounts'])->name('pending-accounts');
    Route::post('/approve-account/{id}', [AdminController::class, 'approveAccount'])->name('approve-account');
    Route::post('/reject-account/{id}', [AdminController::class, 'rejectAccount'])->name('reject-account');
    Route::get('/water-rates', [AdminController::class, 'waterRates'])->name('water-rates');
    Route::post('/set-water-rate', [AdminController::class, 'setWaterRate'])->name('set-water-rate');
    Route::post('/assign-plumber', [AdminController::class, 'assignPlumber'])->name('assign-plumber');
    Route::post('/assign-disconnection', [AdminController::class, 'assignDisconnection'])->name('assign-disconnection');
    Route::get('/user-records/{role}', [AdminController::class, 'userRecords'])->name('user-records');
    Route::get('/search-users/{role}', [AdminController::class, 'searchUsers'])->name('search-users');
    Route::post('/generate-monthly-bills', [AdminController::class, 'generateMonthlyBills'])->name('generate-monthly-bills');
    Route::get('/create-user', [AdminController::class, 'createUser'])->name('create-user');
    Route::post('/create-user', [AdminController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Login Monitoring Routes
    Route::get('/monitoring', [LoginMonitoringController::class, 'index'])->name('monitoring');
    Route::get('/monitoring/data', [LoginMonitoringController::class, 'getLoginAttempts'])->name('monitoring.data');
    Route::get('/monitoring/export', [LoginMonitoringController::class, 'export'])->name('monitoring.export');
    Route::post('/monitoring/clear', [LoginMonitoringController::class, 'clearOldAttempts'])->name('monitoring.clear');
});

// Accountant routes
Route::middleware(['auth', 'verified', 'accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');
    Route::get('/search-customers', [AccountantController::class, 'searchCustomers'])->name('search-customers');
    Route::post('/process-payment', [AccountantController::class, 'processPayment'])->name('process-payment');
    Route::get('/customer-bills/{customerId}', [AccountantController::class, 'getCustomerBills'])->name('customer-bills');
    Route::get('/payment-history', [AccountantController::class, 'paymentHistory'])->name('payment-history');
    Route::get('/receipt/{paymentId}', [AccountantController::class, 'printReceipt'])->name('receipt');
});

// Plumber routes
Route::middleware(['auth', 'verified', 'plumber'])->prefix('plumber')->name('plumber.')->group(function () {
    Route::get('/dashboard', [PlumberController::class, 'dashboard'])->name('dashboard');
    Route::post('/start-job/{connectionId}', [PlumberController::class, 'startJob'])->name('start-job');
    Route::post('/complete-job/{connectionId}', [PlumberController::class, 'completeJob'])->name('complete-job');
    Route::get('/customer-history', [PlumberController::class, 'customerHistory'])->name('customer-history');
    Route::post('/update-availability', [PlumberController::class, 'updateAvailability'])->name('update-availability');
    Route::post('/record-reading/{customerId}', [PlumberController::class, 'recordReading'])->name('record-reading');
    Route::get('/receipt/{customerId}', [PlumberController::class, 'printBillReceipt'])->name('receipt');
    Route::get('/last-reading/{customerId}', [PlumberController::class, 'lastReading'])->name('last-reading');
    Route::post('/mark-notification-read/{notificationId}', [PlumberController::class, 'markNotificationRead'])->name('mark-notification-read');
    Route::get('/notifications', [PlumberController::class, 'notifications'])->name('notifications');
    Route::post('/mark-all-notifications-read', [PlumberController::class, 'markAllNotificationsRead'])->name('mark-all-notifications-read');
});

// Customer routes
Route::middleware(['auth', 'verified', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/bills', [CustomerController::class, 'bills'])->name('bills');
    Route::get('/recent-bills', [CustomerController::class, 'recentBills'])->name('recent-bills');
    Route::get('/payment-history', [CustomerController::class, 'paymentHistory'])->name('payment-history');
    Route::get('/recent-payments', [CustomerController::class, 'recentPayments'])->name('recent-payments');
    Route::post('/request-setup', function() {
        $user = auth()->user();
        // Create a pending setup request
        SetupRequest::firstOrCreate(
            ['customer_id' => $user->id, 'status' => 'pending'],
            ['notes' => 'Customer requested water setup']
        );
        return back()->with('success', 'Setup request sent to admin.');
    })->name('request-setup');
    Route::get('/plumber-info', [CustomerController::class, 'plumberInfo'])->name('plumber-info');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::post('/update-profile', [CustomerController::class, 'updateProfile'])->name('update-profile');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
