<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap — SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; font-family: 'Segoe UI', sans-serif; }
        .auth-left {
            width: 45%; background: linear-gradient(145deg, #0f0c29, #302b63, #24243e);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 60px 48px; position: relative; overflow: hidden;
        }
        .auth-left::before {
            content: ''; position: absolute; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(108,99,255,0.3), transparent 70%);
            top: -100px; right: -100px; border-radius: 50%;
        }
        .auth-left::after {
            content: ''; position: absolute; width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(90,82,213,0.2), transparent 70%);
            bottom: -80px; left: -80px; border-radius: 50%;
        }
        .auth-left-content { position: relative; z-index: 1; color: #fff; }
        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(108,99,255,0.4);
        }
        .auth-left h1 { font-size: 2rem; font-weight: 800; margin-bottom: 12px; }
        .auth-left p { color: rgba(255,255,255,0.65); font-size: 1rem; line-height: 1.6; }
        .feature-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 0; color: rgba(255,255,255,0.8); font-size: 0.9rem;
        }
        .feature-item .fi {
            width: 32px; height: 32px; background: rgba(255,255,255,0.08);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; flex-shrink: 0;
        }
        .auth-right {
            flex: 1; background: #f5f6fa;
            display: flex; align-items: center; justify-content: center; padding: 40px 24px;
        }
        .auth-form-wrap { width: 100%; max-width: 400px; }
        .auth-form-wrap h2 { font-size: 1.6rem; font-weight: 800; color: #1a1a2e; margin-bottom: 6px; }
        .auth-form-wrap .sub { color: #6c757d; font-size: 0.9rem; margin-bottom: 28px; }
        .form-group label { font-weight: 600; font-size: 0.875rem; color: #374151; margin-bottom: 6px; display: block; }
        .form-control {
            border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px 16px;
            font-size: 0.9rem; transition: border-color 0.2s; background: #fff;
        }
        .form-control:focus { border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,0.12); outline: none; }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .input-icon-wrap .form-control { padding-left: 42px; }
        .btn-auth {
            background: linear-gradient(135deg, #6c63ff, #5a52d5); border: none; border-radius: 12px;
            color: #fff; font-weight: 700; font-size: 0.95rem; padding: 13px; width: 100%;
            transition: opacity 0.2s, transform 0.1s; cursor: pointer;
        }
        .btn-auth:hover { opacity: 0.92; transform: translateY(-1px); }
        .auth-footer { text-align: center; color: #6c757d; font-size: 0.875rem; }
        .auth-footer a { color: #6c63ff; font-weight: 600; text-decoration: none; }
        @media (max-width: 768px) { .auth-left { display: none; } }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="brand-icon"><i class="bi bi-broadcast text-white"></i></div>
            <h1>SocialHub</h1>
            <p>Tüm sosyal medya hesaplarını tek bir yerden yönet, planla ve paylaş.</p>
            <div class="mt-4">
                <div class="feature-item"><div class="fi"><i class="bi bi-tiktok"></i></div> TikTok'a otomatik video yükle</div>
                <div class="feature-item"><div class="fi"><i class="bi bi-instagram"></i></div> Instagram'a fotoğraf ve video paylaş</div>
                <div class="feature-item"><div class="fi"><i class="bi bi-twitter-x"></i></div> X (Twitter)'da tweet at</div>
                <div class="feature-item"><div class="fi"><i class="bi bi-calendar-check"></i></div> Postları ileriye zamanlayarak at</div>
            </div>
        </div>
    </div>
    <div class="auth-right">
        <div class="auth-form-wrap">
            <h2>Tekrar hoş geldin 👋</h2>
            <p class="sub">Devam etmek için hesabına giriş yap</p>
            @if($errors->any())
                <div class="alert alert-danger rounded-3 border-0 mb-4" style="background:#fef2f2;color:#dc2626;font-size:.875rem;">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
            @endif
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group mb-4">
                    <label>E-posta</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="ornek@email.com"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label>Şifre</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Beni hatırla</label>
                </div>
                <button type="submit" class="btn-auth mb-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                </button>
            </form>
            <div class="auth-footer">Hesabın yok mu? <a href="{{ route('register') }}">Kayıt Ol</a></div>
        </div>
    </div>
</body>
</html>
