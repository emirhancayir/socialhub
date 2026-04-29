@extends('layouts.app')

@section('title', 'Dashboard - SocialHub')

@push('styles')
<style>
@keyframes spin { to { transform: rotate(360deg); } }
.spinner-ring {
    width: 18px; height: 18px;
    border: 2px solid #ffc107;
    border-top-color: transparent;
    border-radius: 50%;
    display: inline-block;
    animation: spin 0.8s linear infinite;
    vertical-align: middle;
    margin-right: 5px;
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Dashboard</h3>
        <p class="text-muted mb-0">Hoş geldin, {{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('posts.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #6c63ff, #5a52d5); border: none; border-radius: 10px; padding: 10px 20px;">
        <i class="bi bi-plus-lg"></i> Yeni Post Oluştur
    </a>
</div>

{{-- Bağlı Hesaplar --}}
<div class="row g-3 mb-4">
    @php
        $platforms = [
            'instagram' => ['icon' => 'instagram', 'color' => '#e1306c', 'bg' => '#fce4ec', 'label' => 'Instagram'],
            'twitter'   => ['icon' => 'twitter-x', 'color' => '#000',    'bg' => '#f5f5f5',  'label' => 'X (Twitter)'],
            'tiktok'    => ['icon' => 'tiktok',    'color' => '#010101', 'bg' => '#f0f0f0',  'label' => 'TikTok'],
        ];
    @endphp

    @foreach($platforms as $key => $info)
    @php $account = $accounts->where('platform', $key)->first(); @endphp
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:{{ $info['bg'] }};">
                            <i class="bi bi-{{ $info['icon'] }}" style="font-size:1.4rem;color:{{ $info['color'] }};"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $info['label'] }}</div>
                            @if($account)
                                <small class="text-success"><i class="bi bi-check-circle-fill"></i> {{ '@'.$account->platform_username }}</small>
                            @else
                                <small class="text-muted">Bağlı değil</small>
                            @endif
                        </div>
                    </div>
                    @if($account)
                        <form method="POST" action="{{ route('social.destroy', $account) }}" onsubmit="return confirm('Hesap bağlantısı kaldırılsın mı?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Bağlantıyı Kes">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    @else
                        @if(env('DEMO_MODE'))
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="openConnectModal('{{ $key }}', '{{ $info['label'] }}')"
                                    data-bs-toggle="modal" data-bs-target="#connectModal">
                                <i class="bi bi-link-45deg"></i> Bağla
                            </button>
                        @else
                            <a href="{{ route('social.redirect', $key) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-link-45deg"></i> Bağla
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- İstatistikler --}}
<div class="row g-3 mb-4">
    @php
        $totalPosts     = $posts->total();
        $publishedPosts = \App\Models\Post::where('user_id', auth()->id())->where('status', 'published')->count();
        $failedPosts    = \App\Models\Post::where('user_id', auth()->id())->where('status', 'failed')->count();
        $connectedAccounts = $accounts->count();
    @endphp
    @foreach([
        ['label' => 'Toplam Post', 'value' => $totalPosts, 'icon' => 'file-post', 'color' => '#6c63ff', 'bg' => '#ede9ff'],
        ['label' => 'Yayınlandı',  'value' => $publishedPosts, 'icon' => 'check-circle', 'color' => '#28a745', 'bg' => '#e8f5e9'],
        ['label' => 'Başarısız',   'value' => $failedPosts, 'icon' => 'x-circle', 'color' => '#dc3545', 'bg' => '#fce4ec'],
        ['label' => 'Bağlı Hesap', 'value' => $connectedAccounts, 'icon' => 'people', 'color' => '#fd7e14', 'bg' => '#fff3e0'],
    ] as $stat)
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:{{ $stat['bg'] }};">
                    <i class="bi bi-{{ $stat['icon'] }}" style="font-size:1.5rem;color:{{ $stat['color'] }};"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $stat['value'] }}</div>
                    <small class="text-muted">{{ $stat['label'] }}</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Son Postlar --}}
