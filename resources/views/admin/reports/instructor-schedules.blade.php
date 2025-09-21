@extends('layouts.app')

@section('title', 'Instructor Schedules Report')

@push('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Instructor Schedules</li>
</ol>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .schedule-table {
        font-size: 0.9rem;
    }
    .schedule-table th {
        background-color: #f8f9fa;
        white-space: nowrap;
        vertical-align: top;
    }
    .schedule-table td {
        vertical-align: top;
    }
    .no-schedules {
        text-align: center;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    .no-schedules i {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .time-slot {
        padding: 0.25rem 0.5rem;
        background-color: #f1f8ff;
        border-radius: 0.25rem;
        font-family: monospace;
        display: inline-block;
        margin-right: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .badge {
        font-weight: 500;
    }
    .select2-container {
        width: 100% !important;
    }
    .export-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Instructor Schedules</h1>
    </div>

    {{-- <div class="card shadow mb-4"> --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Select Instructor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.instructor-schedules') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group mb-3">
                            <label for="academicSessionSelect" class="form-label">Academic Session</label>
                            <select class="form-control select2" name="academic_session_id" id="academicSessionSelect" required>
                                @foreach($academicSessions as $session)
                                    <option value="{{ $session->id }}" {{ $selectedAcademicSessionId == $session->id ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-3">
                            <label for="instructorSelect" class="form-label">Instructor</label>
                            <select class="form-control select2" name="instructor_id" id="instructorSelect" required>
                                <option value="">-- Select Instructor --</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }} ({{ $instructor->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> View Schedule
                        </button>
                    </div>
                </div>
            </form>

            @if($selectedInstructor)
                <div class="instructor-info mb-4">
                    <h4>Instructor Schedule for {{ $selectedInstructor->name }}</h4>
                    <p class="text-muted">
                        Email: {{ $selectedInstructor->email }}<br>
                        Academic Session: {{ $academicSessions->firstWhere('id', $selectedAcademicSessionId)->name ?? 'N/A' }}
                    </p>
                </div>

                @if($schedules->count() > 0)
                    <div class="export-buttons">
                        <a href="{{ route('admin.reports.export', ['format' => 'excel']) }}?instructor_id={{ $selectedInstructor->id }}&academic_session_id={{ $selectedAcademicSessionId }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel
                        </a>
                        <a href="{{ route('admin.reports.export', ['format' => 'pdf']) }}?instructor_id={{ $selectedInstructor->id }}&academic_session_id={{ $selectedAcademicSessionId }}" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Export to PDF
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered schedule-table">
                            <thead>
                                <tr>
                                    <th>Day & Time</th>
                                    <th>Course Unit</th>
                                    <th>Programmes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <strong>{{ $schedule->day }}</strong>
                                            <div class="mt-2">
                                                @foreach($schedule->times as $index => $time)
                                                    <div class="time-slot {{ $index > 0 ? 'mt-1' : '' }}">
                                                        {{ $time }}
                                                        <span class="badge bg-light text-dark">{{ $schedule->session_types[$index] ?? '' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                {{ $schedule->course_unit->code ?? 'N/A' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $schedule->course_unit->name ?? 'N/A' }}
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge bg-info text-dark">{{ $schedule->year_semester ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if(count($schedule->programmes) > 0)
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($schedule->programmes as $programme)
                                                        <li class="mb-1">
                                                            <span class="badge bg-light text-dark">
                                                                {{ $programme->name }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @if($schedules->count() === 0)
                                <tr>
                                    <td colspan="5" class="text-center">No schedules found for this instructor.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="no-schedules">
                        <i class="bi bi-calendar-x fs-1"></i>
                        <h5>No schedules found for this instructor</h5>
                        <p class="mb-0">This instructor doesn't have any scheduled classes yet.</p>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="bi bi-person-lines-fill fs-1 text-muted"></i>
                    <h5>Select an instructor to view their schedule</h5>
                    <p class="text-muted">Use the dropdown above to select an instructor and click "View Schedule"</p>
                </div>
            @endif
        </div>
    {{-- </div> --}}
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: 'Search for an instructor...',
            allowClear: true,
            width: 'resolve'
        });

        // Auto-submit form when instructor is selected (optional)
        // $('#instructorSelect').on('change', function() {
        //     if ($(this).val()) {
        //         $(this).closest('form').submit();
        //     }
        // });
    });
</script>
@endpush
