<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdmController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// --- ROUTES AUTHENTICATION ---
Route::get('/login', [PdmController::class, 'showLoginForm'])->name('login');
Route::post('/login', [PdmController::class, 'login'])->name('login.proses');
Route::get('/logout', [PdmController::class, 'logout'])->name('logout');

// --- ROUTES KARYAWAN (Perlu Login) ---
// Route::middleware(['auth'])->group(function () {
    // 1. Halaman Utama PDM (Menampilkan Form / Status Pending)
    Route::get('/pdm', [PdmController::class, 'index'])->name('pdm.index');
    
    // 2. Ambil Data JSON (untuk pengisian otomatis form)
    Route::get('/pdm/get-data/{employee_no}', [PdmController::class, 'getData'])->name('pdm.get-data');
    
    // 3. Proses Update (Hanya boleh dipanggil via POST dari Form/AJAX)
    // Jika Anda tidak sengaja mengetik URL ini di browser, Laravel akan mengarahkan balik ke /pdm
    Route::post('/pdm/update', [PdmController::class, 'update'])->name('pdm.update');

    // --- ROUTES HR ADMIN ---
    Route::get('/admin/pdm-approval', [PdmController::class, 'pendingList'])->name('admin.pdm.index');
    Route::post('/admin/pdm-action/{id}', [PdmController::class, 'approveAction'])->name('admin.pdm.action');
    
// });