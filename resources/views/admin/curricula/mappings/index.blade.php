@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Course Unit Mappings</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}">{{ $academicSession->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum]) }}">{{ $curriculum->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Course Units</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.academic-sessions.curricula.mappings.create', [$academicSession, $curriculum]) }}" 
               class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Course Unit
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="programmeFilter">
                        <option value="">All Programmes</option>
                        @foreach($programmes as $programme)
                            <option value="{{ $programme->id }}" {{ request('programme_id') == $programme->id ? 'selected' : '' }}>
                                {{ $programme->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="yearFilter">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" {{ request('year_of_study_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="semesterFilter">
                        <option value="">All Semesters</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search course units..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="resetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($mappings->isEmpty())
                <div class="text-center p-4">
                    <p class="text-muted">No course unit mappings found.</p>
                    <a href="{{ route('admin.academic-sessions.curricula.mappings.create', [$academicSession, $curriculum]) }}" 
                       class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add your first course unit
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course Unit</th>
                                <th>Programme</th>
                                <th>Year</th>
                                <th>Semester</th>
                                <th>Instructor</th>
                                <th>Type</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mappings as $mapping)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $mapping->courseUnit->code }}</div>
                                        <div class="text-muted small">{{ $mapping->courseUnit->name }}</div>
                                    </td>
                                    <td>{{ $mapping->programme->name }}</td>
                                    <td>{{ $mapping->yearOfStudy->name }}</td>
                                    <td>{{ $mapping->semester->name }}</td>
                                    <td>
                                        @if($mapping->instructor)
                                            {{ $mapping->instructor->name }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($mapping->is_elective)
                                            <span class="badge bg-warning text-dark">Elective</span>
                                        @else
                                            <span class="badge bg-primary">Core</span>
                                        @endif
                                        @if($mapping->max_students)
                                            <div class="small text-muted">Max: {{ $mapping->max_students }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.academic-sessions.curricula.mappings.edit', [$academicSession, $curriculum, $mapping]) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.academic-sessions.curricula.mappings.destroy', [$academicSession, $curriculum, $mapping]) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to remove this course unit from the curriculum?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($mappings->hasPages())
                    <div class="card-footer">
                        {{ $mappings->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Curriculum Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $mappings->total() }}</h3>
                            <p class="text-muted mb-0">Total Course Units</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $programmes->count() }}</h3>
                            <p class="text-muted mb-0">Programmes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $mappings->where('is_elective', false)->count() }}</h3>
                            <p class="text-muted mb-0">Core Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $mappings->where('is_elective', true)->count() }}</h3>
                            <p class="text-muted mb-0">Elective Courses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Filter functionality
    function applyFilters() {
        const params = new URLSearchParams();
        
        const programmeId = document.getElementById('programmeFilter').value;
        if (programmeId) params.set('programme_id', programmeId);
        
        const yearId = document.getElementById('yearFilter').value;
        if (yearId) params.set('year_of_study_id', yearId);
        
        const semesterId = document.getElementById('semesterFilter').value;
        if (semesterId) params.set('semester_id', semesterId);
        
        const search = document.getElementById('searchInput').value.trim();
        if (search) params.set('search', search);
        
        // Update URL without page reload if using history API
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
        
        // Reload the page with the new filters
        window.location.href = newUrl;
    }
    
    // Add event listeners
    document.getElementById('programmeFilter').addEventListener('change', applyFilters);
    document.getElementById('yearFilter').addEventListener('change', applyFilters);
    document.getElementById('semesterFilter').addEventListener('change', applyFilters);
    
    // Debounce search input
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
    
    // Reset filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });
});
</script>
@endpush
@endsection
