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
                        <!-- Hidden inputs for sorting to persist when filtering -->
                        <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                        <input type="hidden" name="sort_order" value="{{ $sortOrder }}">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="academic_session_id" class="form-label">Academic Session</label>
                                <select name="academic_session_id" id="academic_session_id" class="form-select" required>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" {{ $selectedAcademicSessionId == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select name="department_id" id="department_id" class="form-select">
                                    <option value="all">All Departments</option>
                                    @foreach($departments->groupBy('school.name') as $schoolName => $deptGroup)
                                        <optgroup label="{{ $schoolName }}">
                                            @foreach($deptGroup as $department)
                                                <option value="{{ $department->id }}" {{ $selectedDepartmentId == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="assignment_status" class="form-label">Assignment Status</label>
                                <select name="assignment_status" id="assignment_status" class="form-select">
                                    <option value="all" {{ $assignmentStatus == 'all' ? 'selected' : '' }}>All Instructors</option>
                                    <option value="assigned" {{ $assignmentStatus == 'assigned' ? 'selected' : '' }}>Assigned Only</option>
                                    <option value="not_assigned" {{ $assignmentStatus == 'not_assigned' ? 'selected' : '' }}>Not Assigned Only</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="per_page" class="form-label">Rows per page</label>
                                <select name="per_page" id="per_page" class="form-select">
                                    <option value="10" {{ $perPage == '10' ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == '100' ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Export Buttons -->
                    @if($selectedAcademicSessionId)
                        <div class="d-flex justify-content-end mb-3">
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.export-workload-distribution', array_merge(request()->query(), ['format' => 'excel'])) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </a>
                                <a href="{{ route('admin.reports.export-workload-distribution', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                                   class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-1"></i> PDF
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 35%">
                                            <a href="{{ route('admin.reports.workload-distribution', array_merge(request()->query(), ['sort_by' => 'name', 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                                Instructor
                                                @if($sortBy === 'name')
                                                    <i class="bi bi-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted small"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th style="width: 25%">Department</th>
                                        <th style="width: 25%">Course Units</th>
                                        <th style="width: 10%" class="text-center">
                                            <a href="{{ route('admin.reports.workload-distribution', array_merge(request()->query(), ['sort_by' => 'total_units', 'sort_order' => $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                                Total Units
                                                @if($sortBy === 'total_units')
                                                    <i class="bi bi-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                                @else
                                                    <i class="bi bi-arrow-down-up text-muted small"></i>
                                                @endif
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($instructors as $index => $instructor)
                                        <tr>
                                            <td class="text-center">{{ $instructors->firstItem() + $index }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $instructor->full_name }}</div>
                                                <small class="text-muted">{{ $instructor->email }}</small>
                                            </td>
                                            <td>
                                                {{ $instructor->department->name ?? 'N/A' }}
                                                <div class="small text-muted">{{ $instructor->department->school->code ?? '' }}</div>
                                            </td>
                                            <td>{{ $instructor->course_units_display ?: 'No units assigned' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $instructor->total_units > 0 ? 'primary' : 'secondary' }} rounded-pill">
                                                    {{ $instructor->total_units }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                    No workload data found for the selected filters.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                Showing {{ $instructors->firstItem() ?? 0 }} to {{ $instructors->lastItem() ?? 0 }} of {{ $instructors->total() }} results
                            </div>
                            <div>
                                {{ $instructors->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
