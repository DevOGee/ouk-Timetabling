@extends('layouts.app')
@section('title', $examSchedule->name . ' — Exam Schedule')

@push('styles')
<style>
:root {
    --teal:        #037b90;
    --teal-dark:   #024d5c;
    --teal-bg:     rgba(3,123,144,.1);
    --coral:       #ff7f50;
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
    --border:      1.5px solid rgba(226,232,240,.9);
}

.content-wrapper {
    background: transparent !important;
    box-shadow: none !important;
    padding: 1.8rem 2rem !important;
}

/* ── Page Animations ── */
.page-fade-in { animation: fadeIn 0.4s cubic-bezier(.34,1.56,.64,1); }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.stagger-1 { animation: fadeIn 0.4s cubic-bezier(.34,1.56,.64,1) 0.05s both; }
.stagger-2 { animation: fadeIn 0.4s cubic-bezier(.34,1.56,.64,1) 0.12s both; }
.stagger-3 { animation: fadeIn 0.4s cubic-bezier(.34,1.56,.64,1) 0.20s both; }

/* ── Header ── */
.page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    margin-bottom: 1.75rem; gap: 1rem; flex-wrap: wrap;
}
.page-header-left { display: flex; align-items: flex-start; gap: 1rem; }
.header-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: var(--teal-bg); color: var(--teal);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.header-title { font-size: 1.7rem; font-weight: 800; color: var(--slate-900); margin: 0; font-family: 'Outfit', sans-serif; line-height: 1.2; }
.header-subtitle { font-size: .82rem; color: var(--slate-500); margin: .2rem 0 0; display: flex; align-items: center; gap: 0.4rem; }
.header-actions { display: flex; gap: .65rem; flex-wrap: wrap; align-items: center; }

/* ── Breadcrumb ── */
.breadcrumb-nav {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.78rem; color: var(--slate-500);
    margin-bottom: 1.25rem;
}
.breadcrumb-nav a { color: var(--slate-500); text-decoration: none; transition: color .15s; }
.breadcrumb-nav a:hover { color: var(--teal); }
.breadcrumb-nav .sep { color: var(--slate-300); }
.breadcrumb-nav .current { color: var(--slate-700); font-weight: 600; }

/* ── Stat Cards ── */
.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card {
    background: #fff; border-radius: var(--radius-md);
    border: var(--border); box-shadow: var(--card-shadow);
    padding: 1.1rem 1.25rem; display: flex; align-items: center; gap: 0.85rem;
    transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(15,23,42,.1); }
.stat-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.stat-info { flex: 1; min-width: 0; }
.stat-value { font-size: 1.4rem; font-weight: 800; color: var(--slate-900); line-height: 1; }
.stat-label { font-size: .72rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: .04em; margin-top: .2rem; }

/* ── Buttons ── */
.btn-premium {
    background: var(--teal); color: #fff; border: none;
    padding: .6rem 1.15rem; font-size: .83rem; font-weight: 700;
    border-radius: var(--radius-sm); cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;
}
.btn-premium:hover { background: var(--teal-dark); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(3,123,144,.2); }

.btn-outline-soft {
    background: #fff; color: var(--slate-700);
    border: 1px solid var(--slate-200);
    padding: .6rem 1.15rem; font-size: .83rem; font-weight: 600;
    border-radius: var(--radius-sm); cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;
}
.btn-outline-soft:hover { border-color: var(--teal); color: var(--teal); background: var(--teal-bg); }

.btn-outline-coral {
    background: #fff; color: var(--coral);
    border: 1px solid rgba(255,127,80,.3);
    padding: .6rem 1.15rem; font-size: .83rem; font-weight: 600;
    border-radius: var(--radius-sm); cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none;
}
.btn-outline-coral:hover { background: var(--coral-bg); color: var(--coral); }

