<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol — SocialHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f0f2f8;
        }

        .auth-left {
            width: 44%;
            background: linear-gradient(145deg, #0f0c29 0%, #302b63 55%, #24243e 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 52px;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(108,99,255,0.25), transparent 65%);
            top: -150px; right: -150px;
            border-radius: 50%;
            pointer-events: none;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(167,139,250,0.15), transparent 65%);
            bottom: -100px; left: -80px;
            border-radius: 50%;
            pointer-events: none;
        }
        .auth-left-inner { position: relative; z-index: 1; width: 100%; }

        .brand-wrap { display: flex; align-items: center; gap: 14px; margin-bottom: 40px; }
        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #6c63ff, #a78bfa);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            box-shadow: 0 8px 24px rgba(108,99,255,0.4);
            flex-shrink: 0;
        }
        .brand-name { font-size: 1.5rem; font-weight: 800; color: #fff; }
        .brand-tag { font-size: 0.6rem; font-weight: 700; background: rgba(108,99,255,0.4); color: rgba(255,255,255,0.7); padding: 2px 8px; border-radius: 20px; }

        .auth-tagline { font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: 10px; }
        .auth-sub { color: rgba(255,255,255,0.55); font-size: 0.9rem; line-height: 1.65; margin-bottom: 36px; }

        .step-list { display: flex; flex-direction: column; gap: 0; }
        .step-item {
            display: flex; align-items: flex-start; gap: 16px;
            padding: 16px 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.8);
        }
        .step-item:last-child { border-bottom: none; }
        .step-num {
            width: 30px; height: 30px;
            background: rgba(108,99,255,0.45);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.8rem; color: #fff;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .step-title { font-weight: 700; font-size: 0.9rem; margin-bottom: 3px; }
        .step-desc { font-size: 0.8rem; color: rgba(255,255,255,0.5); }

        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: #f0f2f8;
        }
        .auth-box { width: 100%; max-width: 420px; }
        .auth-box h2 { font-size: 1.7rem; font-weight: 800; color: #1e1e2e; margin-bottom: 6px; }
        .auth-box .auth-desc { color: #9ca3af; font-size: 0.9rem; margin-bottom: 28px; }

        .field-label { font-weight: 600; font-size: 0.82rem; color: #374151; margin-bottom: 6px; display: block; }
        .input-wrap { position: relative; }
        .input-wrap .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #c4c7d0; font-size: 1rem;
            pointer-events: none;
        }
        .input-wrap input {
            width: 100%;
            border: 2px solid #e8eaf0;
            border-radius: 12px;
            padding: 12px 14px 12px 42px;
            font-size: 0.875rem;
            font-family: inherit;
            background: #fff;
            color: #1e1e2e;
            transition: border-color 0.15s;
            outline: none;
        }
        .input-wrap input:focus { border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,0.1); }
        .input-wrap input::placeholder { color: #c4c7d0; }

        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            font-size: 0.925rem;
            padding: 13px;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            box-shadow: 0 4px 14px rgba(108,99,255,0.35);
            font-family: inherit;
        }
        .btn-register:hover { opacity: 0.92; transform: translateY(-1px); }

        .auth-footer-link { text-align: center; font-size: 0.875rem; color: #9ca3af; }
        .auth-footer-link a { color: #6c63ff; font-weight: 600; text-decoration: none; }
        .auth-footer-link a:hover { text-decoration: underline; }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) { .auth-left { display: none; } }
    </style>
</head>
<body>
    <div class="auth-left">
        <div class="auth-left-inner">
            <div class="brand-wrap">
                <div class="brand-icon"><i class="bi bi-broadcast text-white"></i></div>
                <div>
                    <div class="brand-name">SocialHub</div>
                    <div class="brand-tag">v1.0 BETA</div>
                </div>
            </div>

            <h1 class="auth-tagline">SocialHub'a<br>katıl, ücretsiz</h1>
            <p class="auth-sub">Birkaç adımda hesabını oluştur,<br>hemen kullanmaya başla.</p>

            <div class="step-list">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div>
                        <div class="step-title">Hesabını oluştur</div>
                        <div class="step-desc">Ad, e-posta ve şifrenle hızlıca kayıt ol</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div>
                        <div class="step-title">Sosyal hesaplarını bağla</div>
                        <div class="step-desc">TikTok, Instagram ve X hesaplarını ekle</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-title">Paylaşmaya başla</div>
                        <div class="step-desc">Tek tıkla tüm platformlara aynı anda post at</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-right">
        <div class="auth-box">
            <h2>Hesap Oluştur</h2>
            <p class="auth-desc">Ücretsiz hesabını şimdi oluştur</p>

            @if($errors->any())
                <div class="error-box">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="field-label">Ad Soyad</label>
                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="name" placeholder="Adın Soyadın"
                               value="{{ old('name') }}" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="field-label">E-posta</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="ornek@email.com"
                               value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="field-label">Şifre</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="field-label">Tekrar</label>
                        <div class="input-wrap">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" name="password_confirmation" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-register mb-4">
                    <i class="bi bi-person-plus me-2"></i>Kayıt Ol
                </button>
            </form>

            <div class="auth-footer-link">
                Zaten hesabın var mı? <a href="{{ route('login') }}">Giriş Yap</a>
            </div>
        </div>
    </div>
</body>
</html>
