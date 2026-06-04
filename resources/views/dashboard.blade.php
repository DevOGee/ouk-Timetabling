@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ═══════════════════════════════ TOKENS ══════════════════════════════ */
:root {
    --teal:        #037b90;
    --teal-dark:   #024d5c;
    --teal-bg:     rgba(3,123,144,.1);
    --coral:       #ff7f50;
    --coral-dark:  #e05c28;
    --coral-bg:    rgba(255,127,80,.1);
    --purple:      #7c3aed;
    --purple-bg:   rgba(124,58,237,.1);
    --green:       #10b981;
    --green-bg:    rgba(16,185,129,.1);
    --amber:       #f59e0b;
    --amber-bg:    rgba(245,158,11,.1);
    --slate-50:    #f8fafc;
    --slate-100:   #f1f5f9;
    --slate-200:   #e2e8f0;
    --slate-300:   #cbd5e1;
    --slate-500:   #64748b;
    --slate-700:   #334155;
    --slate-900:   #0f172a;
    --radius-lg:   20px;
    --radius-md:   14px;
    --radius-sm:   10px;
    --card-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 20px rgba(15,23,42,.06);
    --card-hover:  0 8px 30px rgba(15,23,42,.12);
    --border:      1.5px solid rgba(226,232,240,.9);
}

/* ═══════════════════════════ LAYOUT RESET ════════════════════════════ */
.content-wrapper {
    background: transparent !important;
    box-shadow: none !important;
    padding: 1.8rem 2rem !important;
}

/* ══════════════════════════ PAGE HEADER ══════════════════════════════ */
.db-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.75rem;
}
.db-header-left .eyebrow {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--teal);
    margin-bottom: .3rem;
}
.db-header-left h1 {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--slate-900);
    margin: 0 0 .3rem;
    line-height: 1.15;
    font-family: 'Outfit', sans-serif;
}
.db-header-left .meta {
    display: flex; align-items: center; gap: .5rem;
    font-size: .8rem; color: var(--slate-500);
}
.db-header-left .meta .sep { width: 3px; height: 3px; border-radius: 50%; background: var(--slate-300); }
.db-session-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    background: linear-gradient(135deg, var(--teal), var(--teal-dark));
    color: #fff; font-size: .72rem; font-weight: 700;
    padding: .22rem .8rem; border-radius: 100px; letter-spacing: .03em;
}
.db-session-badge .live-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: rgba(255,255,255,.7);
    animation: livePulse 2s ease-in-out infinite;
}
@keyframes livePulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.4; transform:scale(.75); }
}
.db-header-actions { display: flex; gap: .6rem; flex-wrap: wrap; }

/* Buttons */
.btn-db-primary {
    display: inline-flex; align-items: center; gap: .45rem;
    background: linear-gradient(135deg, var(--teal), var(--teal-dark));
    color: #fff; border: none; border-radius: var(--radius-sm);
    padding: .55rem 1.15rem; font-size: .83rem; font-weight: 700;
    text-decoration: none; cursor: pointer;
    box-shadow: 0 4px 14px rgba(3,123,144,.28);
    transition: transform .2s, box-shadow .2s, filter .2s;
}
.btn-db-primary:hover { color:#fff; transform:translateY(-2px); box-shadow:0 8px 22px rgba(3,123,144,.38); filter:brightness(1.05); }
.btn-db-outline {
    display: inline-flex; align-items: center; gap: .45rem;
    background: #fff; color: var(--slate-700);
    border: var(--border); border-radius: var(--radius-sm);
    padding: .52rem 1.1rem; font-size: .83rem; font-weight: 600;
    text-decoration: none; transition: all .2s;
}
.btn-db-outline:hover { color: var(--teal); border-color: var(--teal); background: var(--teal-bg); }

/* ══════════════════════════ KPI CARDS ════════════════════════════════ */
.kpi-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.kpi-grid-4 { grid-template-columns: repeat(4, 1fr); }
.kpi-grid-2 { grid-template-columns: repeat(2, 1fr); max-width: 540px; }
@media (max-width: 1100px) { .kpi-grid-4 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px)  { .kpi-grid-4, .kpi-grid-2 { grid-template-columns: 1fr; } }

.kpi-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: var(--border);
    padding: 1.35rem 1.4rem 1.2rem;
    box-shadow: var(--card-shadow);
    display: flex;
    flex-direction: column;
    gap: .75rem;
    position: relative;
    overflow: hidden;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
    animation: fadeUp .45s ease both;
}
.kpi-card:hover { transform: translateY(-5px); box-shadow: var(--card-hover); }