<div class="card border-0 shadow-sm" style="border-radius: 14px;">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-clock-history"></i> Son Postlar</h5>

        @forelse($posts as $post)
        <div class="card post-card border shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            @foreach($post->platforms as $platform)
                                <span class="platform-badge badge-{{ $platform }}">
                                    <i class="bi bi-{{ $platform === 'twitter' ? 'twitter-x' : $platform }}"></i>
                                    {{ $platform === 'twitter' ? 'X' : ucfirst($platform) }}
                                </span>
                            @endforeach

                            @php
                                $statusConfig = [
                                    'published'  => ['color' => 'success',   'label' => 'Yayınlandı',     'spinner' => false],
                                    'publishing' => ['color' => 'warning',   'label' => 'Yayınlanıyor...','spinner' => true],
                                    'failed'     => ['color' => 'danger',    'label' => 'Başarısız',      'spinner' => false],
                                    'draft'      => ['color' => 'secondary', 'label' => 'Taslak',         'spinner' => false],
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

                        @if($post->content)
                            <p class="mb-2 text-truncate" style="max-width: 600px;">{{ $post->content }}</p>
                        @endif

                        {{-- Medya önizleme --}}
                        @if($post->media->isNotEmpty())
                        <div class="d-flex gap-2 mb-2">
                            @foreach($post->media->take(4) as $media)
                                @if($media->file_type === 'image')
                                    <img src="{{ asset('storage/' . $media->file_path) }}"
                                         style="width:60px;height:60px;object-fit:cover;border-radius:8px;"
                                         alt="media">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-dark text-white"
                                         style="width:60px;height:60px;border-radius:8px;font-size:1.5rem;">
                                        <i class="bi bi-play-circle"></i>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        {{-- Platform sonuçları --}}
                        @if($post->results->isNotEmpty())
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($post->results as $result)
                                <small class="text-{{ $result->status === 'success' ? 'success' : ($result->status === 'failed' ? 'danger' : 'warning') }}">
                                    <i class="bi bi-{{ $result->status === 'success' ? 'check-circle-fill' : ($result->status === 'failed' ? 'x-circle-fill' : 'hourglass-split') }}"></i>
                                    {{ ucfirst($result->platform) }}
                                </small>
                            @endforeach
                        </div>
                        @endif

                        <small class="text-muted mt-1 d-block">
                            <i class="bi bi-clock"></i> {{ $post->created_at->diffForHumans() }}
                        </small>
                    </div>

                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Bu post silinsin mi?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Henüz post yok. <a href="{{ route('posts.create') }}">İlk postunu oluştur!</a></p>
        </div>
        @endforelse

        {{ $posts->links() }}
    </div>
</div>

{{-- Hesap Bağlama Modalı (Demo Mod) --}}
@if(env('DEMO_MODE'))
<div class="modal fade" id="connectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div id="modalPlatformIcon" style="font-size: 1.8rem;"></div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle">Hesap Bağla</h5>
                        <small class="text-muted" id="modalSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('social.demo-store') }}">
                @csrf
                <input type="hidden" name="platform" id="modalPlatform">

                <div class="modal-body pt-3">
                    <div class="alert alert-warning d-flex align-items-center gap-2 py-2" style="border-radius: 10px; font-size: 0.85rem;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span><strong>Demo Mod:</strong> Gerçek giriş yapılmaz. API key eklenince OAuth devreye girer.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kullanıcı Adı</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" name="username" class="form-control" placeholder="kullaniciadi" required autofocus>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold">Şifre</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">
                        İptal
                    </button>
                    <button type="submit" class="btn text-white fw-semibold" id="modalSubmitBtn"
                            style="border-radius: 10px; background: linear-gradient(135deg, #6c63ff, #5a52d5); border: none; padding: 8px 24px;">
                        <i class="bi bi-link-45deg"></i> Bağla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Yayınlanıyor durumunda 3 saniyede bir otomatik yenile
@if($posts->where('status', 'publishing')->count() > 0)
    setTimeout(() => location.reload(), 3000);
@endif

    const platformIcons = {
        instagram: '<i class="bi bi-instagram" style="color:#e1306c;"></i>',
        twitter:   '<i class="bi bi-twitter-x" style="color:#000;"></i>',
        tiktok:    '<i class="bi bi-tiktok" style="color:#010101;"></i>',
    };

    function openConnectModal(platform, label) {
        document.getElementById('modalPlatform').value   = platform;
        document.getElementById('modalTitle').textContent = label + ' Hesabı Bağla';
        document.getElementById('modalSubtitle').textContent = label + ' kullanıcı adını gir';
        document.getElementById('modalPlatformIcon').innerHTML = platformIcons[platform] || '';
    }

    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
</script>
@endpush
@endif
@endsection
