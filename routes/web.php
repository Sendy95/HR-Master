<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdmController;

// --- 1. GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () { 
        return redirect()->route('login'); 
    });
    
    Route::get('/login', [PdmController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PdmController::class, 'login'])->name('login.proses');
});

// --- 2. PROTECTED ROUTES (Harus Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Redirect dashboard langsung ke halaman PDM
    Route::get('/dashboard', function () { 
        return redirect()->route('pdm.index'); 
    });

    // --- HALAMAN USER / KARYAWAN ---
    // URL utama PDM (Tampilan & Form)
    Route::get('/pdm', [PdmController::class, 'edit'])->name('pdm.index');
    
    // Proses Simpan Perubahan (POST)
    Route::post('/pdm', [PdmController::class, 'update'])->name('pdm.update');
    
    // API untuk ambil data JSON jika dibutuhkan oleh JavaScript
    Route::get('/pdm/get-data/{employee_no}', [PdmController::class, 'getData'])->name('pdm.get-data');
    
    // Logout
    Route::get('/logout', [PdmController::class, 'logout'])->name('logout'); 

    // --- 3. HR ADMIN ROUTES (Prefix: admin) ---
    Route::prefix('admin')->name('admin.')->group(function () {
        // Halaman Daftar Pengajuan (Header: employee_update_requests)
        Route::get('/pdm-approval', [PdmController::class, 'pendingList'])->name('pdm.index');
        
        // Aksi Approve per Tiket (Bulk Update berdasarkan Request ID)
        Route::post('/pdm-approve/{id}', [PdmController::class, 'approveBulk'])->name('pdm.approve');
        
        // Aksi Reject per Tiket
        Route::post('/pdm-reject/{id}', [PdmController::class, 'rejectRequest'])->name('pdm.reject');
    });
});