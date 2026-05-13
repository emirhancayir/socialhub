@extends('layouts.app')

@section('title', 'Dashboard - SocialHub')

@push('styles')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spinner-ring {
    width: 16px; height: 16px;
    border: 2px solid #ffc107;
    border-top-color: transparent;
    border-radius: 50%;
    display: inline-block;
    animation: spin 0.8s linear infinite;
    vertical-align: middle;
    margin-right: 4px;
}
.stat-card {
    border-radius: 16px;
    border: none;
    transition: transform 0.2s;
}
.stat-card:hover { transform: translateY(-3px); }
.account-card { border-radius: 16px; border: none; }
.account-card .connected-indicator {
    width: 8px; height: 8px; border-radius: 50%;
    background: #28a745; display: inline-block; margin-right: 4px;
}
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Merhaba, {{ auth()->user()->name }} 👋</h4>
        <small class="text-muted">Sosyal medya hesaplarını buradan yönet</small>
    </div>
    <a href="{{ route('posts.create') }}" class="btn btn-primary px-4 py-2 fw-semibold"
       style="background:linear-gradient(135deg,#6c63ff,#5a52d5);border:none;border-radius:12px;">
        <i class="bi bi-plus-lg me-1"></i> Yeni Post
    </a>
</div>

{{-- Bağlı Hesaplar --}}
@php
    $platforms = [
        'instagram' => ['icon' => 'instagram', 'color' => '#e1306c', 'bg' => '#fce4ec', 'label' => 'Instagram'],
        'twitter'   => ['icon' => 'twitter-x', 'color' => '#000',    'bg' => '#f5f5f5', 'label' => 'X (Twitter)'],
        'tiktok'    => ['icon' => 'tiktok',    'color' => '#010101', 'bg' => '#f0f0f0', 'label' => 'TikTok'],
    ];
@endphp
<div class="row g-3 mb-4">
    @foreach($platforms as $key => $info)
    @php $account = $accounts->where('platform', $key)->first(); @endphp
    <div class="col-md-4">
        <div class="card account-card shadow-sm">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:44px;height:44px;background:{{ $info['bg'] }};">
                        <i class="bi bi-{{ $info['icon'] }}" style="font-size:1.2rem;color:{{ $info['color'] }};"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small">{{ $info['label'] }}</div>
                        @if($account)
                            <small class="text-success fw-semibold">
                                <span class="connected-indicator"></span>{{ '@'.$account->platform_username }}
                            </small>
                        @else
                            <small class="text-muted">Bağlı değil</small>
                        @endif
                    </div>
                </div>
                @if($account)
                    <form method="POST" action="{{ route('social.destroy', $account) }}"
                          onsubmit="return confirm('Hesap bağlantısı kaldırılsın mı?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('social.redirect', $key) }}"
                       class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-link-45deg"></i> Bağla
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- İstatistikler --}}
@php
    $totalPosts        = $posts->total();
    $publishedPosts    = \App\Models\Post::where('user_id', auth()->id())->where('status', 'published')->count();
    $failedPosts       = \App\Models\Post::where('user_id', auth()->id())->where('status', 'failed')->count();
    $connectedAccounts = $accounts->count();
@endphp
<div class="row g-3 mb-4">
    @foreach([
        ['label'=>'Toplam Post',  'value'=>$totalPosts,        'icon'=>'file-earmark-text', 'color'=>'#6c63ff','bg'=>'#ede9ff'],
        ['label'=>'Yayınlandı',   'value'=>$publishedPosts,    'icon'=>'check-circle',      'color'=>'#28a745','bg'=>'#e8f5e9'],
        ['label'=>'Başarısız',    'value'=>$failedPosts,       'icon'=>'x-circle',          'color'=>'#dc3545','bg'=>'#fce4ec'],
        ['label'=>'Bağlı Hesap',  'value'=>$connectedAccounts, 'icon'=>'person-check',      'color'=>'#fd7e14','bg'=>'#fff3e0'],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:48px;height:48px;background:{{ $stat['bg'] }};">
                    <i class="bi bi-{{ $stat['icon'] }}" style="font-size:1.4rem;color:{{ $stat['color'] }};"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1 mb-1">{{ $stat['value'] }}</div>
                    <small class="text-muted">{{ $stat['label'] }}</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Son Postlar --}}
