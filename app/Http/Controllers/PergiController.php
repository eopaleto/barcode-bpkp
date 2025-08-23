<?php

namespace App\Http\Controllers;

use App\Models\Pergi;
use Illuminate\Http\Request;

class PergiController extends Controller
{
    public function show($kode)
    {
        $pergi = Pergi::with('pegawai')->where('kode', $kode)->first();

        if ($pergi) {
            return response()->json(array_merge($pergi->toArray(), ['type' => 'Barang Pergi']));
        }

        return response()->json(['message' => 'Data tidak ditemukan di Barang Pergi'], 404);
    }

    public function ambil($kode)
    {
        $pergi = Pergi::where('kode', $kode)->firstOrFail();
        $pergi->update(['status' => 1]);

        return response()->json(['message' => 'Barang Pergi telah dikonfirmasi']);
    }
}
