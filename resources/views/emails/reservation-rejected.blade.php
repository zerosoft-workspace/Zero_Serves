<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rezervasyon Talebi</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .reservation-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; color: #666; }
        .value { color: #333; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Rezervasyon Talebi Hakkında</h1>
        </div>
        
        <div class="content">
            <p>Sayın <strong>{{ $reservation->name }}</strong>,</p>
            
            <p>{{ $reservation->date->format('d.m.Y') }} tarihli rezervasyon talebiniz hakkında bilgilendirme yapmak istiyoruz.</p>
            
            <div class="reservation-details">
                <h3>Rezervasyon Detayları</h3>
                
                <div class="detail-row">
                    <span class="label">Tarih:</span>
                    <span class="value">{{ $reservation->date->format('d.m.Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Saat:</span>
                    <span class="value">{{ $reservation->time->format('H:i') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Kişi Sayısı:</span>
                    <span class="value">{{ $reservation->people }} kişi</span>
                </div>
            </div>
            
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <strong>Üzgünüz, rezervasyon talebinizi şu anda karşılayamıyoruz.</strong>
            </div>
            
            @if($reservation->admin_note)
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <strong>Açıklama:</strong><br>
                {{ $reservation->admin_note }}
            </div>
            @endif
            
            <p><strong>Alternatif Öneriler:</strong></p>
            <ul>
                <li>Farklı bir tarih ve saat için yeni rezervasyon talebinde bulunabilirsiniz</li>
                <li>Daha az kişi sayısı ile rezervasyon deneyebilirsiniz</li>
                <li>Direkt olarak restoranımızı arayarak müsaitlik durumunu öğrenebilirsiniz</li>
            </ul>
            
            <p>Anlayışınız için teşekkür eder, size hizmet verebilmek için farklı tarihlerde bekliyoruz.</p>
            
            <p>Saygılarımızla,<br>
            <strong>{{ config('app.name') }} Ekibi</strong></p>
        </div>
        
        <div class="footer">
            <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayın.</p>
        </div>
    </div>
</body>
</html>