<div class="card border-0 shadow-sm" style="border-radius:16px;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Son Postlar</h5>
        </div>

        @forelse($posts as $post)
        <div class="card post-card border-0 bg-light mb-3" style="border-radius:12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="flex-grow-1 min-w-0">
                        {{-- Platformlar ve durum --}}
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            @foreach($post->platforms as $platform)
                                <span class="platform-badge badge-{{ $platform }}">
                                    <i class="bi bi-{{ $platform === 'twitter' ? 'twitter-x' : $platform }}"></i>
                                    {{ $platform === 'twitter' ? 'X' : ucfirst($platform) }}
                                </span>
                            @endforeach

                            @php
                                $statusConfig = [
                                    'published'  => ['color'=>'success',  'label'=>'Yayınlandı',     'spinner'=>false],
                                    'publishing' => ['color'=>'warning',  'label'=>'Yayınlanıyor...','spinner'=>true],
                                    'failed'     => ['color'=>'danger',   'label'=>'Başarısız',      'spinner'=>false],
                                    'draft'      => ['color'=>'secondary','label'=>'Taslak',         'spinner'=>false],
                                ];
                                $sc = $statusConfig[$post->status] ?? $statusConfig['draft'];
                            @endphp
                            <span class="badge bg-{{ $sc['color'] }}-subtle text-{{ $sc['color'] }} border border-{{ $sc['color'] }}-subtle">
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
                                <small class="text-muted progress-message">Hazırlanıyor...</small>
                                <small class="text-muted progress-percent fw-semibold">0%</small>
                            </div>
                            <div class="progress" style="height:5px;border-radius:6px;background:#e9ecef;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                     role="progressbar" style="width:0%;border-radius:6px;"></div>
                            </div>
                        </div>
                        @endif

                        {{-- İçerik --}}
                        @if($post->title)
                            <div class="fw-semibold small mb-1 text-truncate">{{ $post->title }}</div>
                        @endif
                        @if($post->content)
                            <p class="mb-2 small text-muted text-truncate" style="max-width:500px;">{{ $post->content }}</p>
                        @endif

                        {{-- Medya --}}
                        @if($post->media->isNotEmpty())
                        <div class="d-flex gap-2 mb-2">
                            @foreach($post->media->take(4) as $media)
                                @if($media->file_type === 'image')
                                    <img src="{{ asset('storage/'.$media->file_path) }}"
                                         style="width:52px;height:52px;object-fit:cover;border-radius:8px;" alt="">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white"
                                         style="width:52px;height:52px;border-radius:8px;">
                                        <i class="bi bi-play-circle fs-5"></i>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        {{-- Platform sonuçları --}}
                        @if($post->results->isNotEmpty())
                        <div class="d-flex gap-2 flex-wrap mb-1">
                            @foreach($post->results as $result)
                                <span class="small text-{{ $result->status==='success' ? 'success' : ($result->status==='failed' ? 'danger' : 'warning') }}">
                                    <i class="bi bi-{{ $result->status==='success' ? 'check-circle-fill' : ($result->status==='failed' ? 'x-circle-fill' : 'hourglass-split') }}"></i>
                                    {{ ucfirst($result->platform === 'twitter' ? 'X' : $result->platform) }}
                                </span>
                            @endforeach
                        </div>
                        @endif

                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $post->created_at->diffForHumans() }}</small>
                    </div>

                    <form method="POST" action="{{ route('posts.destroy', $post) }}"
                          onsubmit="return confirm('Bu post silinsin mi?')" class="flex-shrink-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <div style="font-size:3.5rem;color:#dee2e6;"><i class="bi bi-inbox"></i></div>
            <p class="text-muted mt-3 mb-1 fw-semibold">Henüz post yok</p>
            <a href="{{ route('posts.create') }}" class="btn btn-sm btn-primary rounded-pill px-4"
               style="background:linear-gradient(135deg,#6c63ff,#5a52d5);border:none;">
                İlk Postunu Oluştur
            </a>
        </div>
        @endforelse

        <div class="mt-3">{{ $posts->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.post-progress').forEach(el => {
    const postId = el.dataset.postId;
    const bar = el.querySelector('.progress-bar');
    const msg = el.querySelector('.progress-message');
    const pct = el.querySelector('.progress-percent');

    const poll = () => {
        fetch(`/posts/${postId}/progress`)
            .then(r => r.json())
            .then(data => {
                if (data.percent != null) {
                    bar.style.width = data.percent + '%';
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

@endsection
