@extends('layouts.app')

@section('title', 'Medya Kütüphanesi — SocialHub')
@section('page-title', 'Medya Kütüphanesi')
@section('page-sub', 'Tüm yüklenen görsel ve videolar')

@push('styles')
<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
    }
    @media (max-width: 1200px) {
        .media-grid { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 768px) {
        .media-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 480px) {
        .media-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .media-item {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--border);
        background: var(--surface);
        transition: transform 0.15s, box-shadow 0.15s;
        cursor: pointer;
    }
    .media-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        border-color: #c7d2fe;
    }
    .media-thumb-wrap {
        width: 100%;
        aspect-ratio: 1;
        overflow: hidden;
        position: relative;
        background: #f3f4f6;
    }
    .media-thumb-wrap img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.2s;
    }
    .media-item:hover .media-thumb-wrap img { transform: scale(1.04); }
    .media-video-placeholder {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: #1e1e2e;
        color: rgba(255,255,255,0.5);
        font-size: 2rem;
    }
    .media-overlay {
        position: absolute; inset: 0;
        background: rgba(108,99,255,0.0);
        display: flex; align-items: center; justify-content: center;
        transition: background 0.15s;
        color: #fff;
        font-size: 1.5rem;
        opacity: 0;
    }
    .media-item:hover .media-overlay { background: rgba(108,99,255,0.35); opacity: 1; }
    .media-info {
        padding: 10px 12px;
    }
    .media-name {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 3px;
    }
    .media-meta {
        font-size: 0.68rem;
        color: var(--muted);
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .media-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-image { background: #dbeafe; color: #1d4ed8; }
    .badge-video { background: #f3e8ff; color: #7c3aed; }

    /* Lightbox */
    .lightbox-overlay {
        position: fixed; inset: 0;
        background: rgba(5,5,15,0.9);
        z-index: 10100;
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        animation: fadeIn 0.18s ease;
    }
    .lightbox-inner {
        position: relative;
        max-width: 900px;
        width: 100%;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .lightbox-inner img {
        max-width: 100%;
        max-height: 80vh;
        border-radius: 14px;
        object-fit: contain;
        box-shadow: 0 24px 64px rgba(0,0,0,0.5);
    }
    .lightbox-close {
        position: absolute;
        top: -14px; right: -14px;
        width: 40px; height: 40px;
        background: rgba(255,255,255,0.1);
        border: 1.5px solid rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    .lightbox-close:hover { background: rgba(255,255,255,0.2); }
    .lightbox-caption {
        margin-top: 14px;
        color: rgba(255,255,255,0.65);
        font-size: 0.82rem;
        text-align: center;
    }

    .empty-state {
        text-align: center; padding: 60px 20px;
    }
    .empty-icon {
        width: 80px; height: 80px;
        background: #f3f4f6;
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: #d1d5db;
        margin: 0 auto 20px;
    }
</style>
@endpush

@section('content')

@if($media->isEmpty())
    <div class="sh-card">
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-images"></i></div>
            <div class="fw-bold mb-1" style="font-size:0.95rem;color:var(--text);">Henüz medya yok</div>
            <p style="color:#9ca3af;font-size:0.85rem;margin-bottom:20px;">Post oluşturduğunda burada görünecek.</p>
            <a href="{{ route('posts.create') }}" class="btn fw-semibold px-4 py-2"
               style="background:linear-gradient(135deg,#6c63ff,#5a52d5);color:#fff;border:none;border-radius:12px;font-size:0.875rem;">
                <i class="bi bi-plus-lg me-1"></i> Yeni Post Oluştur
            </a>
        </div>
    </div>
@else
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div style="font-size:0.875rem;color:var(--muted);">
            <i class="bi bi-images me-1"></i>
            Toplam <strong style="color:var(--text);">{{ $media->total() }}</strong> medya dosyası
        </div>
    </div>

    <div class="media-grid mb-4">
        @foreach($media as $item)
        <div class="media-item"
             @if($item->file_type === 'image')
             onclick="openLightbox('{{ asset('storage/'.$item->file_path) }}', '{{ e($item->file_name) }}')"
             @endif>
            <div class="media-thumb-wrap">
                @if($item->file_type === 'image')
                    <img src="{{ asset('storage/'.$item->file_path) }}"
                         alt="{{ $item->file_name }}"
                         loading="lazy">
                    <div class="media-overlay"><i class="bi bi-zoom-in"></i></div>
                @else
                    <div class="media-video-placeholder">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <div class="media-overlay"><i class="bi bi-play-fill"></i></div>
                @endif
            </div>
            <div class="media-info">
                <div class="media-name" title="{{ $item->file_name }}">{{ $item->file_name }}</div>
                <div class="media-meta">
                    <span class="media-type-badge {{ $item->file_type === 'image' ? 'badge-image' : 'badge-video' }}">
                        <i class="bi bi-{{ $item->file_type === 'image' ? 'image' : 'camera-video' }}"></i>
                        {{ $item->file_type === 'image' ? 'Görsel' : 'Video' }}
                    </span>
                    @if($item->file_size)
                        <span>{{ round($item->file_size / 1024, 0) }} KB</span>
                    @endif
                    <span>{{ $item->created_at->format('d.m.Y') }}</span>
                </div>
                @if($item->post)
                    <div style="font-size:0.68rem;color:#6c63ff;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <i class="bi bi-link-45deg"></i>
                        {{ implode(', ', array_map(fn($p) => ucfirst($p === 'twitter' ? 'X' : $p), (array)$item->post->platforms)) }}
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    @if($media->hasPages())
    <div class="d-flex justify-content-center mt-2">
        {{ $media->links() }}
    </div>
    @endif
@endif

{{-- Lightbox Modal --}}
<div id="lightboxOverlay" class="lightbox-overlay" style="display:none;" onclick="if(event.target===this) closeLightbox()">
    <div class="lightbox-inner">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="bi bi-x-lg"></i>
        </button>
        <img id="lightboxImg" src="" alt="">
        <div id="lightboxCaption" class="lightbox-caption"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption;
    document.getElementById('lightboxOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightboxOverlay').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush
