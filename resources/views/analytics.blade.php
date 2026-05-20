@extends('layouts.app')

@section('title', 'Analitik — SocialHub')
@section('page-title', 'Analitik')
@section('page-sub', 'İçerik performansını analiz et')

@push('styles')
<style>
    .stat-card {
        border-radius: 18px;
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: var(--card-shadow);
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
    .stat-value { font-size: 1.9rem; font-weight: 800; line-height: 1; color: var(--text); }
    .stat-label { font-size: 0.78rem; color: var(--muted); font-weight: 500; margin-top: 3px; }
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
    .chart-container {
        position: relative;
        height: 280px;
    }
</style>
@endpush

@section('content')

{{-- Stat Cards --}}
@php
    $statCards = [
        ['label' => 'Toplam Post',  'value' => $totalPosts,     'icon' => 'file-earmark-text-fill', 'color' => '#6c63ff', 'bg' => '#ede9ff', 'accent' => '#6c63ff'],
        ['label' => 'Yayınlandı',   'value' => $publishedPosts, 'icon' => 'check-circle-fill',       'color' => '#22c55e', 'bg' => '#dcfce7', 'accent' => '#22c55e'],
        ['label' => 'Başarısız',    'value' => $failedPosts,    'icon' => 'x-circle-fill',            'color' => '#ef4444', 'bg' => '#fee2e2', 'accent' => '#ef4444'],
        ['label' => 'Taslak',       'value' => $draftPosts,     'icon' => 'bookmark-fill',            'color' => '#f59e0b', 'bg' => '#fef3c7', 'accent' => '#f59e0b'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($statCards as $stat)
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

{{-- Charts --}}
<div class="row g-4">
    {{-- Daily line chart --}}
    <div class="col-lg-7">
        <div class="sh-card p-4">
            <div class="section-head mb-3"><i class="bi bi-graph-up"></i> Son 14 Gün</div>
            <div class="chart-container">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Platform bar chart --}}
    <div class="col-lg-5">
        <div class="sh-card p-4">
            <div class="section-head mb-3"><i class="bi bi-bar-chart-fill"></i> Platform Performansı</div>
            <div class="chart-container">
                <canvas id="platformChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Platform detail table --}}
<div class="sh-card p-4 mt-4">
    <div class="section-head mb-3"><i class="bi bi-table"></i> Platform Detayları</div>
    <div class="table-responsive">
        <table class="table" style="font-size:0.875rem;">
            <thead>
                <tr style="color:var(--muted);font-size:0.78rem;text-transform:uppercase;letter-spacing:0.5px;">
                    <th style="font-weight:700;border:none;padding:8px 12px;">Platform</th>
                    <th style="font-weight:700;border:none;padding:8px 12px;">Başarılı</th>
                    <th style="font-weight:700;border:none;padding:8px 12px;">Başarısız</th>
                    <th style="font-weight:700;border:none;padding:8px 12px;">Başarı Oranı</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $platformLabels = ['instagram' => 'Instagram', 'twitter' => 'X (Twitter)', 'tiktok' => 'TikTok'];
                    $platformColors = ['instagram' => '#e1306c', 'twitter' => '#000', 'tiktok' => '#010101'];
                    $platformIcons  = ['instagram' => 'instagram', 'twitter' => 'twitter-x', 'tiktok' => 'tiktok'];
                @endphp
                @foreach($platformStats as $platform => $counts)
                @php
                    $total = $counts['published'] + $counts['failed'];
                    $rate  = $total > 0 ? round(($counts['published'] / $total) * 100) : 0;
                @endphp
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px;vertical-align:middle;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-{{ $platformIcons[$platform] }}" style="color:{{ $platformColors[$platform] }};font-size:1.1rem;"></i>
                            <span class="fw-semibold">{{ $platformLabels[$platform] }}</span>
                        </div>
                    </td>
                    <td style="padding:12px;vertical-align:middle;">
                        <span style="color:#22c55e;font-weight:700;">{{ $counts['published'] }}</span>
                    </td>
                    <td style="padding:12px;vertical-align:middle;">
                        <span style="color:#ef4444;font-weight:700;">{{ $counts['failed'] }}</span>
                    </td>
                    <td style="padding:12px;vertical-align:middle;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:80px;height:6px;background:#f3f4f6;border-radius:100px;overflow:hidden;">
                                <div style="width:{{ $rate }}%;height:100%;background:{{ $rate > 70 ? '#22c55e' : ($rate > 40 ? '#f59e0b' : '#ef4444') }};border-radius:100px;"></div>
                            </div>
                            <span class="fw-semibold" style="font-size:0.82rem;">{{ $rate }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const dailyData    = @json($dailyStats);
const platformData = @json($platformStats);

// Dark mode detection
const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
const gridColor = () => isDark() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
const textColor = () => isDark() ? '#94a3b8' : '#9ca3af';

Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";

// Daily Line Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: dailyData.map(d => d.date),
        datasets: [{
            label: 'Yayınlanan Post',
            data: dailyData.map(d => d.count),
            borderColor: '#6c63ff',
            backgroundColor: 'rgba(108,99,255,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#6c63ff',
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.35,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: isDark() ? '#1a1a2e' : '#fff',
                titleColor: isDark() ? '#e2e8f0' : '#1e1e2e',
                bodyColor: '#6c63ff',
                borderColor: isDark() ? '#2a2a40' : '#eff0f6',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 10,
                callbacks: {
                    label: ctx => ' ' + ctx.raw + ' post',
                },
            },
        },
        scales: {
            x: {
                grid: { color: gridColor() },
                ticks: { color: textColor(), font: { size: 11 } },
            },
            y: {
                beginAtZero: true,
                grid: { color: gridColor() },
                ticks: { color: textColor(), font: { size: 11 }, stepSize: 1 },
            },
        },
    },
});

// Platform Bar Chart
const platformCtx = document.getElementById('platformChart').getContext('2d');
new Chart(platformCtx, {
    type: 'bar',
    data: {
        labels: ['Instagram', 'X (Twitter)', 'TikTok'],
        datasets: [
            {
                label: 'Başarılı',
                data: [
                    platformData.instagram?.published ?? 0,
                    platformData.twitter?.published ?? 0,
                    platformData.tiktok?.published ?? 0,
                ],
                backgroundColor: 'rgba(34,197,94,0.75)',
                borderRadius: 8,
                borderSkipped: false,
            },
            {
                label: 'Başarısız',
                data: [
                    platformData.instagram?.failed ?? 0,
                    platformData.twitter?.failed ?? 0,
                    platformData.tiktok?.failed ?? 0,
                ],
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 8,
                borderSkipped: false,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    color: textColor(),
                    font: { size: 12 },
                    usePointStyle: true,
                    padding: 16,
                },
            },
            tooltip: {
                backgroundColor: isDark() ? '#1a1a2e' : '#fff',
                titleColor: isDark() ? '#e2e8f0' : '#1e1e2e',
                bodyColor: isDark() ? '#94a3b8' : '#6b7280',
                borderColor: isDark() ? '#2a2a40' : '#eff0f6',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 10,
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: textColor(), font: { size: 11 } },
            },
            y: {
                beginAtZero: true,
                grid: { color: gridColor() },
                ticks: { color: textColor(), font: { size: 11 }, stepSize: 1 },
            },
        },
    },
});
</script>
@endpush
