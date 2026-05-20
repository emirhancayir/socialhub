@extends('layouts.app')

@section('title', 'Profil Ayarları — SocialHub')
@section('page-title', 'Profil Ayarları')
@section('page-sub', 'Hesap bilgilerini güncelle')

@push('styles')
<style>
    .form-control {
        border: 2px solid #eff0f6;
        border-radius: 12px;
        padding: 11px 14px;
        font-size: 0.875rem;
        transition: border-color 0.15s;
        color: #1e1e2e;
        background: var(--surface);
    }
    .form-control:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108,99,255,0.1);
        outline: none;
    }
    .form-control::placeholder { color: #c4c7d0; }
    .form-label { font-size: 0.85rem; font-weight: 600; color: var(--text); }
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
    .btn-save {
        background: linear-gradient(135deg, #6c63ff, #5a52d5);
        border: none; border-radius: 12px;
        color: #fff; font-weight: 700; font-size: 0.875rem;
        padding: 11px 28px;
        cursor: pointer;
        transition: opacity 0.15s, transform 0.1s;
        box-shadow: 0 4px 14px rgba(108,99,255,0.3);
    }
    .btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
    .profile-avatar-big {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, #6c63ff, #a78bfa);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 800; color: #fff;
        flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(108,99,255,0.35);
    }
</style>
@endpush

@section('content')

<div class="row g-4">
    <div class="col-lg-4">
        {{-- Profil özeti --}}
        <div class="sh-card p-4 mb-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="profile-avatar-big">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div>
                    <div class="fw-bold" style="font-size:1.05rem;color:var(--text);">{{ $user->name }}</div>
                    <div style="font-size:0.82rem;color:#9ca3af;">{{ $user->email }}</div>
                    <div style="font-size:0.75rem;color:#6c63ff;font-weight:600;margin-top:4px;">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $user->created_at->format('d M Y') }} tarihinde katıldı
                    </div>
                </div>
            </div>
        </div>

        {{-- Güvenlik ipuçları --}}
        <div class="sh-card p-4" style="background:linear-gradient(135deg,#fffbeb,#fef9c3);border-color:#fde68a;">
            <div class="fw-bold mb-2" style="font-size:0.82rem;color:#92400e;">
                <i class="bi bi-shield-check-fill me-1" style="color:#f59e0b;"></i>
                Güvenlik İpuçları
            </div>
            <div style="font-size:0.8rem;color:#78716c;line-height:1.6;">
                <div class="mb-1"><i class="bi bi-check2 me-1 text-success"></i>Güçlü bir şifre kullan</div>
                <div class="mb-1"><i class="bi bi-check2 me-1 text-success"></i>Şifreni düzenli güncelle</div>
                <div><i class="bi bi-check2 me-1 text-success"></i>Şifreni kimseyle paylaşma</div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- Hesap Bilgileri --}}
        <div class="sh-card p-4 mb-4">
            <div class="sec-label"><i class="bi bi-person-fill"></i> Hesap Bilgileri</div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_section" value="info">

                <div class="mb-3">
                    <label class="form-label">Ad Soyad</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required maxlength="100"
                           placeholder="Adın ve soyadın">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">E-posta Adresi</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required
                           placeholder="email@ornek.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-lg me-2"></i>Bilgileri Güncelle
                    </button>
                </div>
            </form>
        </div>

        {{-- Şifre Değiştir --}}
        <div class="sh-card p-4">
            <div class="sec-label"><i class="bi bi-lock-fill"></i> Şifre Değiştir</div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_section" value="password">
                {{-- Name and email must be passed through for validation --}}
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">

                <div class="mb-3">
                    <label class="form-label">Yeni Şifre</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="En az 8 karakter" minlength="8">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Şifre Tekrar</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Şifreni tekrar gir">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-shield-lock-fill me-2"></i>Şifreyi Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
