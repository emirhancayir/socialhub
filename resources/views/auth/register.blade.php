<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Social Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; }
        .auth-card { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 2px solid #e9ecef; }
        .form-control:focus { border-color: #6c63ff; box-shadow: 0 0 0 0.2rem rgba(108,99,255,0.25); }
        .btn-primary { background: linear-gradient(135deg, #6c63ff, #5a52d5); border: none; border-radius: 10px; padding: 12px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div style="width: 100%; max-width: 420px; padding: 20px;">
        <div class="text-center mb-4">
            <div style="font-size: 3rem; color: #6c63ff;"><i class="bi bi-broadcast"></i></div>
            <h2 class="text-white fw-bold">SocialHub</h2>
        </div>

        <div class="card auth-card p-4">
            <h4 class="fw-bold mb-4">Kayıt Ol</h4>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ad Soyad</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">E-posta</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Şifre</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Şifre Tekrar</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="bi bi-person-plus"></i> Kayıt Ol
                </button>
            </form>

            <hr>
            <p class="text-center mb-0 text-muted">
                Zaten hesabın var mı? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Giriş Yap</a>
            </p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
