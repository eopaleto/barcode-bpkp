<?php

use App\Models\Pergi;
use App\Http\Controllers\Barang;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeController;

Route::get('/', function () {
    return redirect('/barcode');
});

Route::get('/barcode/print/pergi/{pergi}', [BarcodeController::class, 'printPergi'])->name('barcode.print.pergi');
Route::get('/barcode/print/pulang/{pulang}', [BarcodeController::class, 'printPulang'])->name('barcode.print.pulang');

Route::prefix('api')->group(function () {
    Route::get('/pergi/{kode}', [BarcodeController::class, 'show']);
    Route::post('/pergi/{kode}/ambil', function ($kode) {
        $pergi = Pergi::where('kode', $kode)->firstOrFail();
        $pergi->update(['status' => 1]);
        return response()->json(['message' => 'Barang telah diambil']);
    });
});
