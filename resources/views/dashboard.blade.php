@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25) !important;
    }
    .stat-icon {
        font-size: 2rem;
        opacity: 0.8;
    }
    .card-header {
        border-bottom: none;
        background: transparent;
        padding: 1.25rem 1.5rem 0.5rem;
    }
    .quick-actions .btn {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .quick-actions .btn i {
        margin-right: 8px;
    }
    .recent-activity {
        transition: all 0.3s ease;
    }
    .recent-activity:hover {
        transform: translateX(5px);
    }
    .badge-indicator {
        position: absolute;
        top: -5px;
        right: -5px;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
    }
    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .stat-label {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .stat-change {
        font-size: 0.75rem;
    }
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1) !important;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .list-group-item {
        border: none;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fc;
        border-left-color: #4e73df;
    }
    .heatmap-container {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    .heatmap-header {
        padding: 1rem 1.25rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    .heatmap-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    .heatmap-table {
        width: 100%;
        border-collapse: collapse;
    }
    .heatmap-table th, 
    .heatmap-table td {
        padding: 0.75rem 1rem;
        text-align: center;
        border: 1px solid #e9ecef;
    }
    .heatmap-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .heatmap-programme {
        text-align: left !important;
        white-space: nowrap;
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .heatmap-cell {
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .heatmap-cell:hover {
        transform: scale(1.05);
        z-index: 1;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .heatmap-count {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .heatmap-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-5px);
        background: #2c3e50;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s, transform 0.2s;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .heatmap-cell:hover .heatmap-tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(-10px);
    }
    .heatmap-legend {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
        font-size: 0.8rem;
        color: #6c757d;
    }
    .heatmap-legend-item {
        display: flex;
        align-items: center;
        margin: 0 0.5rem;
    }
    .heatmap-legend-color {
        width: 1rem;
        height: 1rem;
        border-radius: 2px;
        margin-right: 0.25rem;
        border: 1px solid rgba(0,0,0,0.1);
    }
    .transition-icon {
        transition: transform 0.3s ease;
    }
    [aria-expanded="true"] .transition-icon {
        transform: rotate(0deg);
    }
    [aria-expanded="false"] .transition-icon {
        transform: rotate(-90deg);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard
            </h1>
            <p class="mb-0 text-muted">Welcome back! Here's what's happening with your timetable system.</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3 rounded-pill">
                <i class="bi bi-calendar3 me-1"></i> {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>



    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        @if(!$isTimetabler)
        <!-- Academic Sessions Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 small fw-bold">Academic Sessions</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ $academicSessions->count() }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-calendar-week text-primary stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(!$isTimetabler)
        <!-- Programmes Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 small fw-bold">Programmes</h6>
                            <h2 class="mb-0 fw-bold text-success">{{ $programmes->count() }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-journal-text text-success stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Instructors Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card bg-white h-100">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 small fw-bold">Instructors</h6>
                            <h2 class="mb-0 fw-bold text-info">{{ $instructors->count() }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-people text-info stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Units Card -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-muted mb-2 small fw-bold">Course Units</h6>
                            <h2 class="mb-0 fw-bold text-warning">{{ $courseUnits->count() }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-book text-warning stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Session Status -->
    <div class="row g-4 mb-4">
        <!-- Active Academic Session -->
        @if($activeSession || $selectedSession)
        @if($activeSession)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 border-start border-success border-4 py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-broadcast me-2"></i>Active Academic Session
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $activeSession->name }}</h4>
                            <p class="text-muted mb-2">This is the live timetable session currently visible to students and staff.</p>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="badge bg-success bg-opacity-10 text-success me-2">
                                    <i class="bi bi-calendar3 me-1"></i> 
                                    {{ $activeSession->start_date->format('M d, Y') }} - {{ $activeSession->end_date->format('M d, Y') }}
                                </span>
                                <a href="{{ route('timetable.index', ['academic_session' => $activeSession->id]) }}" class="btn btn-sm btn-success mt-1">
                                    <i class="bi bi-calendar-week me-1"></i> View Timetable
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.academic-sessions.edit', $activeSession) }}" class="btn btn-sm btn-outline-secondary mt-1">
                                    <i class="bi bi-pencil me-1"></i> Manage Session
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Selected Academic Session -->
        @if($selectedSession && !$isInstructor)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 border-start border-primary border-4 py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-pencil-square me-2"></i>Selected Academic Session
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-4">
                            <i class="bi bi-pencil-square text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $selectedSession->name }}</h4>
                            <p class="text-muted mb-2">You are currently viewing and editing this academic session.</p>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary me-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $selectedSession->start_date->format('M d, Y') }} - {{ $selectedSession->end_date->format('M d, Y') }}
                                </span>
                                <a href="{{ route('timetable.index', ['academic_session' => $selectedSession->id]) }}" class="btn btn-sm btn-primary mt-1">
                                    <i class="bi bi-calendar-week me-1"></i> View Timetable
                                </a>
                                @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.academic-sessions.edit', $selectedSession) }}" class="btn btn-sm btn-outline-secondary mt-1">
                                    <i class="bi bi-pencil me-1"></i> Manage Session
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                        Quick Actions
                    </h5>
                    <p class="text-muted mb-0 small">
                        Quickly access frequently used features
                    </p>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @if($isInstructor)
                            <!-- Instructor Quick Actions -->
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ route('instructor.timetable') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-calendar-week text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">My Timetable</h6>
                                        <small class="text-muted">View your class schedule</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ route('instructor.course-units') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-book text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">My Course Units</h6>
                                        <small class="text-muted">View your assigned courses</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ route('profile.show') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-person text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">My Profile</h6>
                                        <small class="text-muted">Update your information</small>
                                    </div>
                                </a>
                            </div>
                        @else
                            <!-- Admin/Timetabler Quick Actions -->
                            @if($selectedSession)
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.academic-sessions.show', $selectedSession) }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-calendar-check text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Selected Session</h6>
                                        <small class="text-muted">{{ $selectedSession->name }}</small>
                                    </div>
                                </a>
                            </div>
                            @endif
                            @if(!$isTimetabler)
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-calendar3 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">All Sessions</h6>
                                        <small class="text-muted">Manage academic years</small>
                                    </div>
                                </a>
                            </div>
                            @endif
                            @if($isTimetabler && $selectedSession)
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.curriculum-mapping') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-diagram-3 text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Curriculum Mapping</h6>
                                        <small class="text-muted">Map courses to programmes</small>
                                    </div>
                                </a>
                            </div>
                            @endif
                        @if(!$isTimetabler)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.programmes.index') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-journal-bookmark text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Programmes</h6>
                                    <small class="text-muted">Manage degree programmes</small>
                                </div>
                            </a>
                        </div>
                        @endif
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.users.index', ['role' => 'instructor']) }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-people text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Instructors</h6>
                                    <small class="text-muted">Manage teaching staff</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('timetable.index') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-calendar-check text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Timetable</h6>
                                    <small class="text-muted">View schedules</small>
                                </div>
                            </a>
                        </div>
                        @if($isAdmin)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                <div class="bg-purple bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-people-fill text-purple"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Manage Users</h6>
                                    <small class="text-muted">Manage system users and roles</small>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Happening Today Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" 
                     role="button" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#happeningTodayCollapse" 
                     aria-expanded="true" 
                     aria-controls="happeningTodayCollapse">
                    <h6 class="m-0 font-weight-bold text-{{ $eventType === 'exam' ? 'danger' : 'primary' }}">
                        <i class="bi {{ $eventType === 'exam' ? 'bi-exclamation-circle' : 'bi-calendar-event' }} me-2"></i>
                        Happening Today: {{ $eventType === 'exam' ? 'Exams' : 'Classes' }}
                        <span class="badge bg-light text-dark ms-2">{{ \Carbon\Carbon::today()->format('D, M d, Y') }}</span>
                    </h6>
                    <div class="d-flex align-items-center">
                        @if($eventType === 'exam')
                            <span class="badge bg-danger me-2">Exam Period Active</span>
                        @endif
                        <i class="bi bi-chevron-down transition-icon"></i>
                    </div>
                </div>
                <div class="collapse show" id="happeningTodayCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Course</th>
                                        <th>{{ $eventType === 'exam' ? 'Invigilator' : 'Programme' }}</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($todaysEvents as $event)
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                {{-- Controller ensures start_time is Carbon --}}
                                                {{ $event->start_time ? $event->start_time->format('H:i') : 'N/A' }} 
                                                - 
                                                {{ $event->start_time ? $event->start_time->copy()->addMinutes($eventType === 'exam' ? $event->duration_minutes : ($event->duration ?? 0))->format('H:i') : 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $event->courseUnit->code ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $event->courseUnit->name ?? '' }}</small>
                                            </td>
                                            <td>
                                                @if($eventType === 'exam')
                                                    @if($event->invigilator)
                                                        <div class="d-flex align-items-center">
                                                            <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                                                <span class="small fw-bold text-gray-600">{{ substr($event->invigilator->name, 0, 1) }}</span>
                                                            </div>
                                                            {{ $event->invigilator->name }}
                                                        </div>
                                                    @else
                                                        <span class="text-muted fst-italic">Unassigned</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-info text-dark">{{ $event->programme->code ?? 'N/A' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($eventType === 'exam')
                                                    {{-- Room model does not exist, use room_id directly --}}
                                                    {{ $event->room_id ? 'Room ' . $event->room_id : 'TBA' }}
                                                @else
                                                    {{-- LessonSlots don't typically have room directly in this simple schema, usually in Timetable structure. 
                                                         If no room, show 'Classroom' or similar. --}}
                                                    <span class="text-muted">Classroom</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-calendar-x me-2"></i>
                                                    No {{ $eventType === 'exam' ? 'exams' : 'classes' }} scheduled for today.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Heatmap Section for All Users -->
    @if(($isAdmin || $isTimetabler) && $selectedSession && !empty($heatmapData['programmes']))
        @include('partials.heatmap')
    @endif

    @if($unmappedProgrammes->isNotEmpty())
    <!-- Programmes Section -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 border-bottom border-{{ $isTimetabler ? 'primary' : 'danger' }} border-3 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-{{ $isTimetabler ? 'primary' : 'danger' }}">
                            <i class="bi {{ $isTimetabler ? 'bi-journal-bookmark' : 'bi-exclamation-triangle-fill' }} me-2"></i>
                            {{ $isTimetabler ? 'Programmes in Your School' : 'Programmes Without Courses' }}
                        </h5>
                        <span class="badge bg-{{ $isTimetabler ? 'primary' : 'danger' }}-subtle text-{{ $isTimetabler ? 'primary' : 'danger' }} px-2 py-1">
                            {{ $unmappedProgrammes->count() }} {{ $unmappedProgrammes->count() === 1 ? 'programme' : 'programmes' }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($unmappedProgrammes->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($unmappedProgrammes as $programme)
                                <a href="{{ url("/admin/academic-sessions/" . $selectedSession->id . "/programmes/" . $programme->id . "/map-course-units") }}" class="list-group-item list-group-item-action py-3 px-4 recent-activity">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-journal-bookmark text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 fw-bold">{{ $programme->name }}</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark me-2">{{ $programme->programme_code }}</span>
                                                @if($programme->school)
                                                    <small class="text-muted">{{ $programme->school->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="card-footer bg-white border-0 py-3">
                            <a href="{{ route('admin.academic-sessions.show', $selectedSession) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-arrow-right me-1"></i> Manage Academic Session
                            </a>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                            <p class="mt-3 mb-0 text-muted">All programmes have courses mapped!</p>
                            <a href="{{ route('admin.programmes.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                                <i class="bi bi-journal-bookmark me-1"></i> View All Programmes
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-journal-bookmark me-2"></i>Recent Programmes
                        </h5>
                        <a href="{{ route('admin.programmes.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentProgrammes->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($recentProgrammes as $programme)
                                <a href="{{ route('admin.programmes.show', $programme) }}" class="list-group-item list-group-item-action py-3 px-4 recent-activity">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-journal-bookmark text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0 fw-bold">{{ $programme->name }}</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark me-2">{{ $programme->programme_code }}</span>
                                                @if($programme->school)
                                                    <small class="text-muted">{{ $programme->school->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 2.5rem;"></i>
                            <p class="mt-3 mb-0 text-muted">No programmes found</p>
                            <a href="{{ route('admin.programmes.create') }}" class="btn btn-sm btn-primary mt-3">
                                <i class="bi bi-plus-lg me-1"></i> Add Programme
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif


</div>

@push('scripts')
<script>
    // Add animation on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                // Force reflow
                void card.offsetWidth;
                
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
        
        // Add hover effect to quick action buttons
        const quickActions = document.querySelectorAll('.quick-actions .btn');
        quickActions.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Initialize tooltips for heatmap cells if they exist
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
    
    // Function to get color for heatmap cell based on count and max count
    function getHeatmapColor(count, maxCount) {
        if (count === 0) return '#f8f9fa';
        
        // Define color scale from light to dark blue
        const colors = [
            '#e6f2ff', // lightest
            '#b3d7ff',
            '#80bdff',
            '#4da3ff',
            '#1a88ff',
            '#0066e0', // darkest
        ];
        
        // Calculate index based on count relative to max count
        const index = Math.min(
            Math.floor((count / maxCount) * (colors.length - 1)),
            colors.length - 1
        );
        
        return colors[index];
    }
    
    // Initialize heatmap if data is available
    @if(isset($heatmapData) && $isAdmin && !empty($heatmapData['programmes']))
    document.addEventListener('DOMContentLoaded', function() {
        const heatmapData = @json($heatmapData);
        
        // Set cell colors based on count
        document.querySelectorAll('.heatmap-cell').forEach(cell => {
            const count = parseInt(cell.getAttribute('data-count') || '0');
            const maxCount = heatmapData.maxCount;
            const color = getHeatmapColor(count, maxCount);
            
            cell.style.backgroundColor = color;
            
            // Add tooltip content
            const programmeId = cell.getAttribute('data-programme-id');
            const level = cell.getAttribute('data-level');
            
            if (programmeId && level) {
                const programme = heatmapData.programmes.find(p => p.id == programmeId);
                if (programme && programme.levels[level]) {
                    const mappings = programme.levels[level].mappings;
                    let tooltipHtml = `<div class="text-start">
                        <strong>${programme.name}</strong><br>
                        <small>Level: ${level}</small><br>
                        <small>Courses: ${mappings.length}</small>`;
                        
                    if (mappings.length > 0) {
                        tooltipHtml += '<div class="mt-2"><strong>Courses:</strong><ul class="mb-0 ps-3">';
                        mappings.slice(0, 5).forEach(mapping => {
                            tooltipHtml += `<li>${mapping.course_code} - ${mapping.course_name}</li>`;
                        });
                        if (mappings.length > 5) {
                            tooltipHtml += `<li>+${mappings.length - 5} more</li>`;
                        }
                        tooltipHtml += '</ul></div>';
                    }
                    tooltipHtml += '</div>';
                    
                    cell.setAttribute('data-bs-toggle', 'tooltip');
                    cell.setAttribute('data-bs-html', 'true');
                    cell.setAttribute('title', tooltipHtml);
                    
                    // Initialize tooltip
                    if (typeof bootstrap !== 'undefined') {
                        new bootstrap.Tooltip(cell);
                    }
                }
            }
        });
    });
    @endif
</script>
@endpush

@endsection
