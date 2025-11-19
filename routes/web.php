<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParkingEntryController;
use App\Http\Controllers\ParkingExitController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Settings\ParkingRateController;

Route::get('/', function () { return redirect()->route('login'); });

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Two-Factor Authentication
Route::middleware(['auth'])->group(function() {
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enableTwoFactor'])->name('2fa.enable');
    Route::get('/2fa/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('2fa.recovery-codes');
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
});

Route::middleware(['auth','verified','2fa'])->group(function(){
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/dashboard/charts',[DashboardController::class,'charts'])->name('dashboard.charts');
    
    // Profile & Security
    Route::get('/profile/security', function() {
        return view('profile.security');
    })->name('profile.security');

    Route::middleware(['throttle:transactions'])->group(function(){
        Route::get('/parking/entry',[ParkingEntryController::class,'create'])->name('parking.entry.create');
        Route::post('/parking/entry',[ParkingEntryController::class,'store'])->name('parking.entry.store');
        Route::get('/parking/entry/success/{id}',[ParkingEntryController::class,'success'])->name('parking.entry.success');
        Route::get('/parking/download-qr/{id}',[ParkingEntryController::class,'downloadQR'])->name('parking.download-qr');
        Route::get('/parking/print-ticket/{id}',[ParkingEntryController::class,'printTicket'])->name('parking.print-ticket');
        Route::get('/parking/exit',[ParkingExitController::class,'create'])->name('parking.exit.create');
        Route::post('/parking/exit',[ParkingExitController::class,'store'])->name('parking.exit.store');
        Route::get('/parking/exit/success/{id}',[ParkingExitController::class,'success'])->name('parking.exit.success');
        Route::get('/parking/print-receipt/{id}',[ParkingExitController::class,'printReceipt'])->name('parking.print-receipt');
        Route::get('/parking/check',[ParkingExitController::class,'check'])->name('parking.check');
    });

    Route::middleware(['role:admin'])->group(function(){
        Route::get('/reports',[ReportController::class,'index'])->name('reports.index')->middleware('throttle:60,1');
        Route::post('/reports/export',[ReportController::class,'export'])->name('reports.export')->middleware('throttle:20,1');
    });

    Route::middleware(['role:admin'])->group(function(){
        Route::get('/activity',[ActivityLogController::class,'index'])->name('activity.index');
        Route::resource('admin/users', UserController::class, [
            'as' => 'admin',
            'names' => [
                'index'   => 'admin.users.index',
                'create'  => 'admin.users.create',
                'store'   => 'admin.users.store',
                'edit'    => 'admin.users.edit',
                'update'  => 'admin.users.update',
                'destroy' => 'admin.users.destroy',
            ]
        ]);
        
        // Settings routes
        Route::resource('settings/parking-rates', ParkingRateController::class, [
            'as' => 'settings',
            'names' => [
                'index'  => 'settings.parking-rates.index',
                'edit'   => 'settings.parking-rates.edit',
                'update' => 'settings.parking-rates.update',
            ]
        ])->only(['index', 'edit', 'update']);
    });
});
