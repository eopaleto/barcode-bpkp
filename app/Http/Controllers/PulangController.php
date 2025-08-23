<?php

namespace App\Http\Controllers;

use App\Models\Pulang;
use Illuminate\Http\Request;

class PulangController extends Controller
{
    public function show($kode)
    {
        $pulang = Pulang::with('pegawai')->where('kode', $kode)->first();

        if ($pulang) {
            return response()->json(array_merge($pulang->toArray(), ['type' => 'Barang Pulang']));
        }

        return response()->json(['message' => 'Data tidak ditemukan di Barang Pulang'], 404);
    }

    public function konfirmasi($kode)
    {
        $pulang = Pulang::where('kode', $kode)->firstOrFail();
        $pulang->update(['status' => 1]);

        return response()->json(['message' => 'Barang Pulang telah dikonfirmasi']);
    }
}
