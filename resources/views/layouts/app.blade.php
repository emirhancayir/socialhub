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

        /* ── Toast ── */
        #sh-toast-container {
            position: fixed;
            bottom: 28px; right: 28px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .sh-toast {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 32px rgba(0,0,0,0.14), 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #eff0f6;
            pointer-events: all;
            animation: toastIn 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
            font-size: 0.875rem;
            font-family: inherit;
        }
        .sh-toast.hiding { animation: toastOut 0.25s ease forwards; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(60px) scale(0.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes toastOut {
            from { opacity: 1; transform: translateX(0) scale(1); }
            to   { opacity: 0; transform: translateX(60px) scale(0.95); }
        }
        .sh-toast-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .sh-toast-body { flex: 1; min-width: 0; }
        .sh-toast-title { font-weight: 700; font-size: 0.82rem; margin-bottom: 2px; }
        .sh-toast-msg { color: #6b7280; font-size: 0.82rem; line-height: 1.4; }
        .sh-toast-close {
            background: none; border: none; cursor: pointer;
            color: #9ca3af; font-size: 1rem; padding: 0;
            flex-shrink: 0; line-height: 1;
        }
        .sh-toast-close:hover { color: #374151; }
        .sh-toast-progress {
            position: absolute; bottom: 0; left: 0;
            height: 3px; border-radius: 0 0 14px 14px;
            transition: width linear;
        }
        .sh-toast { position: relative; overflow: hidden; }

        .sh-toast.success .sh-toast-icon { background: #dcfce7; color: #16a34a; }
        .sh-toast.success .sh-toast-title { color: #15803d; }
        .sh-toast.success .sh-toast-progress { background: #22c55e; }

        .sh-toast.error .sh-toast-icon { background: #fee2e2; color: #dc2626; }
        .sh-toast.error .sh-toast-title { color: #dc2626; }
        .sh-toast.error .sh-toast-progress { background: #ef4444; }

        .sh-toast.warning .sh-toast-icon { background: #fef3c7; color: #d97706; }
        .sh-toast.warning .sh-toast-title { color: #b45309; }
        .sh-toast.warning .sh-toast-progress { background: #f59e0b; }

        .sh-toast.info .sh-toast-icon { background: #dbeafe; color: #2563eb; }
        .sh-toast.info .sh-toast-title { color: #1d4ed8; }
        .sh-toast.info .sh-toast-progress { background: #3b82f6; }

        /* ── Confirm Modal ── */
        .sh-modal-overlay {
            position: fixed; inset: 0;
            background: rgba(15,12,41,0.55);
            z-index: 10000;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            animation: fadeIn 0.18s ease;
        }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        .sh-modal {
            background: #fff;
            border-radius: 20px;
            padding: 28px;
            width: 100%; max-width: 400px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            animation: modalIn 0.22s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes modalIn {
            from { opacity:0; transform: scale(0.9); }
            to   { opacity:1; transform: scale(1); }
        }
        .sh-modal-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 16px;
        }
        .sh-modal-icon.danger { background: #fee2e2; color: #dc2626; }
        .sh-modal-icon.warning { background: #fef3c7; color: #d97706; }
        .sh-modal-title {
            font-size: 1rem; font-weight: 800; color: #1e1e2e;
            margin-bottom: 6px;
        }
        .sh-modal-msg { font-size: 0.875rem; color: #6b7280; line-height: 1.5; margin-bottom: 24px; }
        .sh-modal-actions { display: flex; gap: 10px; }
        .sh-modal-cancel {
            flex: 1; padding: 11px;
            background: #f3f4f6; color: #374151;
            border: none; border-radius: 12px;
            font-weight: 600; font-size: 0.875rem;
            cursor: pointer; font-family: inherit;
            transition: background 0.15s;
        }
        .sh-modal-cancel:hover { background: #e5e7eb; }
        .sh-modal-confirm {
            flex: 1; padding: 11px;
            border: none; border-radius: 12px;
            font-weight: 700; font-size: 0.875rem;
            cursor: pointer; font-family: inherit;
            color: #fff;
            transition: opacity 0.15s;
        }
        .sh-modal-confirm.danger { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239,68,68,0.3); }
        .sh-modal-confirm.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .sh-modal-confirm:hover { opacity: 0.9; }

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
        @yield('content')
    </div>
</div>

{{-- Toast Container --}}
<div id="sh-toast-container"></div>

{{-- Confirm Modal --}}
<div id="sh-modal-overlay" class="sh-modal-overlay" style="display:none;" onclick="if(event.target===this) SH._closeModal()">
    <div class="sh-modal">
        <div class="sh-modal-icon danger" id="sh-modal-icon"><i class="bi bi-trash" id="sh-modal-icon-i"></i></div>
        <div class="sh-modal-title" id="sh-modal-title">Emin misin?</div>
        <div class="sh-modal-msg" id="sh-modal-msg"></div>
        <div class="sh-modal-actions">
            <button class="sh-modal-cancel" onclick="SH._closeModal()">Vazgeç</button>
            <button class="sh-modal-confirm danger" id="sh-modal-confirm-btn">Evet, Sil</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.SH = (() => {
    const icons = {
        success: 'check-circle-fill',
        error:   'x-circle-fill',
        warning: 'exclamation-triangle-fill',
        info:    'info-circle-fill',
    };
    const titles = {
        success: 'Başarılı',
        error:   'Hata',
        warning: 'Uyarı',
        info:    'Bilgi',
    };

    function toast(msg, type = 'success', duration = 4500) {
        const container = document.getElementById('sh-toast-container');
        const el = document.createElement('div');
        el.className = `sh-toast ${type}`;
        el.innerHTML = `
            <div class="sh-toast-icon"><i class="bi bi-${icons[type] || icons.info}"></i></div>
            <div class="sh-toast-body">
                <div class="sh-toast-title">${titles[type] || 'Bilgi'}</div>
                <div class="sh-toast-msg">${msg}</div>
            </div>
            <button class="sh-toast-close" onclick="SH._removeToast(this.closest('.sh-toast'))">
                <i class="bi bi-x"></i>
            </button>
            <div class="sh-toast-progress" style="width:100%;"></div>
        `;
        container.appendChild(el);

        const bar = el.querySelector('.sh-toast-progress');
        requestAnimationFrame(() => {
            bar.style.transition = `width ${duration}ms linear`;
            bar.style.width = '0%';
        });

        const timer = setTimeout(() => _removeToast(el), duration);
        el._timer = timer;
        return el;
    }

    function _removeToast(el) {
        if (!el || el._removing) return;
        el._removing = true;
        clearTimeout(el._timer);
        el.classList.add('hiding');
        el.addEventListener('animationend', () => el.remove(), { once: true });
    }

    let _onConfirm = null;

    function confirm(msg, { title = 'Emin misin?', confirmText = 'Evet, Sil', type = 'danger', icon = 'trash' } = {}, onConfirm) {
        document.getElementById('sh-modal-msg').textContent = msg;
        document.getElementById('sh-modal-title').textContent = title;
        document.getElementById('sh-modal-icon').className = `sh-modal-icon ${type}`;
        document.getElementById('sh-modal-icon-i').className = `bi bi-${icon}`;
        const confirmBtn = document.getElementById('sh-modal-confirm-btn');
        confirmBtn.textContent = confirmText;
        confirmBtn.className = `sh-modal-confirm ${type}`;
        _onConfirm = onConfirm;
        document.getElementById('sh-modal-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function _closeModal() {
        document.getElementById('sh-modal-overlay').style.display = 'none';
        document.body.style.overflow = '';
        _onConfirm = null;
    }

    document.getElementById('sh-modal-confirm-btn').addEventListener('click', () => {
        _closeModal();
        if (_onConfirm) _onConfirm();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') _closeModal();
    });

    return { toast, confirm, _removeToast, _closeModal };
})();

// Session flash → toast
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => SH.toast(@json(session('success')), 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => SH.toast(@json(session('error')), 'error'));
@endif
</script>
@stack('scripts')
</body>
</html>
