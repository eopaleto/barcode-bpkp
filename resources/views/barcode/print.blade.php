<!DOCTYPE html>
<html>

<head>
    <title>Cetak Barcode Koper</title>
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
            /* pakai cover kalau mau penuh potong */
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        @foreach ($barcodes as $item)
            <div class="sticker">
                <img src="{{ asset($item['barcode']) }}" alt="barcode">
            </div>
        @endforeach
    </div>
</body>

</html>
