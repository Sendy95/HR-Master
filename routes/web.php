<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdmController;

// --- GUEST ROUTES (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () { 
        return redirect()->route('login'); 
    });
    
    Route::get('/login', [PdmController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PdmController::class, 'login'])->name('login.proses');
    Route::post('/get-user-email', [PdmController::class, 'getUserEmail'])->name('user.get-email');
});

// --- AUTH ROUTES (Sudah Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Logout & Security Essentials
    Route::get('/logout', [PdmController::class, 'logout'])->name('logout'); 
    Route::post('/pdm/password-first-update', [PdmController::class, 'updatePasswordFirst'])->name('password.update.first');
    Route::post('/otp/resend', [PdmController::class, 'resendOtp'])->name('otp.resend');

    // --- PROTECTED ROUTES (Harus Lewat Force Password Change) ---
    Route::middleware(['force.password'])->group(function () {
        
        Route::get('/dashboard', function () { 
            return redirect()->route('pdm.index'); 
        })->name('dashboard');

        // Personal Data Management (User Side)
        Route::prefix('pdm')->name('pdm.')->group(function () {
            Route::get('/', [PdmController::class, 'edit'])->name('index');
            Route::post('/', [PdmController::class, 'update'])->name('update');
            
            // Route yang tadi menyebabkan error RouteNotFoundException
            Route::get('/get-data', [PdmController::class, 'getData'])->name('get-data');
            
            Route::get('/view-document/{type}', [PdmController::class, 'viewDocument'])->name('view-document');
        });

        // --- ADMIN ROUTES ---
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/employee-report', [PdmController::class, 'showReport'])->name('report');
            Route::get('/export-excel', [PdmController::class, 'export'])->name('export.excel');
        });
        
    });
});