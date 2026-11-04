<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RFIDController;
use App\Http\Controllers\GuruTendikController;
use App\Http\Controllers\StafKedisiplinanController;
use App\Http\Controllers\KepalaSekolahController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Public & Utility Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Hardware/Utility Routes
Route::get('/rfid', [RFIDController::class, 'store']);
Route::get('/test-esp', function () {
    return 'OK';
});

// Post-production symlink debug
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully.';
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // ----------------------------------------------------------------------
    // 1. Guru & Tendik Routes
    // ----------------------------------------------------------------------
    Route::controller(GuruTendikController::class)->prefix('guru-tendik')->name('guru-tendik.')->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        
        // Laporan & Rekapitulasi
        Route::get('/laporan-rekapitulasi', 'laporanRekapitulasi')->name('laporan-rekapitulasi');
        Route::get('/laporan/export/excel', 'exportExcel')->name('export.excel');
        Route::get('/laporan/export/pdf', 'exportPdf')->name('export.pdf');
    });

    // ----------------------------------------------------------------------
    // 2. Staf Kedisiplinan Routes
    // ----------------------------------------------------------------------
    Route::controller(StafKedisiplinanController::class)->prefix('staf-kedisiplinan')->name('staf-kedisiplinan.')->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/dashboard/refresh', 'refresh')->name('dashboard.refresh');

        // Manajemen Kehadiran
        Route::get('/manajemen-kehadiran', 'manajemenKehadiran')->name('manajemen-kehadiran');
        Route::post('/manajemen-kehadiran', 'store')->name('manajemen-kehadiran.store');
        Route::put('/manajemen-kehadiran/{id}', 'update')->name('manajemen-kehadiran.update');
        Route::delete('/manajemen-kehadiran/{id}', 'destroy')->name('manajemen-kehadiran.destroy');

        // Laporan & Rekapitulasi
        Route::get('/laporan-rekapitulasi', 'laporanRekapitulasi')->name('laporan-rekapitulasi');
        Route::get('/laporan/export/excel', 'exportExcel')->name('export.excel');
        Route::get('/laporan/export/pdf', 'exportPdf')->name('export.pdf');

        // Manajemen Akun
        Route::get('/manajemen-akun', 'manajemenAkun')->name('manajemen-akun');
        Route::post('/manajemen-akun', 'storeAkun')->name('manajemen-akun.store');
        Route::put('/manajemen-akun/{user}', 'updateAkun')->name('manajemen-akun.update');
        Route::delete('/manajemen-akun/{user}', 'destroyAkun')->name('manajemen-akun.destroy');
        Route::get('/manajemen-akun/{user}/rfid', 'daftarkanRFID')->name('manajemen-akun.rfid');
    });

    // ----------------------------------------------------------------------
    // 3. Kepala Sekolah Routes
    // ----------------------------------------------------------------------
    Route::controller(KepalaSekolahController::class)->prefix('kepala-sekolah')->name('kepala-sekolah.')->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        
        // Laporan & Rekapitulasi
        Route::get('/laporan-rekapitulasi', 'laporanRekapitulasi')->name('laporan-rekapitulasi');
        Route::get('/laporan/export/excel', 'exportExcel')->name('export.excel');
        Route::get('/laporan/export/pdf', 'exportPdf')->name('export.pdf');
    });

});

require __DIR__.'/auth.php';