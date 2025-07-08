@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Academic Session: {{ $academicSession->name }}</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.academic-sessions.edit', $academicSession) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Session Details</h5>
                    <dl class="row">
                        <dt class="col-sm-4">Code:</dt>
                        <dd class="col-sm-8">{{ $academicSession->code }}</dd>

                        <dt class="col-sm-4">Duration:</dt>
                        <dd class="col-sm-8">{{ $academicSession->duration }}</dd>

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
                        <dd class="col-sm-8">{{ $academicSession->start_date->format('F d, Y') }}</dd>

                        <dt class="col-sm-4">End Date:</dt>
                        <dd class="col-sm-8">{{ $academicSession->end_date->format('F d, Y') }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Description</h5>
                    <p>{{ $academicSession->description ?? 'No description provided.' }}</p>

                    <div class="mt-4">
                        <h5>Quick Actions</h5>
                        <div class="d-flex gap-2">
                            @if(!$academicSession->is_current)
                                <form action="{{ route('admin.academic-sessions.set-current', $academicSession) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check-circle"></i> Set as Current
                                    </button>
                                </form>
                            @endif

                            @if($academicSession->status !== 'archived')
                                <form action="{{ route('admin.academic-sessions.archive', $academicSession) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning" 
                                            onclick="return confirm('Are you sure you want to archive this session?')">
                                        <i class="bi bi-archive"></i> Archive
                                    </button>
                                </form>
                            @endif

                            @if(!$academicSession->timetables()->exists())
                                <form action="{{ route('admin.academic-sessions.destroy', $academicSession) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this session?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
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

    <!-- Tab Content -->
    <div class="tab-content" id="sessionTabsContent">
        <!-- Programmes Tab -->
        <div class="tab-pane fade show active" id="programmes" role="tabpanel" aria-labelledby="programmes-tab">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Programmes</h5>
                    <div class="d-flex align-items-center">
                        <!-- School Filter Dropdown -->
                        @if(isset($schools) && $schools->count() > 0)
                            <div class="me-3">
                                <label for="schoolFilter" class="form-label mb-0 me-2">Filter by School:</label>
                                <select id="schoolFilter" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                    <option value="all" {{ !request()->has('school_id') ? 'selected' : '' }}>All Schools</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        <div>
                            <a href="{{ route('admin.academic-sessions.select-programmes', $academicSession) }}" class="btn btn-sm btn-primary me-2">
                                <i class="bi bi-plus"></i> Add/Manage Programmes
                            </a>
                            <a href="{{ route('admin.programmes.create') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-plus-circle"></i> New Programme
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Programs Table Container (will be updated via AJAX) -->
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

        <!-- Timetables Tab -->
        <div class="tab-pane fade" id="timetables" role="tabpanel" aria-labelledby="timetables-tab">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Timetables</h5>
                <a href="#" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Add Timetable
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($academicSession->timetables->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($academicSession->timetables as $timetable)
                                <tr>
                                    <td>{{ $timetable->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $timetable->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($timetable->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($timetable->is_published)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-warning text-dark">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $timetable->start_date->format('M d, Y') }}</td>
                                    <td>{{ $timetable->end_date->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    No timetables found for this academic session.
                </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolFilter = document.getElementById('schoolFilter');
    const programmesContainer = document.getElementById('programmes-container');
    const loadingIndicator = document.getElementById('loadingIndicator');
    let currentPage = 1;
    let isLoading = false;

    // Function to load programmes via AJAX
    function loadProgrammes(page = 1, schoolId = null) {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        
        // Show loading indicator
        programmesContainer.classList.add('d-none');
        loadingIndicator.classList.remove('d-none');
        
        // Get the current school filter value
        const selectedSchoolId = schoolId !== null ? schoolId : (schoolFilter && schoolFilter.value !== 'all' ? schoolFilter.value : '');
        
        // Build URL with query parameters
        const url = new URL(window.location.href);
        const params = new URLSearchParams();
        
        // Only add school_id parameter if a school is selected and not 'all'
        if (selectedSchoolId && selectedSchoolId !== 'all') {
            params.append('school_id', selectedSchoolId);
        }
        
        if (page > 1) {
            params.append('page', page);
        }
        
        // Add CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Make AJAX request
        fetch(`${url}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update the programmes container
            programmesContainer.innerHTML = data.html;
            
            // Update pagination
            const paginationContainer = document.createElement('div');
            paginationContainer.innerHTML = data.pagination;
            
            // Find the existing pagination container or create a new one
            const existingPagination = programmesContainer.querySelector('.pagination-container');
            const newPagination = document.createElement('div');
            newPagination.className = 'pagination-container mt-3';
            
            // If we have pagination content, add it to the container
            const paginationContent = paginationContainer.querySelector('.pagination');
            if (paginationContent) {
                newPagination.appendChild(paginationContent);
                
                // Add event listeners to pagination links
                newPagination.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = this.getAttribute('href').match(/page=(\d+)/)?.[1] || 1;
                        loadProgrammes(page, schoolFilter.value);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                });
                
                // Replace or add the pagination container
                const table = programmesContainer.querySelector('table');
                if (existingPagination) {
                    existingPagination.replaceWith(newPagination);
                } else if (table) {
                    table.insertAdjacentElement('afterend', newPagination);
                } else {
                    programmesContainer.appendChild(newPagination);
                }
            } else if (existingPagination) {
                // Remove pagination if no pages
                existingPagination.remove();
            }
            
            // Show the container and hide loading indicator
            programmesContainer.classList.remove('d-none');
            loadingIndicator.classList.add('d-none');
            
            // Update URL without page reload
            const newUrl = new URL(window.location);
            if (schoolId) {
                newUrl.searchParams.set('school_id', schoolId);
            } else {
                newUrl.searchParams.delete('school_id');
            }
            
            if (page > 1) {
                newUrl.searchParams.set('page', page);
            } else {
                newUrl.searchParams.delete('page');
            }
            
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Error loading programmes:', error);
            programmesContainer.classList.remove('d-none');
            loadingIndicator.classList.add('d-none');
            
            // Show error message
            programmesContainer.innerHTML = `
                <div class="alert alert-danger">
                    An error occurred while loading programmes. Please try again.
                    <button class="btn btn-sm btn-outline-secondary ms-3" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Reload
                    </button>
                </div>
            `;
        })
        .finally(() => {
            isLoading = false;
        });
    }
    
    // Update URL without reloading the page
    function updateUrl(page, schoolId) {
        const url = new URL(window.location);
        
        // Update or remove page parameter
        if (page > 1) {
            url.searchParams.set('page', page);
        } else {
            url.searchParams.delete('page');
        }
        
        // Update or remove school_id parameter
        if (schoolId && schoolId !== 'all') {
            url.searchParams.set('school_id', schoolId);
        } else {
            url.searchParams.delete('school_id');
        }
        
        // Update URL without reloading
        window.history.pushState({}, '', url);
    }
    
    // Handle school filter change
    if (schoolFilter) {
        schoolFilter.addEventListener('change', function() {
            const schoolId = this.value;
            updateUrl(1, schoolId);
            loadProgrammes(1, schoolId);
        });
    }
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const schoolId = urlParams.get('school_id') || 'all';
        const page = parseInt(urlParams.get('page')) || 1;
        
        if (schoolFilter) {
            schoolFilter.value = schoolId;
        }
        
        loadProgrammes(page, schoolId);
    });
    
    // Initial load with any URL parameters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const initialSchoolId = urlParams.get('school_id') || 'all';
        const initialPage = parseInt(urlParams.get('page')) || 1;
        
        if (schoolFilter) {
            schoolFilter.value = initialSchoolId;
        }
        
        // Load the initial data
        loadProgrammes(initialPage, initialSchoolId);
    });
});
</script>
@endpush

@endsection