/* ── Search / Filter Card ── */
.filter-card {
    background: #fff; border-radius: var(--radius-md); border: var(--border);
    padding: 1rem 1.25rem; margin-bottom: 1.25rem; box-shadow: var(--card-shadow);
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
}
.search-input-wrap { position: relative; flex: 1; min-width: 220px; }
.search-input-wrap i { position: absolute; left: .7rem; top: 50%; transform: translateY(-50%); color: var(--slate-500); font-size: .85rem; pointer-events: none; }
.search-input-field {
    width: 100%; padding: .6rem .85rem .6rem 2.1rem;
    font-size: .85rem; border: 1px solid var(--slate-200);
    border-radius: var(--radius-sm); color: var(--slate-900);
    background: var(--slate-50); outline: none; transition: all .2s;
}
.search-input-field:focus { border-color: var(--teal); background: #fff; box-shadow: 0 0 0 3px var(--teal-bg); }

/* ── Table Card ── */
.table-card {
    background: #fff; border-radius: var(--radius-lg);
    border: var(--border); box-shadow: var(--card-shadow); overflow: hidden;
}
.table-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--slate-100);
    flex-wrap: wrap; gap: .75rem;
}
.table-card-title { font-size: 1rem; font-weight: 800; color: var(--slate-900); margin: 0; display: flex; align-items: center; gap: .5rem; }

.custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.custom-table th {
    background: var(--slate-50); padding: .85rem 1.25rem;
    font-size: .72rem; font-weight: 700; color: var(--slate-500);
    text-transform: uppercase; letter-spacing: .05em;
    border-bottom: 1px solid var(--slate-200);
}
.custom-table td {
    padding: 1rem 1.25rem; font-size: .855rem; color: var(--slate-700);
    border-bottom: 1px solid var(--slate-100); vertical-align: middle;
}
.custom-table tr:last-child td { border-bottom: none; }
.custom-table tr { transition: background 0.15s; }
.custom-table tr:hover td { background: var(--slate-50); }

/* ── Badges ── */
.badge-pill {
    font-size: .7rem; font-weight: 700; padding: .25rem .65rem;
    border-radius: 100px; display: inline-flex; align-items: center; gap: 0.3rem;
}
.badge-scheduled  { background: var(--green-bg); color: var(--green); }
.badge-unscheduled{ background: var(--coral-bg); color: var(--coral); }
.badge-count      { background: var(--teal-bg); color: var(--teal); padding: .3rem .7rem; }

/* ── Icon Buttons ── */
.icon-btn {
    width: 30px; height: 30px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid var(--slate-200); background: #fff;
    color: var(--slate-500); cursor: pointer; transition: all .2s;
    text-decoration: none; font-size: .85rem;
}
.icon-btn:hover { border-color: var(--teal); color: var(--teal); background: var(--teal-bg); }
.icon-btn-coral:hover { border-color: var(--coral); color: var(--coral); background: var(--coral-bg); }

