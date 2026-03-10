<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\BeritaAcaraController;
use App\Http\Controllers\CekPermohonanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokerController;
use App\Http\Controllers\ManajemenLokasiController;
use App\Http\Controllers\PenerimaanArsipController;
use App\Http\Controllers\PengirimanBerkasController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Root Redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {

    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role === 'petugas_arsip') {
        return redirect()->route('admin.penerimaan-arsip.index');
    }

    if ($user->role === 'user') {
        return redirect()->route('user.pengiriman');
    }

    return redirect()->route('login');

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Manajemen Lemari
        |--------------------------------------------------------------------------
        */
        Route::prefix('manajemen-lemari')->name('manajemen-lemari.')->group(function () {

            Route::get('/', [ManajemenLokasiController::class, 'index'])->name('index');
            Route::get('/create', [ManajemenLokasiController::class, 'create'])->name('create');
            Route::post('/', [ManajemenLokasiController::class, 'store'])->name('store');
            Route::get('/{id}', [ManajemenLokasiController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ManajemenLokasiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ManajemenLokasiController::class, 'update'])->name('update');
            Route::delete('/{id}', [ManajemenLokasiController::class, 'destroy'])->name('destroy');

            Route::put('/loker/{id}/kapasitas', [ManajemenLokasiController::class, 'updateKapasitasLoker'])
                ->name('loker.kapasitas');

            Route::put('/loker/{id}', [ManajemenLokasiController::class, 'updateLoker'])
                ->name('loker.update');

            Route::get('/{lemariId}/lokers-kosong', [ManajemenLokasiController::class, 'getLokerKosong'])
                ->name('loker.kosong');

            Route::get('/loker/{id}/detail', [LokerController::class, 'show'])
                ->name('loker.show');
        });

        /*
        |--------------------------------------------------------------------------
        | PENERIMAAN ARSIP
        |--------------------------------------------------------------------------
        */
        Route::prefix('penerimaan-arsip')->name('penerimaan-arsip.')->group(function () {

            Route::get('/', [PenerimaanArsipController::class, 'index'])->name('index');

            Route::post('/{id}/terima', [PenerimaanArsipController::class, 'terima'])->name('terima');

            Route::post('/{id}/tolak', [PenerimaanArsipController::class, 'tolak'])->name('tolak');

            Route::get('/{id}/detail', [PenerimaanArsipController::class, 'show'])->name('detail');

            Route::get('/lemari/{lemari}/lokers', [PenerimaanArsipController::class, 'getLokersByLemari'])
                ->name('lokers');

            Route::get('/loker/{loker}/generate-nomor-arsip', [LokerController::class, 'generateNomorArsip'])
                ->name('generate-nomor-arsip');
        });

        /*
        |--------------------------------------------------------------------------
        | ARSIP
        |--------------------------------------------------------------------------
        */
        Route::prefix('arsip')->name('arsip.')->group(function () {

            Route::get('/', [ArsipController::class, 'index'])->name('index');

            Route::get('/{arsip}', [ArsipController::class, 'show'])->name('show');

            Route::put('/{arsip}/status', [ArsipController::class, 'update'])->name('update');

            Route::post('/{arsip}/pinjam', [PenerimaanArsipController::class, 'pinjam'])->name('pinjam');

            Route::post('/{arsip}/musnah', [PenerimaanArsipController::class, 'musnah'])->name('musnah');
        });
    });

        /*
    |--------------------------------------------------------------------------
    | PETUGAS ARSIP ROUTES
    |--------------------------------------------------------------------------
    */

    Route::prefix('petugas_arsip')->name('petugas_arsip.')->group(function () {

        Route::get('/penerimaan', [PenerimaanArsipController::class, 'index'])
            ->name('penerimaan');

        Route::post('/penerimaan/{id}/terima', [PenerimaanArsipController::class, 'terima']);

        Route::post('/penerimaan/{id}/tolak', [PenerimaanArsipController::class, 'tolak']);

        Route::get('/arsip', [ArsipController::class, 'index'])
            ->name('index');

        Route::get('/arsip/{arsip}', [ArsipController::class, 'show'])
            ->name('show');

    });

    /*
    |--------------------------------------------------------------------------
    | USER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

        Route::get('/pengiriman', [PengirimanBerkasController::class, 'index'])->name('pengiriman');

        Route::post('/pengiriman/store', [PengirimanBerkasController::class, 'store'])->name('pengiriman.store');

        Route::get('/pengiriman/riwayat', [PengirimanBerkasController::class, 'riwayat'])
            ->name('pengiriman-riwayat');

        Route::get('/pengiriman/check-status/{id}', [PengirimanBerkasController::class, 'checkStatus'])
            ->name('pengiriman.check-status');

        Route::get('/pengiriman/ditolak', [PengirimanBerkasController::class, 'berkasDitolak'])
            ->name('pengiriman.ditolak');

        Route::post('/pengiriman/kirim-perbaikan/{id}', [PengirimanBerkasController::class, 'kirimPerbaikan'])
            ->name('pengiriman.kirim-perbaikan');
        Route::post('/pengiriman/fetch-simkim', [PengirimanBerkasController::class, 'fetchSimkim'])
            ->name('pengiriman.fetch');
        Route::post('/pengiriman/sync/{id}', [PengirimanBerkasController::class, 'kirimDariSync'])
            ->name('user.pengiriman.sync');
        Route::get('/berita-acara', [BeritaAcaraController::class, 'index'])
            ->name('berita-acara.index');
        Route::get('/berita-acara/generate', [BeritaAcaraController::class, 'generate'])
            ->name('berita-acara.generate');
        Route::post('/berita-acara/generate',[BeritaAcaraController::class,'generate'])
            ->name('berita-acara.generate.post');
        Route::get('/berita-acara/{id}', [BeritaAcaraController::class, 'show'])
            ->name('berita-acara.show');
        Route::get('/berita-acara/{id}/pdf', [BeritaAcaraController::class, 'generatePdf'])
            ->name('berita-acara.pdf');
    });
    /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    */
    Route::fallback(function () {
        return redirect('/');
    });
});
