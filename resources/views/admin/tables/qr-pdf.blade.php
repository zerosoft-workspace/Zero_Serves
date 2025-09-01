<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Masalar QR' }}</title>
    <style>
        /* Sayfa kenar boşluğu ve yazı tipi */
        @page {
            size: A4;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111;
        }

        .header {
            text-align: center;
            font-weight: 700;
            font-size: 14pt;
            margin: 0 0 6mm;
        }

        /* ==== GRID (flex yok) ==== */
        .cards {
            font-size: 0;
        }

        /* inline-block boşluklarını sıfırla */
        .card-wrap {
            display: inline-block;
            vertical-align: top;
            width: 32%;
            /* 3 sütun */
            margin: 0 2% 8mm 0;
            /* sağ boşluk: son kolonu aşağıda 0 yapacağız */
            page-break-inside: avoid;
            /* kartlar sayfa ortasında bölünmesin */
            font-size: 11pt;
            /* satır içi font reset */
        }

        .card-wrap:nth-child(3n) {
            margin-right: 0;
        }

        /* her 3. kartta sağ boşluk yok */

        /* ==== KART ==== */
        .card {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 6mm;
            padding: 6mm;
            min-height: 60mm;
            /* sabit yükseklik: hizalı görünüm */
        }

        .title {
            font-weight: 700;
            font-size: 12pt;
            margin: 0 0 3mm;
        }

        .qr {
            text-align: center;
            margin: 2mm 0 3mm;
        }

        .qr img {
            width: 36mm;
            height: 36mm;
            display: block;
            margin: 0 auto;
        }

        /* QR boyutu */
        .url {
            font-size: 9pt;
            color: #374151;
            word-break: break-all;
        }

        .token {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 1mm;
        }
    </style>
</head>

<body>
    <div class="header">{{ $title ?? 'Masalar QR' }}</div>

    <div class="cards">
        @foreach($items as $it)
            <div class="card-wrap">
                <div class="card">
                    <div class="title">{{ $it['name'] }}</div>

                    {{-- QR (controller base64 SVG img döndürüyor) --}}
                    <div class="qr">
                        <img src="{{ $it['img'] }}" alt="QR - {{ $it['name'] }}">
                    </div>

                    <div class="url">{{ $it['url'] }}</div>
                    <div class="token">Token: {{ $it['token'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>