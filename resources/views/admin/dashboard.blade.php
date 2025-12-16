@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        @if($isTimetabler)
            <!-- Timetabler Stats -->
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    My School's Programmes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['programmes'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-journal-text fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Instructors in My School</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['instructors'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Admin Stats -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Programmes</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['programmes'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-journal-text fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Course Units</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['courseUnits'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-book fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Instructors</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['instructors'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Active Session Card (Visible to all) -->
        <div class="{{ $isTimetabler ? 'col-xl-12' : 'col-xl-3' }} col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Active Session</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $currentSession ? $currentSession->name : 'None' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar3 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Happening Today Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-{{ $eventType === 'exam' ? 'danger' : 'primary' }}">
                        <i class="bi {{ $eventType === 'exam' ? 'bi-exclamation-circle' : 'bi-calendar-event' }} me-2"></i>
                        Happening Today: {{ $eventType === 'exam' ? 'Exams' : 'Classes' }}
                        <span class="badge bg-light text-dark ms-2">{{ \Carbon\Carbon::today()->format('D, M d, Y') }}</span>
                    </h6>
                    @if($eventType === 'exam')
                        <span class="badge bg-danger">Exam Period Active</span>
                    @endif
                </div>
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
                                            @if($eventType === 'exam')
                                                {{ $event->start_time ? $event->start_time->format('H:i') : 'N/A' }} 
                                                - 
                                                {{ $event->start_time ? $event->start_time->addMinutes($event->duration_minutes)->format('H:i') : 'N/A' }}
                                            @else
                                                {{-- LessonSlot start_time might be string '08:00' or Carbon --}}
                                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} 
                                                - 
                                                {{ \Carbon\Carbon::parse($event->start_time)->addHours($event->duration)->format('H:i') }}
                                            @endif
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

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-lightning-charge-fill me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(!$isTimetabler)
                            <!-- Admin Quick Actions -->
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('admin.programmes.create') }}" class="btn btn-primary w-100 py-3">
                                    <i class="bi bi-plus-circle me-2"></i>Add Programme
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('course_units.create') }}" class="btn btn-success w-100 py-3">
                                    <i class="bi bi-journal-plus me-2"></i>Add Course Unit
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-info text-white w-100 py-3">
                                    <i class="bi bi-person-plus me-2"></i>Add Instructor
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="btn btn-warning w-100 py-3" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                                    <i class="bi bi-upload me-2"></i>Bulk Upload
                                </a>
                            </div>
                        @else
                            <!-- Timetabler Quick Actions -->
                            <div class="col-12 mb-3">
                                <a href="{{ $currentSession ? url('/admin/academic-sessions/' . $currentSession->id) : '#' }}" 
                                   class="btn btn-primary w-100 py-3 {{ !$currentSession ? 'disabled' : '' }}" 
                                   {{ !$currentSession ? 'aria-disabled="true"' : '' }}>
                                    <i class="bi bi-diagram-3 me-2"></i>Course Mapping
                                    @if(!$currentSession)
                                        <span class="badge bg-warning ms-2">No active session</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('admin.timetables.manage') }}" class="btn btn-success w-100 py-3">
                                    <i class="bi bi-calendar-week me-2"></i>Manage Timetable
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Session -->
        @if($currentSession)
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-calendar3 me-2"></i>Current Academic Session
                    </h6>
                </div>
                <div class="card-body">
                    <h4 class="mb-3">{{ $currentSession->name }}</h4>
                    <p class="mb-2">
                        <i class="bi bi-calendar-range me-2"></i>
                        {{ $currentSession->start_date->format('M d, Y') }} - {{ $currentSession->end_date->format('M d, Y') }}
                    </p>
                    <p class="mb-3">
                        <span class="badge bg-{{ $currentSession->status === 'active' ? 'success' : 'secondary' }} p-2">
                            <i class="bi bi-{{ $currentSession->status === 'active' ? 'check-circle' : 'circle' }} me-1"></i>
                            {{ ucfirst($currentSession->status) }}
                        </span>
                    </p>
                    <a href="{{ route('admin.academic-sessions.show', $currentSession) }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right-circle me-1"></i> View Session
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select upload type:</label>
                    <select class="form-select mb-3">
                        <option value="instructors">Instructors</option>
                        <option value="students" disabled>Students</option>
                        <option value="courses" disabled>Course Units</option>
                    </select>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Only Instructors bulk upload is currently available.
                    </div>
                </div>
                <a href="{{ route('instructors.upload') }}" class="btn btn-primary w-100">
                    <i class="bi bi-upload me-2"></i>Proceed to Upload
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25) !important;
    }
    .btn {
        transition: all 0.2s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection
