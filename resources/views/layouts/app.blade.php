<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SocialHub')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #f0f2f8;
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            color: #1e1e2e;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #0f0c29;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 200;
            border-right: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .logo-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #6c63ff, #a78bfa);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(108,99,255,0.4);
        }
        .logo-text { font-size: 1.15rem; font-weight: 800; color: #fff; letter-spacing: -0.3px; }
        .logo-badge {
            font-size: 0.6rem; font-weight: 700;
            background: rgba(108,99,255,0.4);
            color: rgba(255,255,255,0.75);
            border-radius: 20px;
            padding: 2px 7px;
            margin-top: 1px;
        }

        .sidebar-nav {
            padding: 20px 14px;
            flex: 1;
            overflow-y: auto;
        }
        .nav-group-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255,255,255,0.25);
            padding: 4px 10px 8px;
            margin-top: 8px;
        }
        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 12px;
            border-radius: 10px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-link-item i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .nav-link-item:hover {
            color: #fff;
            background: rgba(255,255,255,0.07);
        }
        .nav-link-item.active {
            color: #fff;
            background: rgba(108,99,255,0.3);
            font-weight: 600;
        }
        .nav-link-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 20px;
            background: #6c63ff;
            border-radius: 0 3px 3px 0;
        }
        .nav-link-item .badge-new {
            margin-left: auto;
            background: #6c63ff;
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px 14px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .user-avatar-box {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #6c63ff, #a78bfa);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .user-name { font-size: 0.85rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .user-email {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.4);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }
        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 9px 14px;
            background: transparent;
            color: rgba(255,255,255,0.45);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }
        .logout-link:hover {
            background: rgba(239,68,68,0.15);
            color: #fca5a5;
            border-color: rgba(239,68,68,0.25);
        }

        /* ── Main ── */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e8eaf0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-title { font-size: 1rem; font-weight: 700; color: #1e1e2e; }
        .topbar-sub { font-size: 0.78rem; color: #9ca3af; margin-top: 1px; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-date {
            font-size: 0.8rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .page-body { padding: 28px 32px; flex: 1; }

        /* ── Alerts ── */
        .alert {
            border: none;
            border-radius: 14px;
            font-size: 0.875rem;
            padding: 13px 18px;
            margin-bottom: 20px;
        }
        .alert-success { background: #ecfdf5; color: #065f46; }
        .alert-danger  { background: #fef2f2; color: #991b1b; }

        /* ── Cards ── */
        .sh-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #eff0f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.03);
        }

        /* ── Platform badges ── */
        .platform-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-instagram { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color:#fff; }
        .badge-twitter   { background: #000; color: #fff; }
        .badge-tiktok    { background: #111; color: #fff; }

        /* ── Post cards ── */
        .post-card { transition: transform 0.15s, box-shadow 0.15s; }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.08) !important;
        }

        /* ── Pagination ── */
        .pagination .page-link {
            border: none;
            border-radius: 9px !important;
            margin: 0 2px;
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 7px 13px;
            background: transparent;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            color: #fff;
            box-shadow: 0 3px 10px rgba(108,99,255,0.3);
        }
        .pagination .page-link:hover { background: #f3f4f6; color: #374151; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="bi bi-broadcast text-white"></i></div>
        <div>
            <div class="logo-text">SocialHub</div>
            <div class="logo-badge">v1.0 BETA</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-group-label">Ana Menü</div>
        <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="{{ route('posts.create') }}" class="nav-link-item {{ request()->routeIs('posts.create') ? 'active' : '' }}">
            <i class="bi bi-plus-square-fill"></i> Yeni Post
        </a>

        <div class="nav-group-label">Platformlar</div>
        <a href="{{ route('social.redirect', 'instagram') }}" class="nav-link-item">
            <i class="bi bi-instagram" style="color:#e1306c;"></i> Instagram
        </a>
        <a href="{{ route('social.redirect', 'twitter') }}" class="nav-link-item">
            <i class="bi bi-twitter-x"></i> X (Twitter)
        </a>
        <a href="{{ route('social.redirect', 'tiktok') }}" class="nav-link-item">
            <i class="bi bi-tiktok"></i> TikTok
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar-box">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div style="min-width:0;">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link">
                <i class="bi bi-power"></i> Oturumu Kapat
            </button>
        </form>
    </div>
</div>

<div class="main-wrapper">
    <div class="topbar">
        <div>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-sub">@yield('page-sub', 'Hoş geldin, ' . auth()->user()->name)</div>
        </div>
        <div class="topbar-right">
            <span class="topbar-date"><i class="bi bi-calendar3 me-1"></i>{{ now()->format('d M Y') }}</span>
            <a href="{{ route('posts.create') }}" class="btn btn-sm fw-semibold px-4"
               style="background:linear-gradient(135deg,#6c63ff,#5a52d5);color:#fff;border:none;border-radius:10px;padding:8px 18px;font-size:0.82rem;">
                <i class="bi bi-plus-lg me-1"></i> Yeni Post
            </a>
        </div>
    </div>

    <div class="page-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
