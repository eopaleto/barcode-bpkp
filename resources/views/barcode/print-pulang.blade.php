<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Pulang - {{ $pulang->pegawai->nama ?? 'Pegawai' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        @page {
            size: 100mm auto;
            /* lebar fix 100mm, tinggi menyesuaikan */
            margin: 5mm;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .barcode-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* 2 kolom */
            gap: 5mm;
        }

        .barcode-item {
            width: 50mm;
            /* 5 cm */
            height: 50mm;
            /* 5 cm */
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #000;
        }

        .barcode-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        @foreach ($barcodes as $item)
            <div class="sticker">
                <img src="{{ asset('storage/koper/pulang/' . $pulang->barcode) }}" alt="barcode">
            </div>
        @endforeach
    </div>
</body>

</html>
