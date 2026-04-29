<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Social Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            position: fixed;
            top: 0; left: 0;
            padding: 20px 0;
            z-index: 100;
        }
        .sidebar .logo {
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            padding: 10px 20px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.1);
            padding-left: 28px;
        }
        .sidebar .nav-link i { width: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .platform-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
        }
        .badge-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: #fff; }
        .badge-twitter   { background: #000; color: #fff; }
        .badge-tiktok    { background: #010101; color: #fff; }
        .badge-facebook  { background: #1877f2; color: #fff; }
        .post-card { transition: transform 0.2s, box-shadow 0.2s; }
        .post-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }
        .status-dot {
            width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="bi bi-broadcast"></i> SocialHub
        </div>
        <nav class="nav flex-column">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('posts.create') }}" class="nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i> Yeni Post
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 20px;">
            <div style="color: rgba(255,255,255,0.4); font-size: 0.75rem; padding: 5px 20px; text-transform: uppercase;">Hesaplar</div>
            @foreach(['instagram','twitter','tiktok'] as $p)
            <a href="{{ route('social.redirect', $p) }}" class="nav-link">
                <i class="bi bi-{{ $p === 'twitter' ? 'twitter-x' : $p }}"></i>
                {{ ucfirst($p === 'twitter' ? 'X (Twitter)' : $p) }}
            </a>
            @endforeach
        </nav>
        <div style="position: absolute; bottom: 20px; width: 100%; padding: 0 20px;">
            <div style="color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-bottom: 10px;">
                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right"></i> Çıkış
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
