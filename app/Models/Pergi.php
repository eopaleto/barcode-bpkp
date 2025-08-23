<?php

namespace App\Models;

use App\Models\Pegawai;
use Milon\Barcode\DNS2D;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * @mixin IdeHelperPergi
 */
class Pergi extends Model
{
    protected $table = 'pergi';
    protected $guarded = [];
    protected $fillable = ['pegawai_id', 'jumlah_koper', 'foto_koper', 'barcode', 'kode', 'status'];

    protected $casts = [
        'foto_koper' => 'array',
    ];

    protected static function booted()
    {
        // Event create
        static::created(function ($pergi) {
            $pegawai = $pergi->pegawai;
            if (!$pegawai) return;

            $kodeUnik = strtoupper(bin2hex(random_bytes(6)));

            $d = new DNS2D();
            $qrBase64 = $d->getBarcodePNG($kodeUnik, 'QRCODE');
            $qr = Image::make(base64_decode($qrBase64));

            $canvas = Image::canvas(300, 150, '#ffffff');
            $canvas->insert($qr->resize(100, 100), 'left', 10, 25);

            $canvas->text("No Urut: {$pergi->id}", 150, 30, function ($font) {
                $font->size(14);
                $font->color('#000000');
                $font->align('left');
            });
            $canvas->text("Nama: " . ($pegawai->nama ?? '-'), 150, 55, function ($font) {
                $font->size(12);
                $font->color('#000000');
                $font->align('left');
            });
            $canvas->text("NIP: " . ($pegawai->nip_baru ?? '-'), 150, 75, function ($font) {
                $font->size(12);
                $font->color('#000000');
                $font->align('left');
            });
            $canvas->text("Unit: " . ($pegawai->unit_kerja ?? '-'), 150, 95, function ($font) {
                $font->size(12);
                $font->color('#000000');
                $font->align('left');
            });
            $canvas->text("Barcode untuk Pergi", 150, 125, function ($font) {
                $font->size(12);
                $font->color('#FF0000');
                $font->align('left');
            });

            $fileName = 'BRG_' . $pergi->id . '.png';
            $canvas->save(storage_path("app/public/barcode/pergi/{$fileName}"));

            $pergi->updateQuietly([
                'barcode' => $fileName,
                'kode' => $kodeUnik,
                'status' => '0',
            ]);
        });

        static::deleted(function ($pergi) {
            if ($pergi->barcode && Storage::disk('public')->exists('barcode/pergi/' . $pergi->barcode)) {
                Storage::disk('public')->delete('barcode/pergi/' . $pergi->barcode);
            }

            if ($pergi->foto_koper) {
                foreach ($pergi->foto_koper as $foto) {
                    if (Storage::disk('public')->exists($foto)) {
                        Storage::disk('public')->delete($foto);
                    }
                }
            }
        });
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id');
    }
}
