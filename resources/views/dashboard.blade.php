@extends('layouts.app')

@section('title', 'Dashboard — SocialHub')
@section('page-title', 'Dashboard')
@section('page-sub', 'Sosyal medya hesaplarını yönet')

@push('styles')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spinner-ring {
    width: 14px; height: 14px;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    display: inline-block;
    animation: spin 0.7s linear infinite;
    vertical-align: middle;
    margin-right: 5px;
    opacity: 0.8;
}

/* Stat Cards */
.stat-card {
    border-radius: 18px;
    border: 1px solid #eff0f6;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: transform 0.18s, box-shadow 0.18s;
    overflow: hidden;
    position: relative;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
.stat-card .stat-accent {
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 18px 18px 0 0;
}
.stat-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.stat-value { font-size: 1.9rem; font-weight: 800; line-height: 1; color: #1e1e2e; }
.stat-label { font-size: 0.78rem; color: #9ca3af; font-weight: 500; margin-top: 3px; }

/* Platform Connection Cards */
.platform-tile {
    border-radius: 18px;
    border: 1.5px solid #eff0f6;
    background: #fff;
    padding: 18px 20px;
    transition: border-color 0.18s, box-shadow 0.18s;
}
.platform-tile.connected { border-color: #d1fae5; background: #f0fdf4; }
.platform-tile:hover { border-color: #c7d2fe; box-shadow: 0 4px 16px rgba(108,99,255,0.08); }
.platform-logo {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}
.connected-dot {
    width: 9px; height: 9px;
    border-radius: 50%;
    background: #22c55e;
    display: inline-block;
    margin-right: 5px;
    box-shadow: 0 0 0 2px rgba(34,197,94,0.2);
}

/* Post Feed */
.post-row {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #eff0f6;
    padding: 16px 20px;
    margin-bottom: 10px;
    transition: box-shadow 0.18s, transform 0.18s;
}
.post-row:hover {
    box-shadow: 0 4px 18px rgba(0,0,0,0.07);
    transform: translateY(-1px);
}
.post-media-thumb {
    width: 48px; height: 48px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #eff0f6;
}
.post-media-video {
    width: 48px; height: 48px;
    border-radius: 10px;
    background: #1e1e2e;
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,0.7);
    font-size: 1.1rem;
    border: 1px solid #eff0f6;
}

/* Status badges */
.status-pill {
    display: inline-flex; align-items: center;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 0.73rem;
    font-weight: 600;
    letter-spacing: 0.1px;
}
.status-published { background: #dcfce7; color: #166534; }
.status-publishing { background: #fef3c7; color: #92400e; }
.status-failed { background: #fee2e2; color: #991b1b; }
.status-draft { background: #f3f4f6; color: #6b7280; }

/* Progress bar */
.progress-wrap { background: #f3f4f6; border-radius: 100px; height: 5px; overflow: hidden; }
.progress-fill {
    height: 100%;
    border-radius: 100px;
    background: linear-gradient(90deg, #6c63ff, #a78bfa);
    transition: width 0.4s ease;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-icon {
    width: 80px; height: 80px;
    background: #f3f4f6;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: #d1d5db;
    margin: 0 auto 20px;
}

/* Section header */
.section-head {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #9ca3af;
    margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.section-head i { color: #6c63ff; font-size: 0.95rem; }
</style>
@endpush

@section('content')

{{-- Welcome Banner --}}
<div class="sh-card p-4 mb-4" style="background:linear-gradient(135deg,#0f0c29,#302b63,#24243e);border:none;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-white mb-1">Merhaba, {{ auth()->user()->name }}! 👋</h4>
            <p class="mb-0" style="color:rgba(255,255,255,0.6);font-size:0.9rem;">
                Bugün ne paylaşmak istiyorsun? Tüm platformların seni bekliyor.
            </p>
        </div>
        <a href="{{ route('posts.create') }}"
           class="btn fw-semibold px-4 py-2"
           style="background:#6c63ff;color:#fff;border:none;border-radius:12px;font-size:0.875rem;box-shadow:0 4px 14px rgba(108,99,255,0.4);">
            <i class="bi bi-plus-lg me-1"></i> Yeni Post Oluştur
        </a>
    </div>
</div>

{{-- İstatistikler --}}
@php
    $totalPosts        = $posts->total();
    $publishedPosts    = \App\Models\Post::where('user_id', auth()->id())->where('status', 'published')->count();
    $failedPosts       = \App\Models\Post::where('user_id', auth()->id())->where('status', 'failed')->count();
    $connectedAccounts = $accounts->count();
    $stats = [
        ['label'=>'Toplam Post',    'value'=>$totalPosts,        'icon'=>'file-earmark-text-fill', 'color'=>'#6c63ff', 'bg'=>'#ede9ff', 'accent'=>'#6c63ff'],
        ['label'=>'Yayınlandı',     'value'=>$publishedPosts,    'icon'=>'check-circle-fill',      'color'=>'#22c55e', 'bg'=>'#dcfce7', 'accent'=>'#22c55e'],
        ['label'=>'Başarısız',      'value'=>$failedPosts,       'icon'=>'x-circle-fill',          'color'=>'#ef4444', 'bg'=>'#fee2e2', 'accent'=>'#ef4444'],
        ['label'=>'Bağlı Hesap',    'value'=>$connectedAccounts, 'icon'=>'person-check-fill',      'color'=>'#f59e0b', 'bg'=>'#fef3c7', 'accent'=>'#f59e0b'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($stats as $stat)
    <div class="col-6 col-xl-3">
        <div class="stat-card p-4">
            <div class="stat-accent" style="background:{{ $stat['accent'] }};"></div>
            <div class="d-flex align-items-center gap-3 mt-1">
                <div class="stat-icon-wrap" style="background:{{ $stat['bg'] }};">
                    <i class="bi bi-{{ $stat['icon'] }}" style="color:{{ $stat['color'] }};"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stat['value'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Bağlı Platformlar --}}
@php
    $platformDefs = [
        'instagram' => ['icon' => 'instagram', 'color' => '#e1306c', 'bg' => '#fff0f5', 'label' => 'Instagram'],
        'twitter'   => ['icon' => 'twitter-x', 'color' => '#000',    'bg' => '#f5f5f5', 'label' => 'X (Twitter)'],
        'tiktok'    => ['icon' => 'tiktok',    'color' => '#010101', 'bg' => '#f0f0f0', 'label' => 'TikTok'],
    ];
@endphp

<div class="mb-4">
    <div class="section-head"><i class="bi bi-share-fill"></i> Bağlı Platformlar</div>
    <div class="row g-3">
        @foreach($platformDefs as $key => $info)
        @php $account = $accounts->where('platform', $key)->first(); @endphp
        <div class="col-md-4">
            <div class="platform-tile {{ $account ? 'connected' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="platform-logo" style="background:{{ $info['bg'] }};">
                        <i class="bi bi-{{ $info['icon'] }}" style="color:{{ $info['color'] }};"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold" style="font-size:0.9rem;">{{ $info['label'] }}</div>
                        @if($account)
                            <div style="font-size:0.78rem;color:#16a34a;font-weight:600;margin-top:2px;">
                                <span class="connected-dot"></span>{{ '@'.$account->platform_username }}
                            </div>
                        @else
                            <div style="font-size:0.78rem;color:#9ca3af;margin-top:2px;">Bağlı değil</div>
                        @endif
                    </div>
                    <div class="flex-shrink-0">
                        @if($account)
                            <form method="POST" action="{{ route('social.destroy', $account) }}" class="sh-confirm-form">
                                @csrf @method('DELETE')
                                <button type="button"
                                        class="btn btn-sm sh-confirm-btn"
                                        data-msg="{{ $info['label'] }} hesabının bağlantısı kaldırılsın mı?"
                                        data-title="Hesabı Kaldır"
                                        data-confirm-text="Evet, Kaldır"
                                        data-icon="link-45deg"
                                        style="background:#fee2e2;color:#dc2626;border:none;border-radius:9px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:0.85rem;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('social.redirect', $key) }}"
                               class="btn btn-sm fw-semibold"
                               style="background:#ede9ff;color:#6c63ff;border:none;border-radius:9px;font-size:0.78rem;padding:6px 14px;">
                                Bağla
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Son Postlar --}}
<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="section-head mb-0"><i class="bi bi-clock-history"></i> Son Postlar</div>
        <span style="font-size:0.78rem;color:#9ca3af;">{{ $posts->total() }} post</span>
    </div>

    @forelse($posts as $post)
    <div class="post-row">
        <div class="d-flex align-items-start gap-3">

            {{-- Medya thumb --}}
            <div class="flex-shrink-0">
                @if($post->media->isNotEmpty())
                    @if($post->media->first()->file_type === 'image')
                        <img src="{{ asset('storage/'.$post->media->first()->file_path) }}" class="post-media-thumb" alt="">
                    @else
                        <div class="post-media-video"><i class="bi bi-play-circle-fill"></i></div>
                    @endif
                @else
                    <div class="post-media-video" style="background:#f3f4f6;color:#d1d5db;">
                        <i class="bi bi-file-text"></i>
                    </div>
                @endif
            </div>

            {{-- İçerik --}}
            <div class="flex-grow-1 min-w-0">

                {{-- Üst satır: platformlar + durum --}}
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    @foreach($post->platforms as $platform)
                        <span class="platform-badge badge-{{ $platform }}">
                            <i class="bi bi-{{ $platform === 'twitter' ? 'twitter-x' : $platform }}"></i>
                            {{ $platform === 'twitter' ? 'X' : ucfirst($platform) }}
                        </span>
                    @endforeach

                    @php
                        $sc = match($post->status) {
                            'published'  => ['cls'=>'status-published',  'label'=>'Yayınlandı',      'spinner'=>false],
                            'publishing' => ['cls'=>'status-publishing', 'label'=>'Yayınlanıyor...', 'spinner'=>true],
                            'failed'     => ['cls'=>'status-failed',     'label'=>'Başarısız',       'spinner'=>false],
                            default      => ['cls'=>'status-draft',      'label'=>'Taslak',          'spinner'=>false],
                        };
                    @endphp
                    <span class="status-pill {{ $sc['cls'] }}">
                        @if($sc['spinner'])<span class="spinner-ring"></span>@endif
                        {{ $sc['label'] }}
                        @if($post->scheduled_at && $post->status === 'draft')
                            — {{ $post->scheduled_at->format('d.m.Y H:i') }}
                        @endif
                    </span>
                </div>

                {{-- Progress bar --}}
                @if($post->status === 'publishing')
                <div class="post-progress mb-2" data-post-id="{{ $post->id }}">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="progress-message" style="font-size:0.75rem;color:#9ca3af;">Hazırlanıyor...</span>
                        <span class="progress-percent" style="font-size:0.75rem;color:#6c63ff;font-weight:700;">0%</span>
                    </div>
                    <div class="progress-wrap">
                        <div class="progress-fill" style="width:0%;"></div>
                    </div>
                </div>
                @endif

                {{-- Başlık + İçerik --}}
                @if($post->title)
                    <div class="fw-semibold text-truncate mb-1" style="font-size:0.875rem;">{{ $post->title }}</div>
                @endif
                @if($post->content)
                    <div class="text-truncate" style="font-size:0.82rem;color:#6b7280;max-width:520px;">{{ $post->content }}</div>
                @endif

                {{-- Platform sonuçları --}}
                @if($post->results->isNotEmpty())
                <div class="d-flex gap-2 flex-wrap mt-2">
                    @foreach($post->results as $result)
                    @php
                        $ico = $result->status === 'success' ? 'check-circle-fill' : ($result->status === 'failed' ? 'x-circle-fill' : 'hourglass-split');
                        $clr = $result->status === 'success' ? '#22c55e' : ($result->status === 'failed' ? '#ef4444' : '#f59e0b');
                    @endphp
                    <span style="font-size:0.75rem;color:{{ $clr }};font-weight:600;">
                        <i class="bi bi-{{ $ico }}"></i>
                        {{ ucfirst($result->platform === 'twitter' ? 'X' : $result->platform) }}
                    </span>
                    @endforeach
                </div>
                @endif

                <div style="font-size:0.75rem;color:#9ca3af;margin-top:6px;">
                    <i class="bi bi-clock me-1"></i>{{ $post->created_at->diffForHumans() }}
                </div>
            </div>

            {{-- Sil butonu --}}
            <form method="POST" action="{{ route('posts.destroy', $post) }}" class="sh-confirm-form flex-shrink-0">
                @csrf @method('DELETE')
                <button type="button"
                        class="btn btn-sm sh-confirm-btn"
                        data-msg="Bu post kalıcı olarak silinecek. Emin misin?"
                        data-title="Postu Sil"
                        data-confirm-text="Evet, Sil"
                        data-icon="trash"
                        style="background:#f3f4f6;color:#9ca3af;border:none;border-radius:9px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:0.85rem;"
                        onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626';"
                        onmouseout="this.style.background='#f3f4f6';this.style.color='#9ca3af';">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="sh-card">
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <div class="fw-bold mb-1" style="font-size:0.95rem;">Henüz hiç post yok</div>
            <p style="color:#9ca3af;font-size:0.85rem;margin-bottom:20px;">İlk postunu oluşturmak için aşağıya tıkla.</p>
            <a href="{{ route('posts.create') }}" class="btn fw-semibold px-4 py-2"
               style="background:linear-gradient(135deg,#6c63ff,#5a52d5);color:#fff;border:none;border-radius:12px;font-size:0.875rem;">
                <i class="bi bi-plus-lg me-1"></i> Yeni Post Oluştur
            </a>
        </div>
    </div>
    @endforelse

    @if($posts->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Özel onay modalı — tüm .sh-confirm-btn butonları
document.addEventListener('click', e => {
    const btn = e.target.closest('.sh-confirm-btn');
    if (!btn) return;
    const form = btn.closest('.sh-confirm-form');
    SH.confirm(
        btn.dataset.msg || 'Bu işlemi yapmak istediğinden emin misin?',
        {
            title:       btn.dataset.title       || 'Emin misin?',
            confirmText: btn.dataset.confirmText || 'Evet',
            icon:        btn.dataset.icon        || 'trash',
            type:        'danger',
        },
        () => form.submit()
    );
});

document.querySelectorAll('.post-progress').forEach(el => {
    const postId = el.dataset.postId;
    const fill = el.querySelector('.progress-fill');
    const msg  = el.querySelector('.progress-message');
    const pct  = el.querySelector('.progress-percent');

    const poll = () => {
        fetch(`/posts/${postId}/progress`)
            .then(r => r.json())
            .then(data => {
                if (data.percent != null) {
                    fill.style.width = data.percent + '%';
                    pct.textContent = data.percent + '%';
                }
                if (data.message) msg.textContent = data.message;
                if (data.status === 'published' || data.status === 'failed') {
                    setTimeout(() => location.reload(), 1000);
                    return;
                }
                setTimeout(poll, 1500);
            })
            .catch(() => setTimeout(poll, 3000));
    };
    poll();
});
</script>
@endpush