/* ── Course Cell ── */
.course-code { font-weight: 800; color: var(--slate-900); font-size: .85rem; }
.course-name { font-size: .75rem; color: var(--slate-500); margin-top: .1rem; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ── Toast ── */
.toast-custom {
    position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
    background: var(--slate-900); color: #fff;
    padding: .85rem 1.25rem; border-radius: var(--radius-sm);
    box-shadow: 0 8px 30px rgba(0,0,0,.2);
    display: flex; align-items: center; gap: .6rem; font-size: .85rem; font-weight: 600;
    transform: translateY(120%); opacity: 0; transition: all .3s cubic-bezier(.34,1.56,.64,1);
    pointer-events: none;
}
.toast-custom.show { transform: translateY(0); opacity: 1; pointer-events: auto; }
.toast-custom.toast-error { background: #dc2626; }

/* ── Modals ── */
.modal-content {
    border: none; border-radius: var(--radius-lg);
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
}
.modal-header {
    border-bottom: 1px solid var(--slate-100);
    padding: 1.25rem 1.5rem;
    background: var(--slate-50); border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.modal-title { font-weight: 800; color: var(--slate-900); font-size: 1rem; display: flex; align-items: center; gap: .5rem; }
.modal-body { padding: 1.5rem; }
.modal-footer { border-top: 1px solid var(--slate-100); padding: 1rem 1.5rem; background: var(--slate-50); border-radius: 0 0 var(--radius-lg) var(--radius-lg); }
.modal-label { font-size: .75rem; font-weight: 700; color: var(--slate-700); text-transform: uppercase; letter-spacing: .04em; margin-bottom: .35rem; display: block; }
.modal-input {
    width: 100%; padding: .65rem .85rem; font-size: .875rem;
    border: 1px solid var(--slate-200); border-radius: var(--radius-sm);
    color: var(--slate-900); background: var(--slate-50); outline: none;
    transition: all .2s; box-sizing: border-box;
}
.modal-input:focus { border-color: var(--teal); background: #fff; box-shadow: 0 0 0 3px var(--teal-bg); }

/* ── Empty State ── */
.empty-state { text-align: center; padding: 4rem 2rem; }
.empty-icon { font-size: 2.8rem; color: var(--slate-300); margin-bottom: 1rem; }
.empty-title { font-size: 1.15rem; font-weight: 800; color: var(--slate-700); margin-bottom: .4rem; }
.empty-text { color: var(--slate-500); font-size: .875rem; }

/* ── Alert ── */
.alert-success-custom {
    background: var(--green-bg); color: var(--green);
    padding: .9rem 1.1rem; border-radius: var(--radius-sm);
    border: 1px solid rgba(16,185,129,.2); font-size: .85rem;
    font-weight: 600; display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1.25rem;
}

/* ── Pagination ── */
.pagination-wrap { padding: 1.1rem 1.5rem; border-top: 1px solid var(--slate-100); display: flex; justify-content: center; }

/* ── Select2 z-index fix ── */
.select2-container--open { z-index: 9999999 !important; }
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav stagger-1">
        <a href="{{ route('admin.exams.index') }}"><i class="bi bi-calendar3"></i> Exam Timetables</a>
        <span class="sep">/</span>
        <span class="current">{{ Str::limit($examSchedule->name, 40) }}</span>
    </nav>

    {{-- Page Header --}}
    <div class="page-header stagger-1">
        <div class="page-header-left">
            <a href="{{ route('admin.exams.index') }}" class="btn-outline-soft" style="padding:.5rem .9rem; flex-shrink:0;" title="Back to Exam Timetables">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="header-icon"><i class="bi bi-journal-check"></i></div>
            <div>
                <h1 class="header-title">{{ $examSchedule->name }}</h1>
                <p class="header-subtitle">
                    <i class="bi bi-calendar3"></i>
                    {{ $examSchedule->start_date->format('M d, Y') }} &ndash; {{ $examSchedule->end_date->format('M d, Y') }}
                    &nbsp;·&nbsp;
                    <i class="bi bi-mortarboard"></i>
                    {{ $examSchedule->academicSession->name }}
                </p>
            </div>
        </div>
        <div class="header-actions">
            <form action="{{ route('admin.exams.rollover', $examSchedule) }}" method="POST"
                  class="confirm-form"
                  data-confirm-title="Rollover from Timetable"
                  data-confirm-msg="This will import all active course units from &ldquo;{{ addslashes($examSchedule->academicSession->name) }}&rdquo;. Existing exams will not be duplicated. Continue?"
                  data-confirm-icon="bi-arrow-repeat"
                  data-confirm-color="#037b90"
                  data-confirm-btn="Yes, Rollover"
                  style="margin:0;">
                @csrf
                <button type="button" class="btn-outline-soft btn-confirm-trigger">
                    <i class="bi bi-arrow-repeat"></i> Rollover
                </button>
            </form>
            <button class="btn-outline-soft" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload"></i> Import CSV
            </button>
            <a href="{{ route('admin.exams.export-unscheduled', $examSchedule) }}" class="btn-outline-soft">
                <i class="bi bi-download"></i> Export
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    @php
        $totalExams      = $exams->total();
        $scheduledCount  = $exams->getCollection()->filter(fn($e) => $e->exam_date)->count();
    @endphp
    <div class="stats-row stagger-2">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--teal-bg); color: var(--teal);">
                <i class="bi bi-list-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalExams }}</div>
                <div class="stat-label">Total Exams</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--green-bg); color: var(--green);">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $scheduledCount }}</div>
                <div class="stat-label">Scheduled</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--coral-bg); color: var(--coral);">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalExams - $scheduledCount }}</div>
                <div class="stat-label">Unscheduled</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--purple-bg); color: var(--purple);">
                <i class="bi bi-calendar-range"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $examSchedule->start_date->diffInDays($examSchedule->end_date) + 1 }}</div>
                <div class="stat-label">Days Period</div>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert-success-custom stagger-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Search Filter --}}
    <form action="{{ route('admin.exams.show', $examSchedule) }}" method="GET">
        <div class="filter-card stagger-2">
            <div class="search-input-wrap">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="search-input-field"
                       placeholder="Search by course code or invigilator..."
                       value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-premium" style="height: 38px;">
                <i class="bi bi-funnel"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.exams.show', $examSchedule) }}" class="btn-outline-soft" style="height: 38px;">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            @endif
            <span class="badge-pill badge-count">{{ $exams->total() }} Exams</span>
        </div>
    </form>

    {{-- Main Table --}}
    <div class="table-card stagger-3">
        <div class="table-card-header">
            <h2 class="table-card-title">
                <i class="bi bi-table" style="color: var(--teal);"></i>
                Scheduled Exams
            </h2>
            @if(request('search'))
                <span style="font-size: .8rem; color: var(--slate-500);">
                    Showing results for <strong>"{{ request('search') }}"</strong>
                </span>
            @endif
        </div>

        @forelse($exams as $index => $exam)
            @if($loop->first)
            <div style="overflow-x: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Course</th>
                            <th>Invigilator</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif

                        <tr id="exam-row-{{ $exam->id }}" style="animation: fadeIn .3s ease {{ $index * 0.03 }}s both;">
                            <td>
                                <div class="course-code">{{ $exam->courseUnit->code }}</div>
                                <div class="course-name" title="{{ $exam->courseUnit->name }}">{{ $exam->courseUnit->name }}</div>
                            </td>
                            <td>
                                <span id="invigilator-name-{{ $exam->id }}" style="font-weight: 600;">
                                    {{ $exam->invigilator->name ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span id="date-text-{{ $exam->id }}" style="font-weight: 600; color: {{ $exam->exam_date ? 'var(--slate-900)' : 'var(--coral)' }}">
                                    {{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'Unscheduled' }}
                                </span>
                            </td>
                            <td>
                                <span id="time-text-{{ $exam->id }}" style="font-weight: 600;">
                                    {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '--:--' }}
                                </span>
                            </td>
                            <td>
                                <span id="duration-text-{{ $exam->id }}" style="color: var(--slate-500);">
                                    {{ $exam->duration_minutes }} min
                                </span>
                            </td>
                            <td>
                                @if($exam->exam_date)
                                    <span class="badge-pill badge-scheduled"><i class="bi bi-circle-fill" style="font-size:5px"></i> Scheduled</span>
                                @else
                                    <span class="badge-pill badge-unscheduled"><i class="bi bi-circle-fill" style="font-size:5px"></i> Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div style="display: inline-flex; gap: .4rem; align-items: center;">
                                    {{-- Edit Schedule --}}
                                    <button class="icon-btn btn-edit-schedule"
                                        title="Edit Schedule"
                                        data-exam-id="{{ $exam->id }}"
                                        data-date="{{ $exam->exam_date ? $exam->exam_date->format('Y-m-d') : '' }}"
                                        data-time="{{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('H:i') : '' }}"
                                        data-duration="{{ $exam->duration_minutes }}">
                                        <i class="bi bi-calendar-event"></i>
                                    </button>
                                    {{-- Assign Invigilator --}}
                                    <button class="icon-btn btn-edit-invigilator"
                                        title="Assign Invigilator"
                                        data-bs-toggle="modal"
                                        data-bs-target="#invigilatorModal"
                                        data-exam-id="{{ $exam->id }}"
                                        data-user-id="{{ $exam->user_id }}">
                                        <i class="bi bi-person-gear"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

            @if($loop->last)
                    </tbody>
                </table>
            </div>
            @endif
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
                <h3 class="empty-title">No exams found</h3>
                <p class="empty-text">
                    @if(request('search'))
                        No results for <strong>"{{ request('search') }}"</strong>. Try a different search.
                    @else
                        Use the <strong>Rollover</strong> button above to import courses from
                        <strong>{{ $examSchedule->academicSession->name }}</strong>.
                    @endif
                </p>
            </div>
        @endforelse

        @if($exams->hasPages())
            <div class="pagination-wrap">
                {{ $exams->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>

{{-- ─────────────────────── TOAST ─────────────────────── --}}
<div id="customToast" class="toast-custom">
    <i class="bi bi-check-circle-fill" style="color: var(--green);"></i>
    <span id="toastMsg">Saved successfully.</span>
</div>

{{-- ─────────────────────── SCHEDULE MODAL ─────────────────────── --}}
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">
                    <i class="bi bi-calendar-event" style="color: var(--teal);"></i>
                    Edit Exam Schedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="schedule-exam-id">
                <div class="mb-3">
                    <label class="modal-label">Date</label>
                    <input type="date" class="modal-input" id="schedule-date"
                        min="{{ $examSchedule->start_date->format('Y-m-d') }}"
                        max="{{ $examSchedule->end_date->format('Y-m-d') }}">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="modal-label">Start Time</label>
                        <input type="time" class="modal-input" id="schedule-time">
                    </div>
                    <div>
                        <label class="modal-label">Duration (minutes)</label>
                        <input type="number" class="modal-input" id="schedule-duration" min="30" step="15" placeholder="120">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline-soft" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-premium" id="save-schedule">
                    <i class="bi bi-save"></i> Save Schedule
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────── INVIGILATOR MODAL ─────────────────────── --}}
<div class="modal fade" id="invigilatorModal" tabindex="-1" aria-labelledby="invigilatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invigilatorModalLabel">
                    <i class="bi bi-person-badge" style="color: var(--teal);"></i>
                    Assign Invigilator
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-exam-id">
                <label class="modal-label">Search & Select Instructor</label>
                <select class="select2-invigilator modal-input" id="invigilator-select" style="width: 100%;"></select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline-soft" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-premium" id="save-invigilator">
                    <i class="bi bi-person-check"></i> Assign
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────── IMPORT MODAL ─────────────────────── --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.exams.import', $examSchedule) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bi bi-cloud-upload" style="color: var(--teal);"></i>
                        Import Exam Schedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Step 1 --}}
                    <div class="mb-4">
                        <label class="modal-label">Step 1 — Get Template</label>
                        <div style="display: flex; gap: .6rem; flex-wrap: wrap;">
                            <a href="{{ asset('templates/exam_import_template.csv') }}" class="btn-outline-soft" style="font-size:.8rem;">
                                <i class="bi bi-download"></i> Blank Template
                            </a>
                            <a href="{{ route('admin.exams.export-unscheduled', $examSchedule) }}" class="btn-premium" style="font-size:.8rem;">
                                <i class="bi bi-cloud-download"></i> Unscheduled Exams
                            </a>
                        </div>
                        <p style="font-size:.78rem; color: var(--slate-500); margin: .5rem 0 0;">
                            Download unscheduled exams, fill in dates/times, then upload below.
                        </p>
                    </div>

                    {{-- Import Options --}}
                    <div class="mb-4">
                        <label class="modal-label">Import Behaviour</label>
                        <div style="display: flex; flex-direction: column; gap: .5rem;">
                            <label style="display:flex; align-items:center; gap:.5rem; font-size:.85rem; cursor:pointer;">
                                <input type="radio" name="import_behavior" value="update" checked> Update Existing Exams (Default)
                            </label>
                            <label style="display:flex; align-items:center; gap:.5rem; font-size:.85rem; cursor:pointer;">
                                <input type="radio" name="import_behavior" value="skip"> Skip Existing Exams
                            </label>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="mb-3">
                        <label class="modal-label">Step 2 — Upload CSV</label>
                        <input type="file" name="file" id="file" class="modal-input" accept=".csv" required style="padding: .5rem;">
                    </div>

                    {{-- Preview --}}
                    <div id="preview" class="d-none mt-3">
                        <label class="modal-label">File Preview (First 5 Rows)</label>
                        <div style="border: 1px solid var(--slate-200); border-radius: var(--radius-sm); max-height: 180px; overflow: auto; background: var(--slate-50);">
                            <table style="width:100%; font-size:.78rem; border-collapse: collapse;">
                                <thead id="preview-head" style="position: sticky; top: 0; background: var(--slate-100);"></thead>
                                <tbody id="preview-body"></tbody>
                            </table>
                        </div>
                        <p style="font-size:.73rem; color: var(--slate-500); margin-top: .35rem; font-style: italic;">
                            Expected columns: course_code, programme_code, date, start_time, duration
                        </p>
                    </div>

                    <div style="background: var(--teal-bg); border: 1px solid rgba(3,123,144,.2); border-radius: var(--radius-sm); padding: .7rem 1rem; font-size: .8rem; color: var(--teal); margin-top: .75rem;">
                        <strong>Required columns:</strong> course_code &nbsp;·&nbsp; date (Y-m-d) &nbsp;·&nbsp; start_time (H:i) &nbsp;·&nbsp; duration (minutes)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-soft" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-premium">
                        <i class="bi bi-cloud-upload"></i> Import Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════ CUSTOM CONFIRM MODAL ═══════════════ --}}
