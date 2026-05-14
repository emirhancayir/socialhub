@extends('layouts.app')

@section('title', 'Yeni Post — SocialHub')

@push('styles')
<style>
    .platform-checkbox { display: none; }
    .platform-label {
        cursor: pointer; border: 2px solid #e5e7eb; border-radius: 14px;
        padding: 14px 16px; transition: all 0.18s; user-select: none;
        display: flex; align-items: center; gap: 14px; width: 100%;
    }
    .platform-label:hover { border-color: #6c63ff; background: #fafafe; }
    .platform-checkbox:checked + .platform-label { border-color: #6c63ff; background: #ede9ff; }
    .platform-checkbox:disabled + .platform-label { opacity: 0.45; cursor: not-allowed; }
    .check-icon { margin-left: auto; display: none; }
    .platform-checkbox:checked + .platform-label .check-icon { display: block; }

    .drop-zone {
        border: 2px dashed #d1d5db; border-radius: 14px; padding: 36px 24px;
        text-align: center; cursor: pointer; transition: all 0.18s; background: #fafafa;
    }
    .drop-zone:hover, .drop-zone.dragover { border-color: #6c63ff; background: #f5f3ff; }
    .drop-zone .drop-icon { font-size: 2.2rem; color: #6c63ff; margin-bottom: 8px; }

    .media-preview { position: relative; display: inline-block; margin: 4px; }
    .media-preview img, .media-preview video {
        width: 90px; height: 90px; object-fit: cover; border-radius: 10px;
        border: 2px solid #e5e7eb;
    }
    .media-preview .remove-btn {
        position: absolute; top: -6px; right: -6px;
        background: #ef4444; color: #fff; border: none;
        border-radius: 50%; width: 22px; height: 22px;
        font-size: 0.65rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .char-counter { font-size: 0.78rem; color: #9ca3af; }
    .char-counter.warning { color: #f59e0b; }
    .char-counter.danger   { color: #ef4444; }

    .form-control, .form-select {
        border: 2px solid #e5e7eb; border-radius: 12px;
        padding: 11px 14px; font-size: 0.9rem; transition: border-color 0.18s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,0.12);
    }
    textarea.form-control { resize: vertical; }

    .section-label {
        font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .6px; color: #6b7280; margin-bottom: 14px;
        display: flex; align-items: center; gap: 7px;
    }
    .section-label i { color: #6c63ff; font-size: 1rem; }

    .requirements-card {
        background: linear-gradient(135deg, #fffbeb, #fef9c3);
        border: 1px solid #fde68a; border-radius: 14px; padding: 14px 16px;
    }
    .req-item { display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #78716c; padding: 3px 0; }
    .req-item i { font-size: 0.9rem; flex-shrink: 0; }

    .btn-submit {
        background: linear-gradient(135deg, #6c63ff, #5a52d5); border: none;
        border-radius: 14px; color: #fff; font-weight: 700; font-size: 1rem;
        padding: 14px; width: 100%; cursor: pointer; transition: opacity 0.2s, transform 0.1s;
        box-shadow: 0 4px 14px rgba(108,99,255,0.35);
    }
    .btn-submit:hover { opacity: 0.93; transform: translateY(-1px); }
    .btn-submit:disabled { opacity: 0.7; transform: none; cursor: not-allowed; }
</style>
@endpush

@section('content')
{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-5">
    <a href="{{ route('dashboard') }}" class="btn btn-light border rounded-3 px-3 py-2">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Yeni Post Oluştur</h4>
        <small class="text-muted">İçeriğini hazırla, platformları seç ve paylaş</small>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4" style="background:#fef2f2;color:#991b1b;">
    @foreach($errors->all() as $error)
        <div class="d-flex align-items-center gap-2"><i class="bi bi-exclamation-circle"></i> {{ $error }}</div>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postForm">
@csrf
<div class="row g-4">

    {{-- ── Sol: İçerik + Medya ──────────────────── --}}
    <div class="col-lg-7">

        {{-- İçerik --}}
        <div class="sh-card p-4 mb-4">
            <div class="section-label"><i class="bi bi-pencil-square"></i> İçerik</div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Başlık
                    <span class="text-muted fw-normal">(TikTok için zorunlu)</span>
                </label>
                <input type="text" name="title" class="form-control"
                       placeholder="Video başlığı veya gönderi başlığı..."
                       value="{{ old('title') }}" maxlength="150" id="titleInput">
                <div class="text-end char-counter mt-1" id="titleCounter">0 / 150</div>
            </div>

            <div class="mb-1">
                <label class="form-label fw-semibold small">Açıklama / Caption</label>
                <textarea name="content" class="form-control" rows="5"
                          placeholder="Postunuzun açıklamasını buraya yazın..."
                          maxlength="2200" id="contentInput">{{ old('content') }}</textarea>
                <div class="text-end char-counter mt-1" id="contentCounter">0 / 2200</div>
            </div>
        </div>

        {{-- Medya --}}
        <div class="sh-card p-4">
            <div class="section-label"><i class="bi bi-images"></i> Medya</div>
            <div class="drop-zone" id="dropZone" onclick="document.getElementById('mediaInput').click()">
                <div class="drop-icon"><i class="bi bi-cloud-upload"></i></div>
                <p class="mb-1 fw-semibold small">Sürükle bırak veya tıkla</p>
                <small class="text-muted">JPG, PNG, GIF, MP4, MOV — Maks. 250 MB</small>
            </div>
            <input type="file" name="media[]" id="mediaInput" multiple accept="image/*,video/*" class="d-none">
            <div id="mediaPreview" class="mt-3 d-flex flex-wrap"></div>
        </div>
    </div>

    {{-- ── Sağ: Platformlar + Ayarlar ──────────── --}}
    <div class="col-lg-5">

        {{-- Platformlar --}}
        <div class="sh-card p-4 mb-4">
            <div class="section-label"><i class="bi bi-share-fill"></i> Platformlar</div>
            @php
                $platformList = [
                    'instagram' => ['icon'=>'instagram', 'label'=>'Instagram',   'color'=>'#e1306c'],
                    'twitter'   => ['icon'=>'twitter-x', 'label'=>'X (Twitter)', 'color'=>'#000'],
                    'tiktok'    => ['icon'=>'tiktok',    'label'=>'TikTok',       'color'=>'#010101'],
                ];
            @endphp
            @foreach($platformList as $key => $info)
            @php $connected = isset($accounts[$key]) && $accounts[$key]->isNotEmpty(); @endphp
            <div class="mb-2">
                <input type="checkbox" class="platform-checkbox" name="platforms[]" value="{{ $key }}"
                       id="platform_{{ $key }}" {{ !$connected ? 'disabled' : '' }}
                       {{ in_array($key, old('platforms', [])) ? 'checked' : '' }}>
                <label class="platform-label" for="platform_{{ $key }}">
                    <i class="bi bi-{{ $info['icon'] }}" style="font-size:1.5rem;color:{{ $info['color'] }};flex-shrink:0;"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $info['label'] }}</div>
                        @if($connected)
                            <small class="text-success fw-semibold">
                                <i class="bi bi-check-circle-fill"></i>
                                {{ '@'.$accounts[$key]->first()->platform_username }}
                            </small>
                        @else
                            <small class="text-muted">
                                Bağlı değil —
                                <a href="{{ route('social.redirect', $key) }}" class="text-primary fw-semibold">Bağla</a>
                            </small>
                        @endif
                    </div>
                    <i class="bi bi-check-circle-fill text-primary check-icon fs-5"></i>
                </label>
            </div>
            @endforeach
        </div>

        {{-- Gereksinimler --}}
        <div class="requirements-card mb-4">
            <div class="fw-bold small mb-2" style="color:#92400e;">
                <i class="bi bi-info-circle-fill me-1" style="color:#f59e0b;"></i> Platform Gereksinimleri
            </div>
            <div class="req-item"><i class="bi bi-instagram" style="color:#e1306c;"></i> <strong>Instagram:</strong> Fotoğraf veya video zorunlu</div>
            <div class="req-item"><i class="bi bi-twitter-x"></i> <strong>X (Twitter):</strong> Medya opsiyonel</div>
            <div class="req-item"><i class="bi bi-tiktok"></i> <strong>TikTok:</strong> Yalnızca video, başlık zorunlu</div>
        </div>

        {{-- Zamanlama --}}
        <div class="sh-card p-4 mb-4">
            <div class="section-label"><i class="bi bi-calendar-event"></i> Zamanlama</div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="scheduleToggle" onchange="toggleSchedule()">
                <label class="form-check-label fw-semibold small" for="scheduleToggle">İleri tarihte yayınla</label>
            </div>
            <div id="scheduleField" class="d-none">
                <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control"
                       min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                <small class="text-muted mt-1 d-block">En az 5 dakika sonrası seçilebilir</small>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-submit" id="submitBtn">
            <i class="bi bi-send-fill me-2"></i><span id="submitText">Şimdi Yayınla</span>
        </button>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
// Karakter sayacı
function updateCounter(inputId, counterId, max) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const update = () => {
        const len = input.value.length;
        counter.textContent = len + ' / ' + max;
        counter.className = 'text-end char-counter mt-1' +
            (len > max * 0.9 ? ' danger' : len > max * 0.7 ? ' warning' : '');
    };
    input.addEventListener('input', update);
    update();
}
updateCounter('titleInput', 'titleCounter', 150);
updateCounter('contentInput', 'contentCounter', 2200);

// Medya yükleme & önizleme
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
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('dragover'); addFiles(e.dataTransfer.files); });

// Zamanlama
function toggleSchedule() {
    const field  = document.getElementById('scheduleField');
    const input  = document.getElementById('scheduledAt');
    const btn    = document.getElementById('submitText');
    const toggle = document.getElementById('scheduleToggle');
    if (toggle.checked) {
        field.classList.remove('d-none'); input.required = true; btn.textContent = 'Zamanla';
    } else {
        field.classList.add('d-none'); input.required = false; input.value = ''; btn.textContent = 'Şimdi Yayınla';
    }
}

// Submit
document.getElementById('postForm').addEventListener('submit', function(e) {
    const platforms = document.querySelectorAll('.platform-checkbox:checked');
    if (platforms.length === 0) {
        e.preventDefault();
        alert('Lütfen en az bir platform seç!');
        return;
    }
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Yayınlanıyor...';
    btn.disabled = true;
});
</script>
@endpush
