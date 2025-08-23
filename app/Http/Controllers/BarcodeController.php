<?php

namespace App\Http\Controllers;

use App\Models\Pergi;
use App\Models\Pulang;

class BarcodeController extends Controller
{
    public function printPergi($id)
    {
        $pergi = Pergi::with('pegawai')->findOrFail($id);

        $jumlahKoper = $pergi->jumlah_koper ?? 1;

        $barcodePath = storage_path('app/public/koper/pergi/' . $pergi->barcode);

        $barcodes = [];
        for ($i = 1; $i <= $jumlahKoper; $i++) {
            $barcodes[] = [
                'kode' => $pergi->id . '-' . $i,
                'barcode' => $barcodePath,
            ];
        }

        return view('barcode.print-pergi', compact('pergi', 'barcodes'));
    }

    public function printPulang($id)
    {
        $pulang = Pulang::with('pegawai')->findOrFail($id);

        $jumlahKoper = $pulang->jumlah_koper ?? 1;

        $barcodePath = storage_path('app/public/koper/pulang/' . $pulang->barcode);

        $barcodes = [];
        for ($i = 1; $i <= $jumlahKoper; $i++) {
            $barcodes[] = [
                'kode' => $pulang->id . '-' . $i,
                'barcode' => $barcodePath,
            ];
        }

        return view('barcode.print-pulang', compact('pulang', 'barcodes'));
    }

    public function show($kode)
    {
        $pergi = Pergi::with('pegawai')->where('kode', $kode)->firstOrFail();

        return response()->json($pergi);
    }
}
