<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş #{{ $order->id }} - Yazdır</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        .order-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-info td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-preparing { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-paid { background: #e2e3e5; color: #383d41; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ZeroServes Restaurant</h1>
        <h2>SİPARİŞ FİŞİ</h2>
    </div>

    <div class="order-info">
        <table>
            <tr>
                <td><strong>Sipariş No:</strong></td>
                <td>#{{ $order->id }}</td>
                <td><strong>Masa:</strong></td>
                <td>{{ $order->table->name ?? 'Masa Yok' }}</td>
            </tr>
            <tr>
                <td><strong>Tarih:</strong></td>
                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                <td><strong>Durum:</strong></td>
                <td>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ 
                            $order->status === 'pending' ? 'Bekliyor' : 
                            ($order->status === 'preparing' ? 'Hazırlanıyor' : 
                            ($order->status === 'delivered' ? 'Teslim Edildi' : 
                            ($order->status === 'paid' ? 'Ödendi' : 'İptal Edildi'))) 
                        }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Ürün</th>
                <th style="text-align: center;">Adet</th>
                <th style="text-align: right;">Birim Fiyat</th>
                <th style="text-align: center;">Stok</th>
                <th style="text-align: right;">Toplam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Ürün Bulunamadı' }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">₺{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: center;">
                        @if($item->product)
                            {{ $item->product->stock_quantity }}/{{ $item->product->max_stock_level }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: right;">₺{{ number_format($item->quantity * $item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;"><strong>TOPLAM TUTAR:</strong></td>
                <td style="text-align: right;"><strong>₺{{ number_format($order->total_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($order->notes)
        <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-left: 4px solid #007bff;">
            <strong>Notlar:</strong><br>
            {{ $order->notes }}
        </div>
    @endif

    <div class="footer">
        <p>Bu fiş {{ now()->format('d.m.Y H:i') }} tarihinde yazdırılmıştır.</p>
        <p>ZeroServes Restaurant Management System</p>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Yazdır
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Kapat
        </button>
    </div>

    <script>
        // Sayfa yüklendiğinde otomatik yazdır
        window.onload = function() {
            // 1 saniye bekle sonra yazdır dialogunu aç
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>
