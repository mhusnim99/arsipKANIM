<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\CekPermohonanController;
use App\Http\Controllers\HomeController;
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

    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.pengiriman');
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
        Route::get('/dashboard', function () {
            abort_unless(Auth::user()->role === 'admin', 403);
            return view('admin.dashboard');
        })->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Manajemen Lemari (Lokasi Arsip)
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
            Route::put('/loker/{id}/kapasitas', [ManajemenLokasiController::class, 'updateKapasitasLoker'])->name('loker.kapasitas');

            // Loker (ADMIN ONLY)
            Route::put('/loker/{id}', [ManajemenLokasiController::class, 'updateLoker'])
                ->name('loker.update');

            Route::get('/{lemariId}/lokers-kosong', [ManajemenLokasiController::class, 'getLokerKosong'])
                ->name('loker.kosong');
            Route::get(
                'loker/{id}/detail',
                [LokerController::class, 'detail']
            )->name('loker.detail');
        });

        /*
        |--------------------------------------------------------------------------
        | Penerimaan Arsip
        |--------------------------------------------------------------------------
        */
        Route::prefix('penerimaan-arsip')->name('penerimaan-arsip.')->group(function () {
                Route::get('/', [PenerimaanArsipController::class, 'index'])->name('index');
                Route::post('{id}/terima', [PenerimaanArsipController::class, 'terima'])->name('terima');
                Route::post('{id}/tolak', [PenerimaanArsipController::class, 'tolak'])->name('tolak');
                Route::get('{id}/detail', [PenerimaanArsipController::class, 'show'])->name('detail');

                // ✅ INI YANG DIPAKAI DROPDOWN
                Route::get(
                    'lemari/{lemari}/lokers',
                    [PenerimaanArsipController::class, 'getLokersByLemari']
                )->name('lokers');

                // generate nomor arsip
                Route::get(
                    'loker/{loker}/generate-nomor-arsip',
                    [LokerController::class, 'generateNomorArsip']
                )->name('generate-nomor-arsip');
            });


        // Arsip
        Route::get('/arsip', function () {
            abort_unless(Auth::user()->role === 'admin', 403);
            return view('admin.arsip.index');
        })->name('arsip.index');

        Route::prefix('arsip')
            ->name('arsip.')
            ->group(function () {

                // LIST + SEARCH
                Route::get('/', [ArsipController::class, 'index'])
                    ->name('index');

                // DETAIL ARSIP
                Route::get('/{arsip}', [ArsipController::class, 'show'])
                    ->name('show');

                // (NEXT)
                //Pinjam Arsip
                Route::post('/{arsip}/pinjam', [PenerimaanArsipController::class, 'pinjam'])->name('pinjam');
                // Musnahkan Arsip
                Route::post('/{arsip}/musnah', [PenerimaanArsipController::class, 'musnah'])->name('musnah');
            });
    });

    /*
    |--------------------------------------------------------------------------
    | USER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/pengiriman', [PengirimanBerkasController::class, 'index'])->name('pengiriman');
        Route::post('/pengiriman/store', [PengirimanBerkasController::class, 'store'])->name('pengiriman.store');
        Route::get('/pengiriman/riwayat', [PengirimanBerkasController::class, 'riwayat'])->name('pengiriman-riwayat');
        Route::post('/pengiriman/fetch-simkim', [PengirimanBerkasController::class, 'fetchSimkim'])->name('pengiriman.fetch-simkim');
        Route::get('/user/pengiriman/check-status/{id}', [PengirimanBerkasController::class, 'checkStatus'])->name('user.pengiriman.check-status');
    });

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    */
    Route::fallback(function () {
        return redirect('/');
    });
});
