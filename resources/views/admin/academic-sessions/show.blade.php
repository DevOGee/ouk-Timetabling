@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            @if(isset($viewMode) && $viewMode == 'curriculum')
                <h2>Curriculum Mapping: {{ $academicSession->name }}</h2>
            @elseif(isset($viewMode) && $viewMode == 'allocation')
                <h2>Teaching Allocation: {{ $academicSession->name }}</h2>
            @else
                <h2>Academic Session: {{ $academicSession->name }}</h2>
            @endif
        </div>
        @if(auth()->user()->hasRole('admin'))
        <div class="col-md-6 text-end">
            @if(!isset($viewMode))
                <a href="{{ route('admin.academic-sessions.edit', $academicSession) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
            <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
        @endif
    </div>

    @if(!isset($viewMode))
    <div class="card mb-4 text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Session Details</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Code:</dt>
                        <dd class="col-sm-8 text-white-50">{{ $academicSession->code }}</dd>

                        <dt class="col-sm-4">Duration:</dt>
                        <dd class="col-sm-8 text-white-50">{{ $academicSession->duration }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ 
                                $academicSession->status === 'active' ? 'success' : 
                                ($academicSession->status === 'upcoming' ? 'info' : 
                                ($academicSession->status === 'completed' ? 'secondary' : 'dark'))
                            }}">
                                {{ ucfirst($academicSession->status) }}
                            </span>
                            @if($academicSession->is_current)
                                <span class="badge bg-success ms-1">Current</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Start Date:</dt>
                        <dd class="col-sm-8 text-white-50">{{ $academicSession->start_date->format('F d, Y') }}</dd>

                        <dt class="col-sm-4">End Date:</dt>
                        <dd class="col-sm-8 text-white-50">{{ $academicSession->end_date->format('F d, Y') }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Description</h5>
                    <p class="text-white-50">{{ $academicSession->description ?? 'No description provided.' }}</p>

                    @if(!auth()->user()->hasRole('timetabler'))
                    <div class="mt-4">
                        <h5>Quick Actions</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            @if(!$hasProgrammes && $otherSessions->isNotEmpty())
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#copyFromPreviousSessionModal">
                                    <i class="bi bi-files"></i> Use Previous Session
                                </button>
                            @endif
                            
                            @if(!$academicSession->is_current)
                                <form action="{{ route('admin.academic-sessions.set-current', $academicSession) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light">
                                            <i class="bi bi-check-circle"></i> Set as Current
                                        </button>
                                    </form>
                                @endif

                                @if($academicSession->status !== 'archived')
                                    <form action="{{ route('admin.academic-sessions.archive', $academicSession) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-light" 
                                                onclick="return confirm('Are you sure you want to archive this session?')">
                                            <i class="bi bi-archive"></i> Archive
                                        </button>
                                    </form>
                                @endif

                                @if(!$academicSession->timetables()->exists())
                                    <form action="{{ route('admin.academic-sessions.destroy', $academicSession) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this session?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Navigation Tabs -->
    @if(!isset($viewMode))
    <ul class="nav nav-tabs mb-4" id="sessionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="programmes-tab" data-bs-toggle="tab" data-bs-target="#programmes" type="button" role="tab" aria-controls="programmes" aria-selected="true">
                Programmes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="timetables-tab" data-bs-toggle="tab" data-bs-target="#timetables" type="button" role="tab" aria-controls="timetables" aria-selected="false">
                Timetables
            </button>
        </li>
    </ul>
    @endif

    <!-- Tab Content -->
    <div class="tab-content" id="sessionTabsContent">
        <!-- Programmes Tab -->
        @if(!isset($viewMode) || $viewMode == 'curriculum')
        <div class="tab-pane fade show active" id="programmes" role="tabpanel" aria-labelledby="programmes-tab">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Programmes</h5>
                    @if(!auth()->user()->hasRole('timetabler'))
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center">
                            @if(isset($schools) && $schools->count() > 0)
                                <div class="me-3">
                                    <label for="schoolFilter" class="form-label mb-0 me-2">Filter by School:</label>
                                    <select id="schoolFilter" class="form-select_school-filter form-select form-select-sm" style="width: auto; display: inline-block;">
                                        <option value="all" {{ !request()->has('school_id') ? 'selected' : '' }}>All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.academic-sessions.select-programmes', $academicSession) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i> Add Programmes
                                </a>
                                <a href="{{ route('admin.programmes.create') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus-circle"></i> New Programme
                                </a>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Department and Search Filters -->
                    @if(!auth()->user()->hasRole('timetabler'))
                    <form method="GET" action="{{ url()->current() }}" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-1">Department</label>
                                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all">All Departments</option>
                                    @foreach($departments->groupBy('school.name') as $schoolName => $schoolDepartments)
                                        <optgroup label="{{ $schoolName }}">
                                            @foreach($schoolDepartments as $department)
                                                <option value="{{ $department->id }}" {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label mb-1">Search</label>
                                <input type="search" name="search" class="form-control form-control-sm" 
                                       placeholder="Programme name or code..." 
                                       value="{{ $searchTerm ?? '' }}">
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                @if($selectedDepartment || $searchTerm)
                                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                    @endif

                    <!-- Programs Table Container -->
                    <div id="programmes-container">
                        @include('admin.academic-sessions.partials.programmes-table', ['programmes' => $programmes, 'academicSession' => $academicSession])
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading programmes...</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Timetables Tab -->
        @if(!isset($viewMode) || $viewMode == 'allocation')
        <div class="tab-pane fade {{ (isset($viewMode) && $viewMode == 'allocation') ? 'show active' : '' }}" id="timetables" role="tabpanel" aria-labelledby="timetables-tab">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Programme Timetables</h5>
                    <div class="d-flex align-items-center">
                        @if(!auth()->user()->hasRole('timetabler'))
                            @if(isset($schools) && $schools->count() > 0)
                                <div class="me-3">
                                    <label for="timetableSchoolFilter" class="form-label mb-0 me-2">Filter by School:</label>
                                    <select id="timetableSchoolFilter" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                        <option value="all">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            
                            @if($programmesForTimetable->isNotEmpty())
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="addTimetableDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-plus"></i> Add Timetable
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="addTimetableDropdown">
                                        @foreach($programmesForTimetable as $programme)
                                            <li>
                                                <a class="dropdown-item" href="#" data-programme-id="{{ $programme->id }}">
                                                    {{ $programme->name }} ({{ $programme->programme_code }})
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Timetables Table Container -->
                    <div id="timetables-container">
                        @include('admin.academic-sessions.partials.timetables-table', [
                            'programmes' => $programmes,
                            'academicSession' => $academicSession
                        ])
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div id="timetableLoadingIndicator" class="text-center d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading timetables...</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<!-- Copy From Previous Session Modal -->
<div class="modal fade" id="copyFromPreviousSessionModal" tabindex="-1" aria-labelledby="copyFromPreviousSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="copyFromPreviousSessionModalLabel">Copy From Previous Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="copyFromPreviousSessionForm" action="{{ route('admin.academic-sessions.copy-mappings', $academicSession) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="source_session_id" class="form-label">Select Source Session</label>
                        <select class="form-select" id="source_session_id" name="source_session_id" required>
                            <option value="">-- Select a session --</option>
                            @foreach($otherSessions as $session)
                                <option value="{{ $session->id }}">{{ $session->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">This will copy all programme mappings from the selected session to the current session.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Copy Mappings</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

