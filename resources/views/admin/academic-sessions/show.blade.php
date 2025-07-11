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
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#copyFromPreviousSessionModal">
                                <i class="bi bi-files"></i> Use Previous Session
                            </button>
                            
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
                        <div class="d-flex align-items-center">
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
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.academic-sessions.select-programmes', $academicSession) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i> Add Programmes
                                </a>
                                <a href="{{ route('admin.programmes.create') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-plus-circle"></i> New Programme
                                </a>
                            </div>
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
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Programme Timetables</h5>
                    <div class="d-flex align-items-center">
                        <!-- School Filter Dropdown -->
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
                    </div>
                </div>
                <div class="card-body">
                    <!-- Timetables Table Container (will be updated via AJAX) -->
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Programmes tab elements
    const schoolFilter = document.getElementById('schoolFilter');
    const programmesContainer = document.getElementById('programmes-container');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    // Timetables tab elements
    const timetableSchoolFilter = document.getElementById('timetableSchoolFilter');
    const timetablesContainer = document.getElementById('timetables-container');
    const timetableLoadingIndicator = document.getElementById('timetableLoadingIndicator');
    
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
        
        // Build URL with query parameters
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        url.searchParams.set('tab', 'programmes');
        
        if (schoolId && schoolId !== 'all') {
            url.searchParams.set('school_id', schoolId);
        } else if (schoolFilter && schoolFilter.value && schoolFilter.value !== 'all') {
            url.searchParams.set('school_id', schoolFilter.value);
        } else {
            url.searchParams.delete('school_id');
        }
        
        // Update browser URL without reloading the page
        window.history.pushState({}, '', url);
        
        // Prepare headers with CSRF token
        const headers = new Headers({
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        });

        // Make AJAX request with proper headers
        fetch(`{{ route('admin.academic-sessions.show', $academicSession) }}?${url.searchParams.toString()}`, {
            headers: headers
        })
        .then(async response => {
            if (!response.ok) {
                const error = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, body: ${error}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.html) {
                programmesContainer.innerHTML = data.html;
                programmesContainer.classList.remove('d-none');
                
                // Update pagination links if available
                if (data.pagination) {
                    updatePagination(data, 'programmes');
                }
            } else {
                throw new Error('Invalid response format from server');
            }
        })
        .catch(error => {
            console.error('Error loading programmes:', error);
            programmesContainer.innerHTML = `
                <div class="alert alert-danger">
                    An error occurred while loading programmes. Please try again.<br>
                    <small>${error.message}</small>
                </div>
            `;
            programmesContainer.classList.remove('d-none');
        })
        .finally(() => {
            loadingIndicator.classList.add('d-none');
            isLoading = false;
        });
    }
    
    // Load timetables for the timetables tab
    function loadTimetables(page = 1, schoolId = null) {
        if (isLoading) return;
        
        isLoading = true;
        currentPage = page;
        
        // Show loading indicator
        timetablesContainer.classList.add('d-none');
        timetableLoadingIndicator.classList.remove('d-none');
        
        // Build URL with query parameters
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        url.searchParams.set('tab', 'timetables');
        if (schoolId && schoolId !== 'all') {
            url.searchParams.set('school_id', schoolId);
        } else {
            url.searchParams.delete('school_id');
        }
        
        // Update browser URL without reloading the page
        window.history.pushState({}, '', url);
        
        // Prepare headers with CSRF token
        const headers = new Headers({
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        });

        // Make AJAX request with proper headers
        fetch(`{{ route('admin.academic-sessions.show', $academicSession) }}?${url.searchParams.toString()}`, {
            headers: headers
        })
        .then(async response => {
            if (!response.ok) {
                const error = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, body: ${error}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.html) {
                timetablesContainer.innerHTML = data.html;
                timetablesContainer.classList.remove('d-none');
                
                // Update pagination links if available
                if (data.pagination) {
                    updatePagination(data, 'timetables');
                }
            } else {
                throw new Error('Invalid response format from server');
            }
        })
        .catch(error => {
            console.error('Error loading timetables:', error);
            timetablesContainer.innerHTML = `
                <div class="alert alert-danger">
                    An error occurred while loading timetables. Please try again.<br>
                    <small>${error.message}</small>
                </div>
            `;
            timetablesContainer.classList.remove('d-none');
        })
        .finally(() => {
            timetableLoadingIndicator.classList.add('d-none');
            isLoading = false;
        });
    }
    
    // Update pagination for a container
    function updatePagination(data, containerType = 'programmes') {
        const container = containerType === 'programmes' ? programmesContainer : timetablesContainer;
        const paginationContainer = container.querySelector('.pagination-wrapper') || document.createElement('div');
        
        if (data.pagination) {
            paginationContainer.className = 'pagination-wrapper mt-3';
            paginationContainer.innerHTML = data.pagination;
            
            // If pagination wrapper doesn't exist, add it after the table
            if (!container.querySelector('.pagination-wrapper')) {
                const table = container.querySelector('table');
                if (table) {
                    table.insertAdjacentElement('afterend', paginationContainer);
                } else {
                    container.appendChild(paginationContainer);
                }
            }
            
            // Update pagination links
            updatePaginationLinks(containerType);
        } else {
            // Remove pagination if no pages
            if (paginationContainer.parentNode) {
                paginationContainer.parentNode.removeChild(paginationContainer);
            }
        }
    }
    
    // Update pagination links to use AJAX
    function updatePaginationLinks(containerType = 'programmes') {
        const container = containerType === 'programmes' ? programmesContainer : timetablesContainer;
        const paginationLinks = container.querySelectorAll('.pagination a');
        const schoolFilterElement = containerType === 'programmes' ? schoolFilter : timetableSchoolFilter;
        const loadFunction = containerType === 'programmes' ? loadProgrammes : loadTimetables;
        
        paginationLinks.forEach(link => {
            if (link.getAttribute('href') && !link.hasAttribute('data-handled')) {
                const url = new URL(link.href);
                const page = url.searchParams.get('page') || 1;
                const schoolId = schoolFilterElement ? schoolFilterElement.value : null;
                
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadFunction(page, schoolId);
                });
                
                // Mark as handled to prevent duplicate event listeners
                link.setAttribute('data-handled', 'true');
            }
        });
    }
    
    // Handle popstate (back/forward navigation)
    window.addEventListener('popstate', function() {
        const url = new URL(window.location);
        const page = url.searchParams.get('page') || 1;
        const schoolId = url.searchParams.get('school_id') || 'all';
        const tab = url.searchParams.get('tab') || 'programmes';
        
        // Update active tab if needed
        if (tab === 'timetables') {
            document.querySelector('#timetables-tab').click();
            if (timetableSchoolFilter) {
                timetableSchoolFilter.value = schoolId;
            }
            loadTimetables(page, schoolId);
        } else {
            document.querySelector('#programmes-tab').click();
            if (schoolFilter) {
                schoolFilter.value = schoolId;
            }
            loadProgrammes(page, schoolId);
        }
    });
    
    // Handle tab changes
    const tabEl = document.querySelector('button[data-bs-toggle="tab"][data-bs-target="#timetables"]');
    if (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (e) {
            // Update URL to reflect the active tab
            const url = new URL(window.location);
            url.searchParams.set('tab', 'timetables');
            window.history.pushState({}, '', url);
        });
    }
    
    // Initialize school filter for timetables
    if (timetableSchoolFilter) {
        timetableSchoolFilter.addEventListener('change', function() {
            loadTimetables(1, this.value);
        });
    }
    
    // Initialize school filter for programmes
    if (schoolFilter) {
        schoolFilter.addEventListener('change', function() {
            loadProgrammes(1, this.value);
        });
    }
    
    // Initial load based on current tab
    const activeTab = window.location.hash === '#timetables' ? 'timetables' : 'programmes';
    
    // Set initial filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    const schoolId = urlParams.get('school_id');
    const page = parseInt(urlParams.get('page')) || 1;
    
    if (activeTab === 'timetables') {
        if (timetableSchoolFilter && schoolId) {
            timetableSchoolFilter.value = schoolId;
        }
        loadTimetables(page, schoolId || (timetableSchoolFilter ? timetableSchoolFilter.value : null));
    } else {
        if (schoolFilter && schoolId) {
            schoolFilter.value = schoolId;
        }
        loadProgrammes(page, schoolId || (schoolFilter ? schoolFilter.value : null));
    }
    
    // Update URL hash when tabs are changed
    const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabEls.forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('data-bs-target');
            const tab = target === '#timetables' ? 'timetables' : 'programmes';
            
            // Update URL to reflect the active tab
            const url = new URL(window.location);
            url.hash = tab === 'timetables' ? '#timetables' : '';
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
            
            // Load data for the tab if it hasn't been loaded yet
            if (tab === 'timetables' && timetablesContainer && timetablesContainer.children.length === 0) {
                loadTimetables(1, timetableSchoolFilter ? timetableSchoolFilter.value : null);
            }
        });
    });
});
</script>
@endpush

@endsection
