@extends('layouts.app')

@section('title', 'Today\'s Classes Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Today's Classes</h1>
            <p class="mb-0 text-muted">{{ $date->format('l, F j, Y') }}</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Reports
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm ms-2">
                <i class="bi bi-printer me-1"></i> Print Report
            </button>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Lecturers Teaching Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['lecturers_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-video3 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-event fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4 d-print-none">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Options</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.todays-classes') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="academic_session_id" class="form-label">Academic Session</label>
                    <select name="academic_session_id" id="academic_session_id" class="form-select">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $selectedAcademicSessionId == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by Course Code, Name, Instructor..." 
                           value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($groupedClasses->isNotEmpty())
        @foreach($groupedClasses as $schoolName => $departments)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h5 class="m-0 font-weight-bold">
                        <i class="bi bi-building me-2"></i>{{ $schoolName }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    @foreach($departments as $deptName => $classes)
                        <div class="border-bottom p-3 bg-light">
                            <h6 class="m-0 font-weight-bold text-dark">
                                <i class="bi bi-diagram-3 me-2"></i>{{ $deptName }}
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%">Time</th>
                                        <th style="width: 50%">Course</th>
                                        <th style="width: 25%">Instructor</th>
                                        <th style="width: 10%">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classes as $class)
                                        <tr>
                                            <td class="align-middle" style="white-space: nowrap;">
                                                <i class="bi bi-clock me-1 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                            </td>
                                            <td class="align-middle">
                                                <strong>{{ $class->courseUnit->code ?? 'N/A' }}</strong> - {{ $class->courseUnit->name ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle">
                                                @if($class->instructor)
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                            {{ substr($class->instructor->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <div>{{ $class->instructor->name }}</div>
                                                            <div class="small text-muted">{{ $class->instructor->email }}</div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic">Unassigned</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($class->type === 'Morning')
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-brightness-high me-1"></i> Morning</span>
                                                @else
                                                    <span class="badge bg-primary"><i class="bi bi-moon me-1"></i> Evening</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
            <h4 class="mt-3">No Classes Found</h4>
            <p>There are no classes scheduled for today ({{ $date->format('l') }}) in the selected academic session.</p>
        </div>
    @endif
</div>
@endsection
