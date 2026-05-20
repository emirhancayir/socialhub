@extends('layouts.app')

@section('title', 'Yeni Post — SocialHub')
@section('page-title', 'Yeni Post')
@section('page-sub', 'İçerik hazırla, platform seç ve paylaş')

@push('styles')
<style>
    .platform-checkbox { display: none; }
    .platform-label {
        cursor: pointer;
        border: 2px solid #eff0f6;
        border-radius: 14px;
        padding: 13px 16px;
        transition: all 0.15s;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 13px;
        width: 100%;
        background: #fff;
    }
    .platform-label:hover { border-color: #c7d2fe; background: #faf9ff; }
    .platform-checkbox:checked + .platform-label { border-color: #6c63ff; background: #ede9ff; }
    .platform-checkbox:disabled + .platform-label { opacity: 0.45; cursor: not-allowed; }
    .check-icon { margin-left: auto; display: none; color: #6c63ff; }
    .platform-checkbox:checked + .platform-label .check-icon { display: block; }

    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 14px;
        padding: 40px 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s;
        background: #fafbfc;
    }
    .drop-zone:hover, .drop-zone.dragover {
        border-color: #6c63ff;
        background: #f5f3ff;
    }
    .drop-zone .drop-icon { font-size: 2.4rem; color: #c7d2fe; margin-bottom: 10px; transition: color 0.15s; }
    .drop-zone:hover .drop-icon, .drop-zone.dragover .drop-icon { color: #6c63ff; }

    .media-preview { position: relative; display: inline-block; margin: 4px; }
    .media-preview img, .media-preview video {
        width: 88px; height: 88px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #eff0f6;
        display: block;
    }
    .media-preview .remove-btn {
        position: absolute; top: -7px; right: -7px;
        background: #ef4444; color: #fff; border: none;
        border-radius: 50%; width: 22px; height: 22px;
        font-size: 0.6rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(239,68,68,0.35);
    }

    .char-counter { font-size: 0.75rem; color: #9ca3af; }
    .char-counter.warning { color: #f59e0b; }
    .char-counter.danger   { color: #ef4444; }

    .form-control, .form-select {
        border: 2px solid #eff0f6;
        border-radius: 12px;
        padding: 11px 14px;
        font-size: 0.875rem;
        transition: border-color 0.15s;
        color: #1e1e2e;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108,99,255,0.1);
        outline: none;
    }
    textarea.form-control { resize: vertical; }
    .form-control::placeholder { color: #c4c7d0; }

    .sec-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: #9ca3af;
        margin-bottom: 16px;
        display: flex; align-items: center; gap: 7px;
    }
    .sec-label i { color: #6c63ff; font-size: 0.95rem; }

    .req-card {
        background: linear-gradient(135deg, #fffbeb, #fef9c3);
        border: 1px solid #fde68a;
        border-radius: 14px;
        padding: 14px 16px;
    }
    .req-row {
        display: flex; align-items: center; gap: 9px;
        font-size: 0.82rem; color: #78716c;
        padding: 4px 0;
    }

    .btn-publish {
        background: linear-gradient(135deg, #6c63ff, #5a52d5);
        border: none; border-radius: 14px;
        color: #fff; font-weight: 700; font-size: 0.95rem;
        padding: 14px; width: 100%;
        cursor: pointer;
        transition: opacity 0.15s, transform 0.1s;
        box-shadow: 0 4px 16px rgba(108,99,255,0.35);
    }
    .btn-publish:hover { opacity: 0.92; transform: translateY(-1px); }
    .btn-publish:disabled { opacity: 0.65; transform: none; cursor: not-allowed; }

    .btn-draft {
        background: #f3f4f6;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        color: #6b7280; font-weight: 700; font-size: 0.95rem;
        padding: 12px; width: 100%;
        cursor: pointer;
        transition: all 0.15s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-draft:hover { background: #e9eaec; color: #374151; border-color: #d1d5db; }

    .btn-preview {
        background: transparent;
        border: 1.5px solid #c7d2fe;
        border-radius: 14px;
        color: #6c63ff; font-weight: 600; font-size: 0.875rem;
        padding: 10px; width: 100%;
        cursor: pointer;
        transition: all 0.15s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        margin-bottom: 12px;
    }
    .btn-preview:hover { background: #ede9ff; border-color: #6c63ff; }

    /* Preview Modal */
    .preview-overlay {
        position: fixed; inset: 0;
        background: rgba(15,12,41,0.65);
        z-index: 10000;
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        animation: fadeIn 0.18s ease;
    }
    .preview-modal {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        width: 100%; max-width: 740px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0,0,0,0.2);
        animation: modalIn 0.22s cubic-bezier(0.34,1.56,0.64,1);
    }
    .preview-card {
        border: 1px solid #eff0f6;
        border-radius: 16px;
        overflow: hidden;
    }
    .preview-card-header {
        padding: 12px 14px;
        display: flex; align-items: center; gap: 10px;
        border-bottom: 1px solid #f3f4f6;
    }
    .preview-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6c63ff, #a78bfa);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 0.9rem;
    }
    .preview-img-placeholder {
        width: 100%; aspect-ratio: 1;
        background: linear-gradient(135deg, #f0f2f8, #e8eaf0);
        display: flex; align-items: center; justify-content: center;
        color: #d1d5db; font-size: 3rem;
    }
    .preview-caption {
        padding: 12px 14px;
        font-size: 0.85rem; line-height: 1.5; color: #374151;
        white-space: pre-wrap; word-break: break-word;
    }
    .twitter-preview-card {
        border: 1px solid #eff0f6;
        border-radius: 16px;
        padding: 16px;
    }
</style>
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
    @foreach($errors->all() as $error)
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle"></i> {{ $error }}
        </div>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postForm">
@csrf
<div class="row g-4">

    {{-- Sol: İçerik + Medya --}}
    <div class="col-lg-7">

        {{-- İçerik Kartı --}}
        <div class="sh-card p-4 mb-4">
            <div class="sec-label"><i class="bi bi-pencil-square"></i> İçerik</div>

            <div class="mb-3">
                <label class="form-label fw-semibold" style="font-size:0.85rem;">
                    Başlık
                    <span class="text-muted fw-normal">(TikTok için zorunlu)</span>
                </label>
                <input type="text" name="title" class="form-control"
                       placeholder="Gönderi başlığı..."
                       value="{{ old('title') }}" maxlength="150" id="titleInput">
                <div class="d-flex justify-content-end mt-1">
                    <span class="char-counter" id="titleCounter">0 / 150</span>
                </div>
            </div>

            <div>
                <label class="form-label fw-semibold" style="font-size:0.85rem;">Açıklama / Caption</label>
                <textarea name="content" class="form-control" rows="6"
                          placeholder="Postun açıklamasını buraya yaz..."
                          maxlength="2200" id="contentInput">{{ old('content') }}</textarea>
                <div class="d-flex justify-content-end mt-1">
                    <span class="char-counter" id="contentCounter">0 / 2200</span>
                </div>
            </div>
        </div>

        {{-- Medya Kartı --}}
        <div class="sh-card p-4">
            <div class="sec-label"><i class="bi bi-images"></i> Medya</div>
            <div class="drop-zone" id="dropZone" onclick="document.getElementById('mediaInput').click()">
                <div class="drop-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                <p class="mb-1 fw-semibold" style="font-size:0.875rem;color:#374151;">Sürükle bırak veya tıkla</p>
                <small class="text-muted">JPG, PNG, GIF, MP4, MOV — Maks. 250 MB</small>
            </div>
            <input type="file" name="media[]" id="mediaInput" multiple accept="image/*,video/*" class="d-none">
            <div id="mediaPreview" class="mt-3 d-flex flex-wrap"></div>
        </div>
    </div>

    {{-- Sağ: Platformlar + Zamanlama --}}
    <div class="col-lg-5">

        {{-- Platformlar --}}
        <div class="sh-card p-4 mb-4">
            <div class="sec-label"><i class="bi bi-share-fill"></i> Platformlar</div>
            @php
                $platformList = [
                    'instagram' => ['icon'=>'instagram', 'label'=>'Instagram',   'color'=>'#e1306c', 'bg'=>'#fff0f5'],
                    'twitter'   => ['icon'=>'twitter-x', 'label'=>'X (Twitter)', 'color'=>'#000',    'bg'=>'#f5f5f5'],
                    'tiktok'    => ['icon'=>'tiktok',    'label'=>'TikTok',       'color'=>'#010101', 'bg'=>'#f0f0f0'],
                ];
            @endphp
            @foreach($platformList as $key => $info)
            @php $connected = isset($accounts[$key]) && $accounts[$key]->isNotEmpty(); @endphp
            <div class="mb-2">
                <input type="checkbox" class="platform-checkbox" name="platforms[]" value="{{ $key }}"
                       id="platform_{{ $key }}" {{ !$connected ? 'disabled' : '' }}
                       {{ in_array($key, old('platforms', [])) ? 'checked' : '' }}>
                <label class="platform-label" for="platform_{{ $key }}">
                    <div style="width:40px;height:40px;background:{{ $info['bg'] }};border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-{{ $info['icon'] }}" style="font-size:1.2rem;color:{{ $info['color'] }};"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.875rem;">{{ $info['label'] }}</div>
                        @if($connected)
                            <small class="d-block" style="color:#16a34a;font-weight:600;font-size:0.75rem;">
                                <i class="bi bi-check-circle-fill"></i>
                                {{ '@'.$accounts[$key]->first()->platform_username }}
                            </small>
                        @else
                            <small style="color:#9ca3af;font-size:0.75rem;">
                                Bağlı değil —
                                <a href="{{ route('social.redirect', $key) }}" style="color:#6c63ff;font-weight:600;">Bağla</a>
                            </small>
                        @endif
                    </div>
                    <i class="bi bi-check-circle-fill check-icon fs-5"></i>
                </label>
            </div>
            @endforeach
        </div>

        {{-- Gereksinimler --}}
        <div class="req-card mb-4">
            <div class="fw-bold mb-2" style="font-size:0.82rem;color:#92400e;">
                <i class="bi bi-info-circle-fill me-1" style="color:#f59e0b;"></i>
                Platform Gereksinimleri
            </div>
            <div class="req-row">
                <i class="bi bi-instagram" style="color:#e1306c;"></i>
                <span><strong>Instagram:</strong> Fotoğraf veya video zorunlu</span>
            </div>
            <div class="req-row">
                <i class="bi bi-twitter-x"></i>
                <span><strong>X (Twitter):</strong> Medya opsiyonel</span>
            </div>
            <div class="req-row">
                <i class="bi bi-tiktok"></i>
                <span><strong>TikTok:</strong> Yalnızca video, başlık zorunlu</span>
            </div>
        </div>

        {{-- Zamanlama --}}
        <div class="sh-card p-4 mb-4">
            <div class="sec-label"><i class="bi bi-calendar-event"></i> Zamanlama</div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="scheduleToggle" onchange="toggleSchedule()">
                <label class="form-check-label fw-semibold" style="font-size:0.875rem;" for="scheduleToggle">
                    İleri tarihte yayınla
                </label>
            </div>
            <div id="scheduleField" class="d-none">
                <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control"
                       min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                <small class="text-muted mt-1 d-block">En az 5 dakika sonrası seçilebilir</small>
            </div>
        </div>

        {{-- Önizle Butonu --}}
        <button type="button" class="btn-preview" onclick="openPreview()">
            <i class="bi bi-eye"></i> Önizle
        </button>

        {{-- Taslak Kaydet --}}
        <button type="submit" name="save_draft" value="1" class="btn-draft mb-3">
            <i class="bi bi-bookmark me-2"></i> Taslak Kaydet
        </button>

        {{-- Yayınla --}}
        <button type="submit" class="btn-publish" id="submitBtn">
            <i class="bi bi-send-fill me-2"></i><span id="submitText">Şimdi Yayınla</span>
        </button>
    </div>
</div>
</form>

{{-- Preview Modal --}}
<div id="previewOverlay" class="preview-overlay" style="display:none;" onclick="if(event.target===this) closePreview()">
    <div class="preview-modal">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <div class="fw-bold" style="font-size:1rem;color:#1e1e2e;">Post Önizlemesi</div>
                <div style="font-size:0.78rem;color:#9ca3af;">Paylaşmadan önce nasıl görüneceğini incele</div>
            </div>
            <button onclick="closePreview()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1.3rem;padding:0;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="row g-3">
            {{-- Instagram Preview --}}
            <div class="col-md-6">
                <div class="fw-semibold mb-2" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.7px;color:#9ca3af;">
                    <i class="bi bi-instagram" style="color:#e1306c;"></i> Instagram
                </div>
                <div class="preview-card">
                    <div class="preview-card-header">
                        <div class="preview-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.72rem;color:#9ca3af;">az önce</div>
                        </div>
                    </div>
                    <div class="preview-img-placeholder">
                        <i class="bi bi-image"></i>
                    </div>
                    <div class="preview-caption">
                        <strong style="font-size:0.85rem;">{{ auth()->user()->name }}</strong>
                        <span id="igCaption"> —</span>
                    </div>
                </div>
            </div>
            {{-- Twitter Preview --}}
            <div class="col-md-6">
                <div class="fw-semibold mb-2" style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.7px;color:#9ca3af;">
                    <i class="bi bi-twitter-x"></i> X (Twitter)
                </div>
                <div class="twitter-preview-card">
                    <div class="d-flex gap-3">
                        <div class="preview-avatar flex-shrink-0">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div>
                            <div class="fw-bold" style="font-size:0.875rem;">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.75rem;color:#9ca3af;margin-bottom:8px;">@{{ strtolower(str_replace(' ', '', auth()->user()->name)) }}</div>
                            <div id="twCaption" style="font-size:0.875rem;color:#374151;line-height:1.5;white-space:pre-wrap;word-break:break-word;">—</div>
                            <div style="font-size:0.75rem;color:#9ca3af;margin-top:10px;">az önce · Twitter Web App</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
            <button onclick="closePreview()" class="btn-draft" style="width:auto;padding:10px 24px;">
                Kapat
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateCounter(inputId, counterId, max) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const update = () => {
        const len = input.value.length;
        counter.textContent = len + ' / ' + max;
        counter.className = 'char-counter' + (len > max * 0.9 ? ' danger' : len > max * 0.7 ? ' warning' : '');
    };
    input.addEventListener('input', update);
    update();
}
updateCounter('titleInput', 'titleCounter', 150);
updateCounter('contentInput', 'contentCounter', 2200);

const mediaInput   = document.getElementById('mediaInput');
const mediaPreview = document.getElementById('mediaPreview');
const dropZone     = document.getElementById('dropZone');
let selectedFiles  = [];

function updateFileInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    mediaInput.files = dt.files;
}

function addFiles(files) {
    Array.from(files).forEach(file => {
        selectedFiles.push(file);
        const wrapper = document.createElement('div');
        wrapper.className = 'media-preview';
        let preview;
        if (file.type.startsWith('video/')) {
            preview = document.createElement('video');
            preview.src = URL.createObjectURL(file);
            preview.muted = true;
        } else {
            preview = document.createElement('img');
            preview.src = URL.createObjectURL(file);
        }
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = () => {
            selectedFiles = selectedFiles.filter(f => f !== file);
            wrapper.remove();
            updateFileInput();
        };
        wrapper.appendChild(preview);
        wrapper.appendChild(removeBtn);
        mediaPreview.appendChild(wrapper);
    });
    updateFileInput();
}

mediaInput.addEventListener('change', () => addFiles(mediaInput.files));
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    addFiles(e.dataTransfer.files);
});

function toggleSchedule() {
    const field  = document.getElementById('scheduleField');
    const input  = document.getElementById('scheduledAt');
    const btn    = document.getElementById('submitText');
    const toggle = document.getElementById('scheduleToggle');
    if (toggle.checked) {
        field.classList.remove('d-none');
        input.required = true;
        btn.textContent = 'Zamanla';
    } else {
        field.classList.add('d-none');
        input.required = false;
        input.value = '';
        btn.textContent = 'Şimdi Yayınla';
    }
}

document.getElementById('postForm').addEventListener('submit', function(e) {
    const isSaveDraft = e.submitter && e.submitter.name === 'save_draft';
    if (!isSaveDraft && !document.querySelectorAll('.platform-checkbox:checked').length) {
        e.preventDefault();
        SH.toast('Devam etmek için en az bir platform seçmelisin.', 'warning');
        return;
    }
    if (!isSaveDraft) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Yayınlanıyor...';
        btn.disabled = true;
    }
});

// Preview modal
function openPreview() {
    const caption = document.getElementById('contentInput').value.trim() || '—';
    document.getElementById('igCaption').textContent = ' ' + caption;
    document.getElementById('twCaption').textContent = caption;
    document.getElementById('previewOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePreview() {
    document.getElementById('previewOverlay').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePreview();
});

// Live preview update from textarea
document.getElementById('contentInput').addEventListener('input', function() {
    const v = this.value.trim() || '—';
    const igEl = document.getElementById('igCaption');
    const twEl = document.getElementById('twCaption');
    if (igEl) igEl.textContent = ' ' + v;
    if (twEl) twEl.textContent = v;
});
</script>
@endpush