/* Top accent bar */
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3.5px;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.kpi-card.c-teal::before   { background: linear-gradient(90deg, var(--teal),   #05bcd4); }
.kpi-card.c-coral::before  { background: linear-gradient(90deg, var(--coral),  #ffb347); }
.kpi-card.c-purple::before { background: linear-gradient(90deg, var(--purple), #a78bfa); }
.kpi-card.c-green::before  { background: linear-gradient(90deg, var(--green),  #34d399); }

/* Stagger */
.kpi-card:nth-child(1){animation-delay:.05s}
.kpi-card:nth-child(2){animation-delay:.1s}
.kpi-card:nth-child(3){animation-delay:.15s}
.kpi-card:nth-child(4){animation-delay:.2s}

.kpi-top { display: flex; align-items: flex-start; justify-content: space-between; }
.kpi-icon {
    width: 50px; height: 50px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.kpi-icon.c-teal   { background: var(--teal-bg);   color: var(--teal); }
.kpi-icon.c-coral  { background: var(--coral-bg);  color: var(--coral); }
.kpi-icon.c-purple { background: var(--purple-bg); color: var(--purple); }
.kpi-icon.c-green  { background: var(--green-bg);  color: var(--green); }

.kpi-trend {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .7rem; font-weight: 700;
    padding: .18rem .55rem; border-radius: 6px;
    margin-top: .25rem;
}
.kpi-trend.up   { background: var(--green-bg);  color: #059669; }
.kpi-trend.info { background: var(--teal-bg);   color: var(--teal); }
.kpi-trend.warn { background: var(--amber-bg);  color: #92400e; }

.kpi-value {
    font-size: 2.1rem; font-weight: 900; color: var(--slate-900);
    line-height: 1; letter-spacing: -.04em;
    font-family: 'Outfit', sans-serif;
}
.kpi-label {
    font-size: .75rem; font-weight: 700; color: var(--slate-500);
    text-transform: uppercase; letter-spacing: .07em;
    margin-top: .2rem;
}
.kpi-detail {
    font-size: .78rem; color: var(--slate-500); margin-top: .15rem;
}

/* ══════════════════════════ SECTION LABEL ════════════════════════════ */
.sec-label {
    font-size: .7rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .1em; color: var(--slate-300);
    display: flex; align-items: center; gap: .6rem;
    margin: 1.5rem 0 1rem;
}
.sec-label::after { content:''; flex:1; height:1px; background: var(--slate-100); }

/* ══════════════════════════ CHART CARDS ══════════════════════════════ */
.charts-row-1 {
    display: grid;
    grid-template-columns: 1fr 1fr 260px;
    gap: 1rem;
    margin-bottom: 1rem;
}
.charts-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1200px) { .charts-row-1 { grid-template-columns: 1fr 1fr; } }
@media (max-width: 860px)  { .charts-row-1, .charts-row-2 { grid-template-columns: 1fr; } }

.chart-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: var(--border);
    padding: 1.35rem 1.4rem;
    box-shadow: var(--card-shadow);
    display: flex; flex-direction: column;
    animation: fadeUp .5s ease both;
    animation-delay: .25s;
}
.chart-card-head {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: .5rem;
    margin-bottom: 1.1rem; flex-wrap: wrap;
}
.chart-card-title {
    font-size: .9rem; font-weight: 800; color: var(--slate-900);
    font-family: 'Outfit', sans-serif; margin: 0;
}
.chart-card-sub { font-size: .73rem; color: var(--slate-500); margin-top: .12rem; }
.chart-badge {
    font-size: .68rem; font-weight: 700;
    padding: .2rem .65rem; border-radius: 100px;
    background: var(--teal-bg); color: var(--teal);
    white-space: nowrap; flex-shrink: 0;
}

/* Donut legend */
.donut-legend { display: flex; flex-wrap: wrap; gap: .4rem .75rem; justify-content: center; margin-top: .8rem; }
.donut-legend-item { display: flex; align-items: center; gap: .3rem; font-size: .71rem; font-weight: 600; color: var(--slate-500); }
.donut-legend-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* ══════════════════════════ BOTTOM GRID ══════════════════════════════ */
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 1rem;
}
@media (max-width: 880px) { .bottom-grid { grid-template-columns: 1fr; } }

/* ── Schedule card ── */
.schedule-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: var(--border);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    display: flex; flex-direction: column;
    animation: fadeUp .5s ease both; animation-delay: .3s;
}
.schedule-header {
    padding: 1.1rem 1.4rem;
    background: linear-gradient(135deg, var(--teal) 0%, var(--teal-dark) 100%);
    color: #fff;
    display: flex; align-items: center; justify-content: space-between; gap: .5rem;
}
.schedule-header-left h3 {
    font-size: .95rem; font-weight: 800; margin: 0;
    display: flex; align-items: center; gap: .45rem;
    font-family: 'Outfit', sans-serif;
}
.schedule-header-left .date { font-size: .73rem; opacity: .72; margin-top: .18rem; }
.events-count {
    background: rgba(255,255,255,.18); backdrop-filter: blur(4px);
    border-radius: 100px; padding: .28rem .85rem;
    font-size: .78rem; font-weight: 700; white-space: nowrap;
}
.schedule-body { flex: 1; overflow-y: auto; }

/* Event rows */
.event-row {
    display: flex; align-items: flex-start; gap: .9rem;
    padding: .9rem 1.4rem;
    border-bottom: 1px solid var(--slate-100);
    transition: background .15s;
}
.event-row:last-child { border-bottom: none; }
.event-row:hover { background: var(--slate-50); }

.event-time {
    font-size: .72rem; font-weight: 800; color: var(--teal);
    min-width: 40px; padding-top: 2px; flex-shrink: 0;
}
.event-indicator { display: flex; flex-direction: column; align-items: center; gap: 3px; padding-top: 3px; }
.event-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; border: 2px solid #fff; }
.event-line { width: 2px; height: 20px; border-radius: 2px; }
.event-body { flex: 1; min-width: 0; }
.event-course { font-size: .855rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.event-prog   { font-size: .74rem; color: var(--slate-500); margin-top: .1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.session-tag {
    font-size: .68rem; font-weight: 700;
    padding: .18rem .58rem; border-radius: 100px;
    white-space: nowrap; flex-shrink: 0; align-self: flex-start;
}
.tag-morning { background: var(--amber-bg);  color: #92400e; }
.tag-evening { background: var(--purple-bg); color: var(--purple); }

/* Empty state */
.empty-state {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 3rem 2rem; text-align: center; flex:1;
}
.empty-state-icon {
    width: 60px; height: 60px; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; margin-bottom: .85rem;
    background: var(--teal-bg); color: var(--teal);
}
.empty-state-title { font-size: .9rem; font-weight: 700; color: var(--slate-700); margin-bottom: .3rem; }
.empty-state-sub   { font-size: .77rem; color: var(--slate-500); }

/* ── Quick Actions ── */
.actions-card {
    background: #fff;
    border-radius: var(--radius-lg);
    border: var(--border);
    padding: 1.35rem 1.25rem;
    box-shadow: var(--card-shadow);
    display: flex; flex-direction: column; gap: .45rem;
    animation: fadeUp .5s ease both; animation-delay: .32s;
}
.action-row {
    display: flex; align-items: center; gap: .8rem;
    padding: .8rem .9rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s;
}
.action-row:hover { transform: translateX(5px); }
.action-row:hover .action-arrow { opacity: 1; }
.action-row.c-teal   { background: var(--teal-bg); }
.action-row.c-coral  { background: var(--coral-bg); }
.action-row.c-purple { background: var(--purple-bg); }
.action-row.c-green  { background: var(--green-bg); }
.action-row.c-amber  { background: var(--amber-bg); }
.action-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; color: #fff; flex-shrink: 0;
}
.action-icon.c-teal   { background: var(--teal); }
.action-icon.c-coral  { background: var(--coral); }
.action-icon.c-purple { background: var(--purple); }
.action-icon.c-green  { background: var(--green); }
.action-icon.c-amber  { background: var(--amber); }
.action-text { flex: 1; min-width: 0; }
.action-title { font-size: .825rem; font-weight: 700; color: var(--slate-900); line-height: 1.2; }
.action-sub   { font-size: .7rem; color: var(--slate-500); margin-top: .08rem; }
.action-arrow { font-size: .75rem; color: var(--slate-300); opacity: 0; transition: opacity .2s; }

/* ── Unmapped alert ── */
.unmapped-item {
    display: flex; align-items: flex-start; gap: .7rem;
    padding: .7rem; border-radius: var(--radius-sm);
    background: rgba(255,127,80,.06);
    border: 1px solid rgba(255,127,80,.18);
    margin-bottom: .5rem;
}
.unmapped-item:last-child { margin-bottom: 0; }
.unmapped-dot {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    background: var(--coral-bg); color: var(--coral); font-size: .8rem; flex-shrink: 0;
}

/* ══════════════════════════ ANIMATIONS ═══════════════════════════════ */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@section('content')
@php
    $today     = \Carbon\Carbon::today();
    $hour      = now()->hour;
    $greeting  = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
    $firstName = explode(' ', auth()->user()->name ?? 'User')[0];

    // Determine role label
    $roleLabel = $isInstructor ? 'Instructor' : ($isTimetabler ? 'Timetabler' : 'Administrator');
@endphp

{{-- ═════════════════════ PAGE HEADER ══════════════════════════ --}}
<div class="db-header">
    <div class="db-header-left">
        <div class="eyebrow">{{ $greeting }}, {{ $firstName }} 👋</div>
        <h1>Dashboard</h1>
        <div class="meta">
            <span>{{ $today->format('l, d M Y') }}</span>
            @if($activeSession)
                <span class="sep"></span>
                <span class="db-session-badge">
                    <span class="live-dot"></span>
                    {{ $activeSession->name }}
                </span>
            @endif
            <span class="sep"></span>
            <span>{{ $roleLabel }}</span>
        </div>
    </div>
    <div class="db-header-actions">
        @if($isInstructor)
            <a href="{{ route('instructor.timetable') }}" class="btn-db-outline">
                <i class="bi bi-calendar3"></i> My Timetable
            </a>
        @else
            <a href="{{ route('timetable.index') }}" class="btn-db-outline">
                <i class="bi bi-calendar3"></i> View Timetable
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn-db-primary">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
        @endif
    </div>
</div>

{{-- ═════════════════════ KPI CARDS ════════════════════════════ --}}

@if($isInstructor)
{{-- ── Instructor KPIs ── --}}
<div class="kpi-grid kpi-grid-2">
    <div class="kpi-card c-teal">
        <div class="kpi-top">
            <div class="kpi-icon c-teal"><i class="bi bi-journal-text"></i></div>
            <span class="kpi-trend info"><i class="bi bi-mortarboard"></i> This Session</span>
        </div>
        <div>
            <div class="kpi-value">{{ $stats['courseUnitsTeaching'] }}</div>
            <div class="kpi-label">Course Units</div>
            <div class="kpi-detail">Assigned to you this semester</div>
        </div>
    </div>
    <div class="kpi-card c-coral">
        <div class="kpi-top">
            <div class="kpi-icon c-coral"><i class="bi bi-building"></i></div>
            <span class="kpi-trend info"><i class="bi bi-diagram-3"></i> Active</span>
        </div>
        <div>
            <div class="kpi-value">{{ $stats['programmesTeaching'] }}</div>
            <div class="kpi-label">Programmes</div>
            <div class="kpi-detail">You are currently teaching in</div>
        </div>
    </div>
</div>

@else
{{-- ── Admin / Timetabler KPIs ── --}}
<div class="kpi-grid kpi-grid-4">

    {{-- Programmes --}}
    <div class="kpi-card c-teal">
        <div class="kpi-top">
            <div class="kpi-icon c-teal"><i class="bi bi-building"></i></div>
            @php $mapped = $stats['programmes'] - $unmappedProgrammes->count(); @endphp
            <span class="kpi-trend up"><i class="bi bi-check2-circle"></i> {{ $mapped }} mapped</span>
        </div>
        <div>
            <div class="kpi-value counter" data-target="{{ $stats['programmes'] }}">0</div>
            <div class="kpi-label">Programmes</div>
            <div class="kpi-detail">
                @if($unmappedProgrammes->count() > 0)
                    <span style="color:var(--coral);font-weight:600;">{{ $unmappedProgrammes->count() }} unmapped</span>
                @else
                    All programmes mapped ✓
                @endif
            </div>
        </div>
    </div>

    {{-- Instructors --}}
    <div class="kpi-card c-coral">
        @php
            $zoomLicensed = isset($chartData['zoom']) ? $chartData['zoom']['licensed'] : 0;
            $totalAssigned = isset($chartData['zoom']) ? ($chartData['zoom']['licensed'] + $chartData['zoom']['unlicensed']) : 0;
        @endphp
        <div class="kpi-top">
            <div class="kpi-icon c-coral"><i class="bi bi-people"></i></div>
            <span class="kpi-trend info"><i class="bi bi-camera-video"></i> {{ $zoomLicensed }} licensed</span>
        </div>
        <div>
            <div class="kpi-value counter" data-target="{{ $stats['instructors'] }}">0</div>
            <div class="kpi-label">Instructors</div>
            <div class="kpi-detail">{{ $totalAssigned }} assigned to session</div>
        </div>
    </div>

    {{-- Course Units --}}
    <div class="kpi-card c-purple">
        @php
            $perDayTotal = isset($chartData['perDay']) ? array_sum($chartData['perDay']['values']) : 0;
        @endphp
        <div class="kpi-top">
            <div class="kpi-icon c-purple"><i class="bi bi-journal-bookmark"></i></div>
            <span class="kpi-trend info"><i class="bi bi-calendar-week"></i> {{ $perDayTotal }} slots</span>
        </div>
        <div>
            <div class="kpi-value counter" data-target="{{ $stats['courseUnits'] }}">0</div>
            <div class="kpi-label">Course Units</div>
            <div class="kpi-detail">Across all programmes</div>
        </div>
    </div>

    {{-- Academic Sessions --}}
    <div class="kpi-card c-green">
        <div class="kpi-top">
            <div class="kpi-icon c-green"><i class="bi bi-calendar-check"></i></div>
            <span class="kpi-trend {{ $activeSession ? 'up' : 'warn' }}">
                <i class="bi bi-{{ $activeSession ? 'check-circle' : 'exclamation-circle' }}"></i>
                {{ $activeSession ? 'Active' : 'None Active' }}
            </span>
        </div>
        <div>
            <div class="kpi-value counter" data-target="{{ $stats['academicSessions'] }}">0</div>
            <div class="kpi-label">Academic Sessions</div>
            <div class="kpi-detail">
                @if($activeSession)
                    Current: <strong>{{ Str::limit($activeSession->name, 30) }}</strong>
                @else
                    No active session set
                @endif
            </div>
        </div>
    </div>

</div>
@endif

{{-- ═════════════════════ CHARTS (Admin/Timetabler) ════════════════ --}}
@if(!$isInstructor && !empty($chartData))
@php
    $schoolPalette = ['#037b90','#ff7f50','#7c3aed','#10b981','#f59e0b','#ec4899'];
    $zoomL = $chartData['zoom']['licensed'] ?? 0;
    $zoomU = $chartData['zoom']['unlicensed'] ?? 0;
    $zoomT = $zoomL + $zoomU;
    $zoomPct = $zoomT > 0 ? round(($zoomL / $zoomT) * 100) : 0;
@endphp

<div class="sec-label">Analytics</div>

{{-- Row 1: Bar + Donut + Zoom --}}
<div class="charts-row-1">

    <div class="chart-card">
        <div class="chart-card-head">
            <div>
                <div class="chart-card-title">Classes Per Day</div>
                <div class="chart-card-sub">Scheduled slots this session</div>
            </div>
            <span class="chart-badge"><i class="bi bi-bar-chart me-1"></i>Weekly</span>
        </div>
        <canvas id="perDayChart" style="max-height:175px;"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-card-head">
            <div>
                <div class="chart-card-title">Instructors by School</div>
                <div class="chart-card-sub">Distribution across faculties</div>
            </div>
            <span class="chart-badge"><i class="bi bi-pie-chart me-1"></i>Faculty</span>
        </div>
        <canvas id="schoolChart" style="max-height:185px;"></canvas>
    </div>

    <div class="chart-card" style="align-items:center;">
        <div class="chart-card-head" style="width:100%;">
            <div>
                <div class="chart-card-title">Zoom Licenses</div>
                <div class="chart-card-sub">Instructor coverage</div>
            </div>
        </div>
        <div style="display:flex;justify-content:center;width:100%;">
            <canvas id="zoomChart" style="max-width:160px;max-height:160px;"></canvas>
        </div>
        <div class="donut-legend">
            <div class="donut-legend-item">
                <div class="donut-legend-dot" style="background:var(--teal);"></div>
                Licensed ({{ $zoomL }})
            </div>
            <div class="donut-legend-item">
                <div class="donut-legend-dot" style="background:var(--slate-200);"></div>
                Unlicensed ({{ $zoomU }})
            </div>
        </div>
    </div>

</div>

{{-- Row 2: Programmes by School + Unmapped --}}
<div class="charts-row-2">

    <div class="chart-card">
        <div class="chart-card-head">
            <div>
                <div class="chart-card-title">Programmes by School</div>
                <div class="chart-card-sub">Total programmes per faculty</div>
            </div>
            <span class="chart-badge"><i class="bi bi-building me-1"></i>Schools</span>
        </div>
        <canvas id="progChart" style="max-height:155px;"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-card-head">
            <div>
                <div class="chart-card-title">Unmapped Programmes</div>
                <div class="chart-card-sub">Missing course unit mappings</div>
            </div>
            @if($unmappedProgrammes->isEmpty())
                <span class="chart-badge" style="background:var(--green-bg);color:var(--green);">
                    <i class="bi bi-check-circle me-1"></i>All Good
                </span>
            @else
                <span class="chart-badge" style="background:var(--coral-bg);color:var(--coral);">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $unmappedProgrammes->count() }} Issues
                </span>
            @endif
        </div>
        @if($unmappedProgrammes->isEmpty())
            <div class="empty-state" style="padding:1.5rem 1rem;">
                <div class="empty-state-icon" style="background:var(--green-bg);color:var(--green);font-size:1.4rem;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="empty-state-title">All Programmes Mapped</div>
                <div class="empty-state-sub">Every programme has course units in this session</div>
            </div>
        @else
            <div style="flex:1;overflow-y:auto;max-height:185px;">
                @foreach($unmappedProgrammes as $prog)
                <div class="unmapped-item">
                    <div class="unmapped-dot"><i class="bi bi-exclamation-circle"></i></div>
                    <div>
                        <div style="font-size:.82rem;font-weight:700;color:var(--slate-900);">{{ $prog->programme_code }}</div>
                        <div style="font-size:.73rem;color:var(--slate-500);margin-top:.08rem;">{{ Str::limit($prog->name, 55) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endif

{{-- ═════════════════════ SCHEDULE + ACTIONS ═══════════════════════ --}}
<div class="sec-label">Today & Quick Actions</div>
<div class="bottom-grid">

    {{-- ── Today's Schedule ── --}}
    <div class="schedule-card">
        <div class="schedule-header">
            <div class="schedule-header-left">
                <h3>
                    @if($isExamPeriod)
                        <i class="bi bi-clipboard-check"></i> Today's Exams
                    @else
                        <i class="bi bi-calendar-day"></i> Today's Classes
                    @endif
                </h3>
                <div class="date">{{ $today->format('l, d F Y') }}</div>
            </div>
            <div class="events-count">
                {{ $todaysEvents->count() }} {{ $todaysEvents->count() === 1 ? 'event' : 'events' }}
            </div>
        </div>

        <div class="schedule-body">
            @if($todaysEvents->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-moon-stars"></i></div>
                    <div class="empty-state-title">No Classes Today</div>
                    <div class="empty-state-sub">Nothing scheduled — enjoy your day! 🎉</div>
                </div>
            @else
                @foreach($todaysEvents as $event)
                @php
                    $time      = \Carbon\Carbon::parse($event->start_time)->format('H:i');
                    $sessType  = $event->type ?? ($event->session ?? 'N/A');
                    $isMorning = str_contains(strtolower($sessType), 'morning');
                    $dotColor  = $isMorning ? '#f59e0b' : '#7c3aed';
                @endphp
                <div class="event-row">
                    <div class="event-time">{{ $time }}</div>
                    <div class="event-indicator">
                        <div class="event-dot" style="background:{{ $dotColor }}; box-shadow:0 0 0 2px #fff, 0 0 0 3px {{ $dotColor }};"></div>
                        <div class="event-line" style="background:linear-gradient(to bottom, {{ $dotColor }}, transparent);"></div>
                    </div>
                    <div class="event-body">
                        <div class="event-course">
                            {{ $event->courseUnit->code ?? '' }}
                            @if(!empty($event->courseUnit->name))
                                — {{ Str::limit($event->courseUnit->name, 46) }}
                            @endif
                        </div>
                        <div class="event-prog">{{ $event->programme->name ?? ($event->programme ?? '') }}</div>
                        
                        @if(isset($event->instructor) && !empty($event->instructor->id))
                            <div style="margin-top: 0.5rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; background: var(--slate-50); padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--slate-100);">
                                <div style="display: flex; flex-direction: column; min-width: 0;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--slate-700);"><i class="bi bi-person-badge"></i> {{ $event->instructor->name }}</span>
                                    <span style="font-size: 0.7rem; color: var(--slate-500); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $event->instructor->email }}</span>
                                </div>
                                <form action="{{ route('users.toggle-zoom', $event->instructor->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @if($event->instructor->zoom_email)
                                        <button type="submit" class="btn-db-outline" title="Remove Zoom License" style="padding: 0.25rem 0.5rem; font-size: 0.65rem; border-color: var(--teal); color: var(--teal); background: var(--teal-bg);">
                                            <i class="bi bi-camera-video-fill"></i> Unassign
                                        </button>
                                    @else
                                        <button type="submit" class="btn-db-outline" title="Assign Zoom License" style="padding: 0.25rem 0.5rem; font-size: 0.65rem;">
                                            <i class="bi bi-camera-video"></i> Assign
                                        </button>
                                    @endif
                                </form>
                            </div>
                        @endif
                    </div>
                    <span class="session-tag {{ $isMorning ? 'tag-morning' : 'tag-evening' }}">{{ $sessType }}</span>
                </div>
                @endforeach
                <div style="padding: 1rem; text-align: center; border-top: 1px solid var(--slate-100); background: var(--slate-50);">
                    <a href="{{ route('dashboard.todays-events') }}" style="font-size: 0.8rem; font-weight: 700; color: var(--teal); text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; transition: color 0.2s;">
                        View More <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Quick Actions ── --}}
    <div class="actions-card">
        <div style="margin-bottom:.75rem;">
            <div class="chart-card-title">Quick Actions</div>
            <div class="chart-card-sub">Common tasks at a glance</div>
        </div>

        @if($isInstructor)
            <a href="{{ route('instructor.timetable') }}" class="action-row c-teal" id="qa-my-tt">
                <div class="action-icon c-teal"><i class="bi bi-calendar3"></i></div>
                <div class="action-text">
                    <div class="action-title">My Timetable</div>
                    <div class="action-sub">View your personal schedule</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
            <a href="{{ route('instructor.course-units') }}" class="action-row c-coral" id="qa-my-cu">
                <div class="action-icon c-coral"><i class="bi bi-journal-text"></i></div>
                <div class="action-text">
                    <div class="action-title">My Course Units</div>
                    <div class="action-sub">Units assigned to you</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
        @else
            <a href="{{ route('timetable.index') }}" class="action-row c-teal" id="qa-tt">
                <div class="action-icon c-teal"><i class="bi bi-calendar3"></i></div>
                <div class="action-text">
                    <div class="action-title">View Timetable</div>
                    <div class="action-sub">Browse published schedules</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
            <a href="{{ $activeSession ? url('/admin/academic-sessions/'.$activeSession->id) : route('admin.academic-sessions.index') }}"
               class="action-row c-coral" id="qa-session">
                <div class="action-icon c-coral"><i class="bi bi-diagram-3"></i></div>
                <div class="action-text">
                    <div class="action-title">Course Mapping</div>
                    <div class="action-sub">Map courses to programmes</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="action-row c-purple" id="qa-reports">
                <div class="action-icon c-purple"><i class="bi bi-file-earmark-bar-graph"></i></div>
                <div class="action-text">
                    <div class="action-title">Reports</div>
                    <div class="action-sub">Workload, conflicts & exports</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
            <a href="{{ route('admin.academic-sessions.index') }}" class="action-row c-green" id="qa-sessions">
                <div class="action-icon c-green"><i class="bi bi-calendar-range"></i></div>
                <div class="action-text">
                    <div class="action-title">Academic Sessions</div>
                    <div class="action-sub">Manage & publish sessions</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
            @if($isAdmin)
            <a href="{{ route('admin.users.index') }}" class="action-row c-amber" id="qa-users">
                <div class="action-icon c-amber"><i class="bi bi-people"></i></div>
                <div class="action-text">
                    <div class="action-title">User Management</div>
                    <div class="action-sub">Manage roles & accounts</div>
                </div>
                <i class="bi bi-chevron-right action-arrow"></i>
            </a>
            @endif
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ══ Animated Counters (IntersectionObserver) ══ */
    const IO = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            const target = parseInt(el.dataset.target, 10) || 0;
            const dur    = 1100;
            const start  = performance.now();
            const ease   = t => 1 - Math.pow(1 - t, 4);
            const tick   = now => {
                const p = Math.min((now - start) / dur, 1);
                el.textContent = Math.round(ease(p) * target).toLocaleString();
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            IO.unobserve(el);
        });
    }, { threshold: 0.2 });
    document.querySelectorAll('.counter').forEach(el => IO.observe(el));

    @if(!$isInstructor && !empty($chartData))
    /* ══ Shared Chart Defaults ══ */
    const TEAL   = '#037b90';
    const CORAL  = '#ff7f50';
    const PURPLE = '#7c3aed';
    const GREEN  = '#10b981';
    const AMBER  = '#f59e0b';
    const PINK   = '#ec4899';
    const palette = [TEAL, CORAL, PURPLE, GREEN, AMBER, PINK];

    const tooltip = {
        backgroundColor: '#0f172a',
        titleColor: '#f8fafc',
        bodyColor: '#94a3b8',
        cornerRadius: 10,
        padding: 10,
        boxPadding: 4,
    };

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color       = '#64748b';

    /* ══ Classes Per Day (vertical bar) ══ */
    const pdEl = document.getElementById('perDayChart');
    if (pdEl) {
        const labels = @json($chartData['perDay']['labels']);
        const values = @json($chartData['perDay']['values']);
        const maxVal = Math.max(...values, 1);
        const colors = labels.map(l => l === 'Thursday' ? CORAL : TEAL);

        new Chart(pdEl, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Classes',
                    data: values,
                    backgroundColor: colors,
                    borderRadius: { topLeft: 8, topRight: 8 },
                    borderSkipped: false,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 900, easing: 'easeOutQuart' },
                plugins: { legend: { display: false }, tooltip },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: {
                        border: { display: false },
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: Math.ceil(maxVal / 5) || 1 }
                    }
                }
            }
        });
    }

    /* ══ Instructors by School (donut) ══ */
    const schEl = document.getElementById('schoolChart');
    if (schEl) {
        new Chart(schEl, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['perSchool']['labels']),
                datasets: [{
                    data: @json($chartData['perSchool']['values']),
                    backgroundColor: palette,
                    borderWidth: 3, borderColor: '#fff', hoverOffset: 8,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '64%',
                animation: { duration: 900 },
                plugins: {
                    tooltip,
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }

    /* ══ Zoom Licenses (donut + center text) ══ */
    const zmEl = document.getElementById('zoomChart');
    if (zmEl) {
        const licensed   = {{ $chartData['zoom']['licensed'] ?? 0 }};
        const unlicensed = {{ $chartData['zoom']['unlicensed'] ?? 0 }};
        const total      = licensed + unlicensed;
        const pct        = total > 0 ? Math.round((licensed / total) * 100) : 0;

        new Chart(zmEl, {
            type: 'doughnut',
            data: {
                labels: ['Licensed', 'Unlicensed'],
                datasets: [{
                    data: [licensed, unlicensed],
                    backgroundColor: [TEAL, '#e2e8f0'],
                    borderWidth: 3, borderColor: '#fff', hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, cutout: '70%',
                animation: { duration: 900 },
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltip, callbacks: { label: c => '  ' + c.label + ': ' + c.parsed } }
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw(chart) {
                    const { ctx, chartArea: { left, right, top, bottom } } = chart;
                    const cx = (left + right) / 2, cy = (top + bottom) / 2;
                    ctx.save();
                    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                    ctx.font = 'bold 20px Outfit, sans-serif';
                    ctx.fillStyle = '#0f172a';
                    ctx.fillText(pct + '%', cx, cy - 7);
                    ctx.font = '600 10px Inter, sans-serif';
                    ctx.fillStyle = '#94a3b8';
                    ctx.fillText('Licensed', cx, cy + 11);
                    ctx.restore();
                }
            }]
        });
    }

    /* ══ Programmes by School (horizontal bar) ══ */
    const pgEl = document.getElementById('progChart');
    if (pgEl) {
        new Chart(pgEl, {
            type: 'bar',
            data: {
                labels: @json($chartData['progPerSchool']['labels']),
                datasets: [{
                    label: 'Programmes',
                    data: @json($chartData['progPerSchool']['values']),
                    backgroundColor: palette,
                    borderRadius: { topRight: 8, bottomRight: 8 },
                    borderSkipped: 'left',
                    maxBarThickness: 26,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 900, easing: 'easeOutQuart' },
                plugins: { legend: { display: false }, tooltip },
                scales: {
                    x: { border: { display: false }, grid: { color: '#f1f5f9' } },
                    y: { border: { display: false }, grid: { display: false } }
                }
            }
        });
    }
    @endif /* !isInstructor */

});
</script>
@endpush
