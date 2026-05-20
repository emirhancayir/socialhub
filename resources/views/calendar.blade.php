@extends('layouts.app')

@section('title', 'Takvim — SocialHub')
@section('page-title', 'Takvim')
@section('page-sub', 'Planlanmış ve yayınlanmış postları görüntüle')

@push('styles')
<style>
    #calendarContainer {
        background: var(--surface);
        border-radius: 18px;
        border: 1px solid var(--border);
        padding: 24px;
        box-shadow: var(--card-shadow);
    }
    /* FullCalendar overrides */
    .fc {
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
        font-size: 0.875rem;
    }
    .fc-toolbar-title {
        font-size: 1rem !important;
        font-weight: 800 !important;
        color: var(--text) !important;
    }
    .fc-button-primary {
        background: #6c63ff !important;
        border-color: #6c63ff !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
        padding: 6px 14px !important;
        box-shadow: none !important;
        transition: opacity 0.15s !important;
    }
    .fc-button-primary:hover { opacity: 0.85 !important; }
    .fc-button-primary:disabled { opacity: 0.55 !important; }
    .fc-button-group .fc-button-primary { border-radius: 10px !important; }
    .fc-daygrid-day-number { color: var(--text) !important; font-size: 0.8rem; font-weight: 600; }
    .fc-col-header-cell-cushion { color: var(--muted) !important; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .fc-daygrid-day.fc-day-today { background: rgba(108,99,255,0.06) !important; }
    .fc-event {
        border: none !important;
        border-radius: 8px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 2px 6px !important;
        cursor: pointer;
    }
    .fc-event:hover { opacity: 0.85; }
    .fc-scrollgrid { border-color: var(--border) !important; }
    .fc-scrollgrid td, .fc-scrollgrid th { border-color: var(--border) !important; }
    .fc-daygrid-day { background: var(--surface) !important; }

    /* Legend */
    .legend-dot {
        width: 12px; height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Event detail modal */
    .event-detail-overlay {
        position: fixed; inset: 0;
        background: rgba(15,12,41,0.55);
        z-index: 10001;
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        animation: fadeIn 0.18s ease;
    }
    .event-detail-modal {
        background: var(--surface);
        border-radius: 20px;
        padding: 28px;
        width: 100%; max-width: 360px;
        box-shadow: 0 24px 64px rgba(0,0,0,0.2);
        animation: modalIn 0.22s cubic-bezier(0.34,1.56,0.64,1);
        color: var(--text);
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.9); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>
@endpush

@section('content')

{{-- Legend + Calendar --}}
<div class="d-flex align-items-center gap-4 flex-wrap mb-3">
    <div class="d-flex align-items-center gap-2" style="font-size:0.8rem;color:var(--muted);">
        <span class="legend-dot" style="background:#22c55e;"></span> Yayınlandı
    </div>
    <div class="d-flex align-items-center gap-2" style="font-size:0.8rem;color:var(--muted);">
        <span class="legend-dot" style="background:#ef4444;"></span> Başarısız
    </div>
    <div class="d-flex align-items-center gap-2" style="font-size:0.8rem;color:var(--muted);">
        <span class="legend-dot" style="background:#6c63ff;"></span> Taslak / Zamanlanmış
    </div>
    <div class="d-flex align-items-center gap-2" style="font-size:0.8rem;color:var(--muted);">
        <span class="legend-dot" style="background:#f59e0b;"></span> Yayınlanıyor
    </div>
</div>

<div id="calendarContainer">
    <div id="calendar"></div>
</div>

{{-- Event detail modal --}}
<div id="eventDetailOverlay" class="event-detail-overlay" style="display:none;" onclick="if(event.target===this) closeEventDetail()">
    <div class="event-detail-modal">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="fw-bold" style="font-size:1rem;" id="edTitle">Post Detayı</div>
            <button onclick="closeEventDetail()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:1.2rem;padding:0;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="edStatus" class="mb-2"></div>
        <div id="edDate" style="font-size:0.82rem;color:var(--muted);"></div>
        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-sm fw-semibold" style="background:#ede9ff;color:#6c63ff;border:none;border-radius:9px;font-size:0.82rem;">
                <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard'a Git
            </a>
            <button onclick="closeEventDetail()" class="btn btn-sm" style="background:#f3f4f6;color:#6b7280;border:none;border-radius:9px;font-size:0.82rem;">
                Kapat
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'tr',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek',
        },
        buttonText: {
            today: 'Bugün',
            month: 'Ay',
            week:  'Hafta',
        },
        events: function(info, successCallback, failureCallback) {
            fetch('/calendar/events')
                .then(r => r.json())
                .then(data => successCallback(data))
                .catch(err => failureCallback(err));
        },
        eventClick: function(info) {
            openEventDetail(info.event);
        },
        height: 'auto',
        dayMaxEvents: 3,
        eventDisplay: 'block',
        nowIndicator: true,
    });
    calendar.render();
});

function openEventDetail(event) {
    const statusLabels = {
        published:  '<span style="color:#22c55e;font-weight:700;"><i class="bi bi-check-circle-fill me-1"></i>Yayınlandı</span>',
        failed:     '<span style="color:#ef4444;font-weight:700;"><i class="bi bi-x-circle-fill me-1"></i>Başarısız</span>',
        draft:      '<span style="color:#6c63ff;font-weight:700;"><i class="bi bi-bookmark-fill me-1"></i>Taslak</span>',
        publishing: '<span style="color:#f59e0b;font-weight:700;"><i class="bi bi-hourglass-split me-1"></i>Yayınlanıyor</span>',
    };

    document.getElementById('edTitle').textContent = event.title;
    document.getElementById('edStatus').innerHTML  = statusLabels[event.extendedProps.status] || event.extendedProps.status;
    document.getElementById('edDate').innerHTML    = '<i class="bi bi-calendar3 me-1"></i>' + (event.startStr || '—');

    document.getElementById('eventDetailOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEventDetail() {
    document.getElementById('eventDetailOverlay').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeEventDetail();
});
</script>
@endpush
