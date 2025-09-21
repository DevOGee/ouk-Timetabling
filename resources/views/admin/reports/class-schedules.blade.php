@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">Class Schedules</h1>
            
            <form method="GET" action="{{ route('admin.reports.class-schedules') }}" class="mb-4">
                <div class="row g-3">
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
                    <div class="col-md-4">
                        <label for="school_id" class="form-label">School</label>
                        <select name="school_id" id="school_id" class="form-select">
                            <option value="all">All Schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="show_course_names" 
                                   name="show_course_names" value="1" {{ $showCourseNames ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_course_names">Show Course Names</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="show_instructors" 
                                   name="show_instructors" value="1" {{ $showInstructors ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_instructors">Show Instructors</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->has('school_id') && request('school_id') !== 'all')
                            <a href="{{ route('admin.reports.class-schedules') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('school_id') && $scheduleData->isNotEmpty())
        @php
            $school = request('school_id') !== 'all' ? $schools->firstWhere('id', request('school_id')) : null;
        @endphp
        
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h4>{{ $school ? $school->name : 'All Schools' }}</h4>
                <p>Class Schedule by Programme</p>
            </div>
            <div class="btn-group" role="group">
                {{-- <form method="POST" action="{{ route('admin.reports.export-class-schedules', 'xlsx') }}" class="d-inline me-2">
                    @csrf
                    <input type="hidden" name="academic_session_id" value="{{ request('academic_session_id') }}">
                    <input type="hidden" name="school_id" value="{{ request('school_id') }}">
                    <input type="hidden" name="show_course_names" value="{{ $showCourseNames ? '1' : '0' }}">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export to Excel
                    </button>
                </form> --}}
                <form method="POST" action="{{ route('admin.reports.export-class-schedules', 'pdf') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="academic_session_id" value="{{ request('academic_session_id') }}">
                    <input type="hidden" name="school_id" value="{{ request('school_id') }}">
                    <input type="hidden" name="show_course_names" value="{{ $showCourseNames ? '1' : '0' }}">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export to PDF
                    </button>
                </form>
            </div>
        </div>
        
        @foreach($scheduleData as $programmeData)
            <div class="card mb-5">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $programmeData['programme_code'] }} - {{ $programmeData['programme_name'] }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 100px;">Level</th>
                                    @foreach($days as $day)
                                        <th class="text-center">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programmeData['schedules'] as $row)
                                    <tr>
                                        <td class="fw-bold">{{ $row['row_header'] }}</td>
                                        @foreach($days as $day)
                                            <td>
                                                @if(!empty($row['days'][$day]))
                                                    @foreach($row['days'][$day] as $course)
                                                        <div class="mb-1">
                                                            <div> {{ $course['code'] }}</div>
                                                            @if($showCourseNames)
                                                                <div class="small text-muted">{{ $course['name'] }}</div>
                                                            @endif
                                                            @if($showInstructors && !empty($course['instructors']))
                                                                <div class="small text-primary mt-1">
                                                                    <i class="bi bi-person-fill"></i>
                                                                    @foreach($course['instructors'] as $instructor)
                                                                        {{ $instructor['name'] }}{{ !$loop->last ? ',' : '' }}
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @elseif(request()->has('school_id'))
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> No schedule data found for the selected filters.
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Update programmes dropdown when school changes
    document.getElementById('school_id').addEventListener('change', function() {
        const schoolId = this.value;
        const programmeSelect = document.getElementById('programme_id');
        
        // Clear existing options except the first one
        while (programmeSelect.options.length > 1) {
            programmeSelect.remove(1);
        }
        
        if (schoolId !== 'all') {
            // Fetch programmes for the selected school
            fetch(`/api/schools/${schoolId}/programmes`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(programme => {
                        const option = new Option(programme.name, programme.id);
                        programmeSelect.add(option);
                    });
                })
                .catch(error => console.error('Error fetching programmes:', error));
        } else {
            // If 'All Schools' is selected, we need to reload the page to get all programmes
            // This is a simplified approach - in a real app, you might want to handle this differently
            const form = document.querySelector('form');
            form.submit();
        }
    });
</script>
@endpush
