<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oturum Süresi Doldu - ZeroServes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .session-expired-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .icon-container {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .countdown {
            font-size: 1.2rem;
            color: #666;
            margin: 1rem 0;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="session-expired-card">
        <div class="icon-container">
            <i class="bi bi-clock-history text-white" style="font-size: 3rem;"></i>
        </div>
        
        <h2 class="mb-3 text-dark">Oturum Süresi Doldu</h2>
        <p class="text-muted mb-4">
            Uzun süre işlem yapılmadığı için güvenlik nedeniyle oturumunuz sonlandırıldı. 
            Lütfen tekrar giriş yapınız.
        </p>
        
        <div class="countdown">
            Ana sayfaya yönlendiriliyorsunuz... <span id="countdown">5</span>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('landing') }}" class="btn-home">
                <i class="bi bi-house-door me-2"></i>
                Ana Sayfaya Dön
            </a>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Güvenliğiniz için oturum süresi 30 dakika ile sınırlıdır
            </small>
        </div>
    </div>

    <script>
        // 5 saniye sonra ana sayfaya yönlendir
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '{{ route("landing") }}';
            }
        }, 1000);
        
        // Sayfa yüklendiğinde localStorage'ı temizle
        localStorage.clear();
        sessionStorage.clear();
    </script>
</body>
</html>
