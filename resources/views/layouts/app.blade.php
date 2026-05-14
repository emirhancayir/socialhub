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
        body { background: #f0f2f5; font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; }

        /* ── Sidebar ─────────────────────────────────── */
        .sidebar {
            width: 240px; min-height: 100vh;
            background: linear-gradient(160deg, #0f0c29, #302b63, #24243e);
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 100; box-shadow: 4px 0 24px rgba(0,0,0,0.25);
        }
        .sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex; align-items: center; gap: 11px;
        }
        .sidebar-logo .logo-box {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            border-radius: 11px; display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; box-shadow: 0 4px 14px rgba(108,99,255,0.45); flex-shrink: 0;
        }
        .sidebar-logo span { font-size: 1.2rem; font-weight: 800; color: #fff; letter-spacing: -.3px; }
        .sidebar-logo .version {
            font-size: 0.65rem; font-weight: 600;
            background: rgba(108,99,255,0.35); color: rgba(255,255,255,0.7);
            border-radius: 20px; padding: 1px 7px; margin-left: 2px;
        }

        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-section-label {
            font-size: 0.68rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.3); padding: 8px 10px 4px;
        }
        .nav-item-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,0.6); text-decoration: none;
            font-size: 0.875rem; font-weight: 500;
            transition: all 0.18s; margin-bottom: 2px;
        }
        .nav-item-link:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .nav-item-link.active { color: #fff; background: rgba(108,99,255,0.38); }
        .nav-item-link i { font-size: 1rem; width: 18px; text-align: center; flex-shrink: 0; }

        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .user-box {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.05); border-radius: 12px;
            padding: 10px 12px; margin-bottom: 10px;
        }
        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #6c63ff, #5a52d5);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: 0.85rem; flex-shrink: 0;
        }
        .user-info .name { font-size: 0.85rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .user-info .email { font-size: 0.72rem; color: rgba(255,255,255,0.45); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px; }
        .logout-btn {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: 8px 12px;
            background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.09); border-radius: 9px;
            font-size: 0.8rem; font-weight: 500; cursor: pointer; transition: all 0.18s;
        }
        .logout-btn:hover { background: rgba(220,53,69,0.2); color: #ff6b6b; border-color: rgba(220,53,69,0.3); }

        /* ── Main ────────────────────────────────────── */
        .main-content { margin-left: 240px; padding: 28px 32px; min-height: 100vh; }

        /* ── Alerts ──────────────────────────────────── */
        .alert { border: none; border-radius: 12px; font-size: 0.875rem; padding: 12px 16px; }
        .alert-success { background: #ecfdf5; color: #065f46; }
        .alert-danger  { background: #fef2f2; color: #991b1b; }

        /* ── Platform badges ─────────────────────────── */
        .platform-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 0.77rem; font-weight: 600;
        }
        .badge-instagram { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color:#fff; }
        .badge-twitter   { background: #000; color: #fff; }
        .badge-tiktok    { background: #111; color: #fff; }

        /* ── Cards ───────────────────────────────────── */
        .sh-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .post-card { transition: transform 0.18s, box-shadow 0.18s; }
        .post-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.09) !important; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-box"><i class="bi bi-broadcast text-white"></i></div>
        <div class="d-flex align-items-baseline gap-1">
            <span>SocialHub</span>
            <span class="version">v1.0</span>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Menü</div>
        <a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
        <a href="{{ route('posts.create') }}" class="nav-item-link {{ request()->routeIs('posts.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill"></i> Yeni Post
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-box">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="email">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
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
