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
        justify-content: center;
        font-size: 0.7rem;
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
                        <div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Selected/Current Academic Session -->
        @if($currentSession)
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
                        <div>
                            <h4 class="mb-1">{{ $currentSession->name }}</h4>
                            <p class="text-muted mb-2">You are currently viewing and editing this academic session.</p>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary me-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $currentSession->start_date->format('M d, Y') }} - {{ $currentSession->end_date->format('M d, Y') }}
                                </span>
                                <a href="{{ route('timetable.index', ['academic_session' => $currentSession->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-calendar-week me-1"></i> View Timetable
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        @if($currentSession)
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.academic-sessions.show', $currentSession) }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Current Session</h6>
                                    <small class="text-muted">{{ $currentSession->name }}</small>
                                </div>
                            </a>
                        </div>
                        @endif
                        <div class="col-lg-3 col-md-6">
                                @if(!$isTimetabler)
                                <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-calendar3 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">All Sessions</h6>
                                        <small class="text-muted">Manage academic years</small>
                                    </div>
                                </a>
                                @endif
                                
                                        @if($isTimetabler && $currentSession)
                                <a href="{{ url('/admin/academic-sessions/' . $currentSession->id) }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
                                    <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-diagram-3 text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Course Mapping</h6>
                                        <small class="text-muted">Map courses to programmes</small>
                                    </div>
                                </a>
                                @endif
                            </div>
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
                            <a href="{{ route('instructors.index') }}" class="btn btn-light w-100 p-3 text-start d-flex align-items-center quick-actions">
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
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                <a href="{{ url("/admin/academic-sessions/" . $currentSession->id . "/programmes/" . $programme->id . "/map-course-units") }}" class="list-group-item list-group-item-action py-3 px-4 recent-activity">
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
                            <a href="{{ route('admin.academic-sessions.show', $currentSession) }}" class="btn btn-sm btn-outline-primary w-100">
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
    });
</script>
@endpush

@endsection