<div id="confirmModal" style="
    display: none;
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
">
    <div id="confirmBox" style="
        background: #fff; border-radius: 20px;
        box-shadow: 0 24px 60px rgba(0,0,0,.18);
        width: 100%; max-width: 400px; margin: 1rem;
        overflow: hidden;
        animation: slideUp .25s cubic-bezier(.34,1.56,.64,1);
    ">
        <div id="confirmHeader" style="padding: 1.4rem 1.5rem 1rem; display: flex; align-items: center; gap: .85rem;">
            <div id="confirmIconWrap" style="
                width: 44px; height: 44px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.2rem; flex-shrink: 0;
            ">
                <i id="confirmIcon" class="bi"></i>
            </div>
            <h3 id="confirmTitle" style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0;"></h3>
        </div>
        <div style="padding: 0 1.5rem 1.25rem;">
            <p id="confirmMsg" style="font-size: .875rem; color: #64748b; margin: 0; line-height: 1.6;"></p>
        </div>
        <div style="
            padding: 1rem 1.5rem; background: #f8fafc;
            display: flex; justify-content: flex-end; gap: .65rem;
            border-top: 1px solid #f1f5f9;
        ">
            <button id="confirmCancel" style="
                padding: .6rem 1.2rem; border-radius: 10px;
                border: 1.5px solid #e2e8f0; background: #fff;
                color: #334155; font-size: .85rem; font-weight: 600; cursor: pointer;
            ">Cancel</button>
            <button id="confirmOk" style="
                padding: .6rem 1.4rem; border-radius: 10px;
                border: none; color: #fff;
                font-size: .85rem; font-weight: 700; cursor: pointer;
            ">Confirm</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    /* ── Toast ── */
    function showToast(msg, isError = false) {
        const el = document.getElementById('customToast');
        const msgEl = document.getElementById('toastMsg');
        msgEl.textContent = msg || 'Saved successfully.';
        el.classList.toggle('toast-error', isError);
        el.classList.add('show');
        setTimeout(() => el.classList.remove('show'), 3000);
    }

    /* ── Schedule Modal ── */
    const scheduleModalEl = document.getElementById('scheduleModal');
    const scheduleModal   = scheduleModalEl ? new bootstrap.Modal(scheduleModalEl) : null;

    document.querySelectorAll('.btn-edit-schedule').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('schedule-exam-id').value  = this.dataset.examId;
            document.getElementById('schedule-date').value     = this.dataset.date;
            document.getElementById('schedule-time').value     = this.dataset.time;
            document.getElementById('schedule-duration').value = this.dataset.duration;
            scheduleModal.show();
        });
    });

    document.getElementById('save-schedule')?.addEventListener('click', function () {
        const examId   = document.getElementById('schedule-exam-id').value;
        const date     = document.getElementById('schedule-date').value;
        const time     = document.getElementById('schedule-time').value;
        const duration = document.getElementById('schedule-duration').value;

        updateExam(examId, { exam_date: date, start_time: time, duration_minutes: duration })
            .then(res => {
                if (res.success) {
                    const dateEl = document.getElementById(`date-text-${examId}`);
                    const timeEl = document.getElementById(`time-text-${examId}`);
                    const durEl  = document.getElementById(`duration-text-${examId}`);
                    if (dateEl) {
                        dateEl.textContent = date ? new Date(date + 'T00:00:00').toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : 'Unscheduled';
                        dateEl.style.color = date ? 'var(--slate-900)' : 'var(--coral)';
                    }
                    if (timeEl) timeEl.textContent = time || '--:--';
                    if (durEl)  durEl.textContent  = duration + ' min';

                    const editBtn = document.querySelector(`.btn-edit-schedule[data-exam-id="${examId}"]`);
                    if (editBtn) { editBtn.dataset.date = date; editBtn.dataset.time = time; editBtn.dataset.duration = duration; }

                    scheduleModal.hide();
                    showToast('Schedule updated successfully.');
                } else {
                    showToast('Failed to update schedule.', true);
                }
            })
            .catch(() => showToast('Network error. Please try again.', true));
    });

    /* ── Invigilator Modal (Select2) ── */
    const invigilatorModalEl = document.getElementById('invigilatorModal');
    if (invigilatorModalEl) {
        invigilatorModalEl.addEventListener('show.bs.modal', function (event) {
            const btn    = event.relatedTarget;
            const examId = btn?.dataset.examId;
            const userId = btn?.dataset.userId;

            document.getElementById('edit-exam-id').value = examId;

            const select = $('#invigilator-select');
            if (select.hasClass('select2-hidden-accessible')) select.select2('destroy');

            select.select2({
                dropdownParent: $('#invigilatorModal'),
                width: '100%',
                placeholder: 'Search instructor...',
                allowClear: true,
                ajax: {
                    url: "{{ route('search.instructors') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                        results: $.map(data, i => ({ id: i.id, text: i.name }))
                    }),
                    cache: true
                }
            });

            select.val(null).trigger('change');
            const nameSpan = document.getElementById(`invigilator-name-${examId}`);
            const name = nameSpan?.innerText.trim();
            if (userId && name && name !== '—') {
                const opt = new Option(name, userId, true, true);
                select.append(opt).trigger('change');
            }
        });

        invigilatorModalEl.addEventListener('hidden.bs.modal', function () {
            const select = $('#invigilator-select');
            if (select.hasClass('select2-hidden-accessible')) select.select2('destroy');
        });
    }

    document.getElementById('save-invigilator')?.addEventListener('click', function () {
        const examId = document.getElementById('edit-exam-id').value;
        const select = $('#invigilator-select');
        const data   = select.select2('data');

        const userId   = data?.length ? data[0].id : '';
        const userName = data?.length ? data[0].text : '—';

        updateExam(examId, { user_id: userId })
            .then(res => {
                if (res.success) {
                    const nameSpan = document.getElementById(`invigilator-name-${examId}`);
                    if (nameSpan) nameSpan.textContent = userId ? userName : '—';

                    const editBtn = document.querySelector(`.btn-edit-invigilator[data-exam-id="${examId}"]`);
                    if (editBtn) editBtn.dataset.userId = userId;

                    bootstrap.Modal.getInstance(document.getElementById('invigilatorModal')).hide();
                    showToast('Invigilator assigned successfully.');
                } else {
                    showToast('Failed to assign invigilator.', true);
                }
            })
            .catch(() => showToast('Network error. Please try again.', true));
    });

    /* ── Update Exam Helper ── */
    function updateExam(examId, payload) {
        return fetch(`/admin/exams/update-slot/${examId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        }).then(r => r.json());
    }

    /* ── CSV Preview ── */
    const fileInput = document.getElementById('file');
    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');
            if (!file) { preview.classList.add('d-none'); return; }

            const reader = new FileReader();
            reader.onload = function (e) {
                const rows = e.target.result.split('\n').filter(r => r.trim());
                if (rows.length < 1) return;

                const headers = rows[0].split(',').map(h => h.trim().replace(/^"|"$/g, ''));
                document.getElementById('preview-head').innerHTML =
                    '<tr>' + headers.map(h => `<th style="padding:.4rem .6rem; white-space:nowrap;">${h}</th>`).join('') + '</tr>';

                const rowCount = Math.min(5, rows.length - 1);
                let body = '';
                for (let i = 1; i <= rowCount; i++) {
                    const cells = rows[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
                    while (cells.length < headers.length) cells.push('');
                    body += '<tr>' + cells.map(c => `<td style="padding:.4rem .6rem;">${c}</td>`).join('') + '</tr>';
                }
                document.getElementById('preview-body').innerHTML = body;
                preview.classList.remove('d-none');
            };
            reader.readAsText(file);
        });
    }

    /* ══════════════════════════════════════════
       Custom Confirmation Modal System
       ══════════════════════════════════════════ */
    const confirmModal   = document.getElementById('confirmModal');
    const confirmTitle   = document.getElementById('confirmTitle');
    const confirmMsg     = document.getElementById('confirmMsg');
    const confirmIcon    = document.getElementById('confirmIcon');
    const confirmIconWrap= document.getElementById('confirmIconWrap');
    const confirmOkBtn   = document.getElementById('confirmOk');
    const confirmCancel  = document.getElementById('confirmCancel');

    let pendingForm = null;

    function showConfirm({ title, msg, icon, color, btnLabel }) {
        confirmTitle.textContent = title    || 'Are you sure?';
        confirmMsg.innerHTML     = msg      || 'This action cannot be undone.';
        confirmIcon.className    = `bi ${icon || 'bi-exclamation-triangle-fill'}`;
        const safeColor          = color    || '#ef4444';
        confirmIconWrap.style.background = safeColor + '18';
        confirmIconWrap.style.color      = safeColor;
        confirmOkBtn.style.background    = safeColor;
        confirmOkBtn.textContent = btnLabel || 'Confirm';
        confirmModal.style.display = 'flex';
    }

    function closeConfirm() {
        confirmModal.style.display = 'none';
        pendingForm = null;
    }

    /* Intercept every .confirm-form trigger button */
    document.querySelectorAll('.confirm-form').forEach(form => {
        const trigger = form.querySelector('.btn-confirm-trigger');
        if (!trigger) return;
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            pendingForm = form;
            showConfirm({
                title   : form.dataset.confirmTitle,
                msg     : form.dataset.confirmMsg,
                icon    : form.dataset.confirmIcon,
                color   : form.dataset.confirmColor,
                btnLabel: form.dataset.confirmBtn,
            });
        });
    });

    confirmOkBtn?.addEventListener('click', function () {
        if (pendingForm) { closeConfirm(); pendingForm.submit(); }
    });

    confirmCancel?.addEventListener('click', closeConfirm);

    /* Backdrop click closes */
    confirmModal?.addEventListener('click', e => { if (e.target === confirmModal) closeConfirm(); });

    /* ESC key closes */
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeConfirm(); });

    /* Cancel button hover */
    confirmCancel?.addEventListener('mouseenter', () => confirmCancel.style.background = '#f1f5f9');
    confirmCancel?.addEventListener('mouseleave', () => confirmCancel.style.background = '#fff');
});
</script>
@endpush
