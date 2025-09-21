@extends('layouts.app')
@section('title', 'Time Conflicts - Reports')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Time Conflict Report</h1>
                <div>
                    <a href="{{ route('admin.reports.export-time-conflicts', ['format' => 'excel']) }}" 
                       class="btn btn-success" id="export-excel">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                    <a href="{{ route('admin.reports.export-time-conflicts', ['format' => 'pdf']) }}" 
                       class="btn btn-danger" id="export-pdf">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.time-conflicts') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="academic_session_id" class="sr-only">Academic Session</label>
                    <select name="academic_session_id" id="academic_session_id" class="form-control">
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ $selectedAcademicSessionId == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="school_id" class="sr-only">School</label>
                    <select name="school_id" id="school_id" class="form-control">
                        <option value="all">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>
    </div>

    @if($conflicts->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Time Conflicts</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="conflictsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time Slot</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Programme</th>
                                <th>Year/Semester</th>
                                <th>Instructor</th>
                                <th>Session Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conflicts as $conflict)
                                @foreach($conflict['mappings'] as $index => $mapping)
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ count($conflict['mappings']) }}" class="align-middle">
                                                {{ $conflict['day']->name }}
                                            </td>
                                            <td rowspan="{{ count($conflict['mappings']) }}" class="align-middle">
                                                {{ Carbon\Carbon::parse($conflict['start_time'])->format('H:i') }} - 
                                                {{ Carbon\Carbon::parse($conflict['end_time'])->format('H:i') }}
                                            </td>
                                        @endif
                                        <td>{{ $mapping->course_unit->code ?? 'N/A' }}</td>
                                        <td>{{ $mapping->course_unit->name ?? 'N/A' }}</td>
                                        <td>{{ $mapping->programme->programme_code ?? 'N/A' }}</td>
                                        <td>
                                            {{ $mapping->year_of_study ? $mapping->year_of_study->name : 'N/A' }} / 
                                            {{ $mapping->semester ? $mapping->semester->name : 'N/A' }}
                                        </td>
                                        <td>{{ $mapping->instructor ? $mapping->instructor->name : 'N/A' }}</td>
                                        <td>{{ $mapping->session_type }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No time conflicts found for the selected filters.
        </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#conflictsTable').DataTable({
            "pageLength": 50,
            "order": [[0, 'asc'], [1, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [2, 3, 4, 5, 6, 7] }
            ]
        });

        // Update export links with current filters
        const updateExportLinks = () => {
            const academicSessionId = $('#academic_session_id').val();
            const schoolId = $('#school_id').val();
            
            $('#export-excel, #export-pdf').each(function() {
                const format = $(this).attr('id') === 'export-excel' ? 'excel' : 'pdf';
                let url = '{{ route("admin.reports.export-time-conflicts", ["format" => "FORMAT"]) }}';
                url = url.replace('FORMAT', format) + `?academic_session_id=${academicSessionId}&school_id=${schoolId}`;
                $(this).attr('href', url);
            });
        };

        // Update export links when filters change
        $('#academic_session_id, #school_id').change(updateExportLinks);
    });
</script>
@endpush

@endsection
