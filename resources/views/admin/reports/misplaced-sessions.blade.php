@extends('layouts.app')

@section('title', 'Misplaced Sessions Report')

@section('content')
<div class="row items-push">
    <div class="col-md-6">
        <h1 class="block-title">Misplaced Sessions Report</h1>
        <small class="text-muted">
            Identify course units taught by the same instructor that are not scheduled at the same time across different programmes.
        </small>
    </div>
</div>

<div class="content">
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter Report</h3>
        </div>
        <div class="block-content block-content-full">
            <form action="{{ route('admin.reports.misplaced-sessions') }}" method="GET" class="row g-3 items-center">
                <div class="col-md-4">
                    <label class="form-label">Academic Session</label>
                    <select name="academic_session_id" class="form-select">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $session->id == $selectedAcademicSessionId ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">School</label>
                    <select name="school_id" class="form-select">
                        <option value="all">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
                    @if(request()->has('academic_session_id'))
                        <a href="{{ route('admin.reports.misplaced-sessions') }}" class="btn btn-secondary mt-4">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($misplacedGroups))
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Misplaced Sessions ({{ count($misplacedGroups) }})</h3>
            </div>
            <div class="block-content">
                @if(count($misplacedGroups) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th>Instructor</th>
                                    <th>Course Unit</th>
                                    <th>Schedule Details (Programme | Day | Time)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($misplacedGroups as $group)
                                    <tr>
                                        <td class="fw-semibold">{{ $group['instructor']->name }}</td>
                                        <td>
                                            {{ $group['course_unit']->code }} - {{ $group['course_unit']->name }}
                                        </td>
                                        <td>
                                            <ul class="list-group list-group-flush">
                                                @foreach($group['mappings'] as $mapping)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <strong>{{ $mapping->programme->name }}</strong>
                                                            <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $mapping->academic_session_id, 'programme' => $mapping->programme_id]) }}" 
                                                               class="text-secondary ms-1" 
                                                               title="Edit Schedule"
                                                               target="_blank">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                        </span>
                                                        <span class="badge bg-defaul text-dark">
                                                            {{ $mapping->day->name ?? 'Not Set' }} |
                                                            @if($mapping->morning_start_time)
                                                                Morn: {{ $mapping->morning_start_time->format('H:i') }}
                                                            @endif
                                                            @if($mapping->evening_start_time)
                                                                Eve: {{ $mapping->evening_start_time->format('H:i') }}
                                                            @endif
                                                            @if(!$mapping->morning_start_time && !$mapping->evening_start_time)
                                                                Time Not Set
                                                            @endif
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle me-1"></i> No misplaced sessions found! All common units are scheduled consistently.
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
