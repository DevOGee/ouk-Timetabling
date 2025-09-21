@extends('layouts.app')
@section('title', 'Workload Distribution')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Workload Distribution</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.workload-distribution') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="academic_session_id">Academic Session</label>
                                    <select name="academic_session_id" id="academic_session_id" class="form-control" required>
                                        @foreach($academicSessions as $session)
                                            <option value="{{ $session->id }}" {{ $selectedAcademicSessionId == $session->id ? 'selected' : '' }}>
                                                {{ $session->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="school_id">School (Optional)</label>
                                    <select name="school_id" id="school_id" class="form-control">
                                        <option value="all">All Schools</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    @if($selectedAcademicSessionId)
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <div class="btn-group">
                                    <a href="{{ route('admin.reports.export-workload-distribution', ['format' => 'excel']) }}?academic_session_id={{ $selectedAcademicSessionId }}&school_id={{ $selectedSchoolId }}" 
                                       class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </a>
                                    <a href="{{ route('admin.reports.export-workload-distribution', ['format' => 'pdf']) }}?academic_session_id={{ $selectedAcademicSessionId }}&school_id={{ $selectedSchoolId }}" 
                                       class="btn btn-danger">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Instructor</th>
                                        <th>Course Units</th>
                                        <th>Total Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($workloadData as $index => $instructor)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $instructor->name }}</td>
                                            <td>{{ $instructor->course_units ?: 'No units assigned' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary text-white">{{ $instructor->total_units }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                No workload data found for the selected filters.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
