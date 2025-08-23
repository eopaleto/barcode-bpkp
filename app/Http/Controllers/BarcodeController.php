<?php

namespace App\Http\Controllers;

use App\Models\Pergi;

class BarcodeController extends Controller
{
    public function print($id)
    {
        $pergi = Pergi::with('pegawai')->findOrFail($id);

        $jumlahKoper = $pergi->jumlah_koper ?? 1;

        $barcodePath = 'storage/koper/' . $pergi->barcode;

        $barcodes = [];
        for ($i = 1; $i <= $jumlahKoper; $i++) {
            $barcodes[] = [
                'kode' => $pergi->id . '-' . $i,
                'barcode' => $barcodePath,
            ];
        }

        return view('barcode.print', compact('pergi', 'barcodes'));
    }

    public function show($kode)
    {
        $pergi = Pergi::with('pegawai')->where('kode', $kode)->firstOrFail();

        if (! $pergi) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($pergi);
    }
}
