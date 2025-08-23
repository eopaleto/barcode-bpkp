<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PergiController;
use App\Http\Controllers\PulangController;
use App\Http\Controllers\BarcodeController;

Route::get('/', function () {
    return redirect('/barcode');
});

Route::get('/barcode/print/pergi/{pergi}', [BarcodeController::class, 'printPergi'])->name('barcode.print.pergi');
Route::get('/barcode/print/pulang/{pulang}', [BarcodeController::class, 'printPulang'])->name('barcode.print.pulang');

Route::prefix('api')->group(function () {
    // Pergi
    Route::get('/pergi/{kode}', [PergiController::class, 'show']);
    Route::post('/pergi/{kode}/ambil', [PergiController::class, 'ambil']);

    // Pulang
    Route::get('/pulang/{kode}', [PulangController::class, 'show']);
    Route::post('/pulang/{kode}/konfirmasi', [PulangController::class, 'konfirmasi']);
});
