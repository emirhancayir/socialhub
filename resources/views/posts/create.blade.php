@extends('layouts.app')

@section('title', 'Yeni Post - SocialHub')

@push('styles')
<style>
    .platform-checkbox { display: none; }
    .platform-label {
        cursor: pointer;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 15px;
        transition: all 0.2s;
        user-select: none;
    }
    .platform-label:hover { border-color: #6c63ff; background: #fafafa; }
    .platform-checkbox:checked + .platform-label {
        border-color: #6c63ff;
        background: #ede9ff;
    }
    .platform-checkbox:disabled + .platform-label { opacity: 0.5; cursor: not-allowed; }
    .drop-zone {
        border: 2px dashed #dee2e6;
        border-radius: 14px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .drop-zone:hover, .drop-zone.dragover { border-color: #6c63ff; background: #f8f7ff; }
    .media-preview { position: relative; display: inline-block; margin: 5px; }
    .media-preview img, .media-preview video { width: 100px; height: 100px; object-fit: cover; border-radius: 10px; }
    .media-preview .remove-btn {
        position: absolute; top: -6px; right: -6px;
        background: #dc3545; color: #fff; border: none;
        border-radius: 50%; width: 22px; height: 22px;
        font-size: 0.7rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
    }
    .char-counter { font-size: 0.8rem; color: #6c757d; }
    .char-counter.warning { color: #fd7e14; }
    .char-counter.danger { color: #dc3545; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h3 class="fw-bold mb-0">Yeni Post Oluştur</h3>
        <small class="text-muted">İçeriğini hazırla ve platformları seç</small>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $error)
        <div><i class="bi bi-exclamation-circle"></i> {{ $error }}</div>
    @endforeach
</div>
@endif

<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postForm">
@csrf
<div class="row g-4">
    {{-- Sol Kolon: İçerik --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-pencil-square text-primary"></i> İçerik</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Başlık <small class="text-muted">(TikTok için zorunlu)</small></label>
                    <input type="text" name="title" class="form-control" placeholder="Video başlığı..." value="{{ old('title') }}" maxlength="150" id="titleInput">
                    <div class="text-end char-counter mt-1" id="titleCounter">0 / 150</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Açıklama / Caption</label>
                    <textarea name="content" class="form-control" rows="5" placeholder="Postunuzun açıklamasını yazın..." maxlength="2200" id="contentInput">{{ old('content') }}</textarea>
                    <div class="text-end char-counter mt-1" id="contentCounter">0 / 2200</div>
                </div>
            </div>
        </div>

        {{-- Medya Yükleme --}}
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-images text-primary"></i> Medya</h5>

                <div class="drop-zone" id="dropZone" onclick="document.getElementById('mediaInput').click()">
                    <i class="bi bi-cloud-upload" style="font-size: 2.5rem; color: #6c63ff;"></i>
                    <p class="mt-2 mb-1 fw-semibold">Dosya sürükle bırak veya tıkla</p>
                    <small class="text-muted">JPG, PNG, GIF, MP4, MOV — Max 100MB</small>
                </div>

                <input type="file" name="media[]" id="mediaInput" multiple accept="image/*,video/*" class="d-none">

                <div id="mediaPreview" class="mt-3 d-flex flex-wrap gap-2"></div>
            </div>
        </div>
    </div>

    {{-- Sağ Kolon: Platform Seçimi --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-share text-primary"></i> Platformlar</h5>
                <p class="text-muted small mb-3">Hangi platformlarda paylaşmak istiyorsun?</p>

                @php
                    $platformList = [
                        'instagram' => ['icon' => 'instagram', 'label' => 'Instagram', 'color' => '#e1306c'],
                        'twitter'   => ['icon' => 'twitter-x', 'label' => 'X (Twitter)', 'color' => '#000'],
                        'tiktok'    => ['icon' => 'tiktok',    'label' => 'TikTok',     'color' => '#010101'],
                    ];
                @endphp

                @foreach($platformList as $key => $info)
                @php $connected = isset($accounts[$key]) && $accounts[$key]->isNotEmpty(); @endphp
                <div class="mb-2">
                    <input type="checkbox" class="platform-checkbox" name="platforms[]" value="{{ $key }}"
                           id="platform_{{ $key }}" {{ !$connected ? 'disabled' : '' }}
                           {{ in_array($key, old('platforms', [])) ? 'checked' : '' }}>
                    <label class="platform-label d-flex align-items-center gap-3 w-100" for="platform_{{ $key }}">
                        <i class="bi bi-{{ $info['icon'] }}" style="font-size: 1.6rem; color: {{ $info['color'] }};"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $info['label'] }}</div>
                            @if($connected)
                                <small class="text-success">
                                    <i class="bi bi-check-circle-fill"></i>
                                    {{ '@'.$accounts[$key]->first()->platform_username }}
                                </small>
                            @else
                                <small class="text-muted">
                                    <i class="bi bi-x-circle"></i> Hesap bağlı değil
                                    <a href="{{ route('social.redirect', $key) }}" class="ms-1 text-primary">Bağla</a>
                                </small>
                            @endif
                        </div>
                        <i class="bi bi-check-circle-fill text-primary check-icon" style="display:none;"></i>
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Platform Uyarıları --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #fff8e1;">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-warning"></i> Platform Gereksinimleri</h6>
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="mb-1"><i class="bi bi-instagram text-danger"></i> <strong>Instagram:</strong> Fotoğraf veya video zorunlu</li>
                    <li class="mb-1"><i class="bi bi-twitter-x"></i> <strong>X (Twitter):</strong> Medya opsiyonel (metin yeterli)</li>
                    <li><i class="bi bi-tiktok"></i> <strong>TikTok:</strong> Yalnızca video, başlık zorunlu</li>
                </ul>
            </div>
        </div>

        {{-- Zamanlama --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock text-primary"></i> Zamanlama</h5>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="scheduleToggle" onchange="toggleSchedule()">
                    <label class="form-check-label fw-semibold" for="scheduleToggle">İleri tarihte yayınla</label>
                </div>
                <div id="scheduleField" class="d-none">
                    <input type="datetime-local" name="scheduled_at" id="scheduledAt"
                           class="form-control" style="border-radius:10px;"
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                    <small class="text-muted mt-1 d-block">En az 5 dakika sonrası seçilebilir</small>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-bold py-3" id="submitBtn"
                style="background: linear-gradient(135deg, #6c63ff, #5a52d5); border: none; border-radius: 12px; font-size: 1.1rem;">
            <i class="bi bi-send-fill"></i> <span id="submitText">Şimdi Yayınla</span>
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
    input.addEventListener('input', () => {
        const len = input.value.length;
        counter.textContent = len + ' / ' + max;
        counter.className = 'text-end char-counter mt-1' +
            (len > max * 0.9 ? ' danger' : len > max * 0.7 ? ' warning' : '');
    });
}
updateCounter('titleInput', 'titleCounter', 150);
updateCounter('contentInput', 'contentCounter', 2200);

// Checkbox görsel efekti
document.querySelectorAll('.platform-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const label = this.nextElementSibling;
        const icon = label.querySelector('.check-icon');
        if (this.checked) {
            icon.style.display = 'block';
        } else {
            icon.style.display = 'none';
        }
    });
});

// Medya yükleme & önizleme
const mediaInput = document.getElementById('mediaInput');
const mediaPreview = document.getElementById('mediaPreview');
const dropZone = document.getElementById('dropZone');
let selectedFiles = [];

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

// Zamanlama toggle
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

// Submit kontrolü
document.getElementById('postForm').addEventListener('submit', function(e) {
    const platforms = document.querySelectorAll('.platform-checkbox:checked');
    if (platforms.length === 0) {
        e.preventDefault();
        alert('Lütfen en az bir platform seç!');
        return;
    }
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-hourglass-split"></i> Yayınlanıyor...';
    document.getElementById('submitBtn').disabled = true;
});
</script>
@endpush
