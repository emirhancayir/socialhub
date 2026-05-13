<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SocialHub')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: linear-gradient(160deg, #0f0c29, #302b63, #24243e);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.2);
        }
        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo span {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .sidebar-logo .logo-icon {
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            border-radius: 10px;
            width: 36px; height: 36px;
            display: inline-flex;
            align-items: center; justify-content: center;
            margin-right: 10px;
        }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .sidebar-nav .nav-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.35);
            padding: 8px 10px 4px;
        }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 2px;
            transition: all 0.2s;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-nav .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
        }
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(108,99,255,0.4);
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        .sidebar-user .avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 0.8rem;
        }

        /* Main content */
        .main-content { margin-left: 240px; padding: 28px 32px; min-height: 100vh; }

        /* Platform badges */
        .platform-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 600;
        }
        .badge-instagram { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color:#fff; }
        .badge-twitter   { background: #000; color: #fff; }
        .badge-tiktok    { background: #111; color: #fff; }

        /* Post cards */
        .post-card { transition: transform 0.2s, box-shadow 0.2s; }
        .post-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important; }

        /* Alert */
        .alert { border: none; border-radius: 12px; font-size: 0.9rem; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <div class="d-flex align-items-center">
            <div class="logo-icon"><i class="bi bi-broadcast text-white"></i></div>
            <span>SocialHub</span>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-label">Menü</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="{{ route('posts.create') }}" class="nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill"></i> Yeni Post
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div style="font-weight:600;">{{ auth()->user()->name }}</div>
                <div style="font-size:0.75rem;opacity:0.6;">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm w-100"
                style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.1);border-radius:8px;">
                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
