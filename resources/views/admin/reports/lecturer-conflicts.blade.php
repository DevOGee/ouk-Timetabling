@extends('layouts.app')
@section('title', 'Lecturer Conflicts - Reports')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Lecturer Conflict Report</h1>
                <div>
                    <a href="{{ route('admin.reports.export-lecturer-conflicts', ['format' => 'pdf']) }}" 
                       class="btn btn-danger" id="export-pdf">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export Report
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
            <form action="{{ route('admin.reports.lecturer-conflicts') }}" method="GET" class="form-inline">
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
        <div class="mb-4">
            <div class="mb-4">
                <h4 class="text-gray-800">Lecturer Scheduling Conflicts</h4>
            </div>
            @php $instructorNumber = 1; @endphp
            @foreach($conflicts as $instructorId => $instructorConflicts)
                    @php 
                        $firstConflict = $instructorConflicts->first();
                        $instructor = is_array($firstConflict) ? $firstConflict['instructor'] : $firstConflict->instructor;
                    @endphp
                    
                    <div class="mb-4 border-bottom pb-2">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 1rem; margin-right: 12px;">
                                {{ $instructorNumber++ }}
                            </span>
                            <h5 class="mb-0 d-flex align-items-center">
                                {{ $instructor->name }}
                                @if($instructor->school)
                                    <span class="badge bg-secondary ms-3" style="font-weight: 500; font-size: 0.8rem; padding: 0.35em 0.8em;">
                                        <i class="fas fa-school me-1"></i>{{ $instructor->school->name }}
                                    </span>
                                @endif
                            </h5>
                        </div>
                        
                        @foreach($instructorConflicts as $conflict)
                            @php 
                                $conflict = is_array($conflict) ? (object)$conflict : $conflict;
                            @endphp
                            <div class="card mb-3 border-left-danger">
                                <div class="card-header py-2 bg-light">
                                    <strong>Conflict on {{ $conflict->day ?? 'Unknown Day' }} ({{ $conflict->time_period ?? 'Unknown Time' }})</strong>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th>Course</th>
                                                <th>Programme</th>
                                                <th>Time</th>
                                                <th>Session</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($conflict->conflicting_slots) && is_array($conflict->conflicting_slots))
                                                @foreach($conflict->conflicting_slots as $slot)
                                                    @php 
                                                        $slot = (object)$slot;
                                                        // Format time as 5.30PM - 8.30PM
                                                        $formatTime = function($timeStr) {
                                                            if (empty($timeStr)) return 'N/A';
                                                            try {
                                                                return \Carbon\Carbon::createFromFormat('H:i:s', $timeStr)->format('g.ia');
                                                            } catch (\Exception $e) {
                                                                try {
                                                                    return \Carbon\Carbon::parse($timeStr)->format('g.ia');
                                                                } catch (\Exception $e) {
                                                                    return $timeStr;
                                                                }
                                                            }
                                                        };
                                                        
                                                        $timeParts = explode(' - ', $slot->time ?? '');
                                                        $formattedTime = count($timeParts) === 2 
                                                            ? $formatTime(trim($timeParts[0])) . ' - ' . $formatTime(trim($timeParts[1]))
                                                            : 'N/A';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $slot->course ?? 'N/A' }}</td>
                                                        <td>{{ $slot->programme ?? 'N/A' }}</td>
                                                        <td>{{ $conflict->day }} {{ $formattedTime }}</td>
                                                        <td>{{ $slot->type ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No conflict details available</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(!$loop->last)
                        <hr class="my-4">
                    @endif
                @endforeach
            </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No lecturer scheduling conflicts found for the selected filters.
        </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Update export links with current filters
        const updateExportLinks = () => {
            const academicSessionId = $('#academic_session_id').val();
            const schoolId = $('#school_id').val();
            
            // Get the current URL parameters
            const params = new URLSearchParams(window.location.search);
            
            // Update or add our parameters
            params.set('academic_session_id', academicSessionId);
            params.set('school_id', schoolId);
            
            // Update both export links
            $('#export-excel, #export-pdf').each(function() {
                const format = $(this).attr('id') === 'export-excel' ? 'excel' : 'pdf';
                let url = '{{ route("admin.reports.export-lecturer-conflicts", ["format" => "FORMAT"]) }}';
                url = url.replace('FORMAT', format) + '?' + params.toString();
                $(this).attr('href', url);
            });
        };

        // Update export links when filters change
        $('#academic_session_id, #school_id').change(updateExportLinks);
        
        // Initialize export links on page load
        updateExportLinks();
    });
</script>
@endpush
@endsection
