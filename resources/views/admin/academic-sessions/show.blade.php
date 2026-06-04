@extends('layouts.app')

@push('styles')
<style>
:root {
    --teal:       #037b90;
    --teal-dark:  #024d5c;
    --teal-bg:    rgba(3,123,144,.1);
    --coral:      #ff7f50;
    --green:      #10b981;
    --slate-50:   #f8fafc;
    --slate-100:  #f1f5f9;
    --slate-200:  #e2e8f0;
    --slate-300:  #cbd5e1;
    --slate-500:  #64748b;
    --slate-700:  #334155;
    --slate-900:  #0f172a;
    --radius-md:  14px;
    --radius-sm:  10px;
    --card-shadow:0 1px 3px rgba(15,23,42,.06), 0 4px 20px rgba(15,23,42,.06);
    --border:     1.5px solid rgba(226,232,240,.9);
}
.content-wrapper { background:transparent!important; box-shadow:none!important; padding:1.8rem 2rem!important; }
.page-fade-in { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) both; }
.stagger-1    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .05s both; }
.stagger-2    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .12s both; }
.stagger-3    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .20s both; }
@keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes slideProgress { from { width: 0%; } }

/* Breadcrumb */
.breadcrumb-nav{display:flex;align-items:center;gap:.4rem;font-size:.78rem;color:var(--slate-500);margin-bottom:1.25rem}
.breadcrumb-nav a{color:var(--slate-500);text-decoration:none;transition:color .15s}
.breadcrumb-nav a:hover{color:var(--teal)}
.breadcrumb-nav .sep{color:var(--slate-300)}
.breadcrumb-nav .current{color:var(--slate-700);font-weight:600}

/* Page Header */
.page-header{display:flex;align-items:center;gap:1rem;margin-bottom:1.75rem}
.header-icon{width:48px;height:48px;background:var(--teal-bg);color:var(--teal);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.3rem}
.header-title{font-size:1.4rem;font-weight:700;color:var(--slate-900);margin:0;display:flex;align-items:center;gap:.75rem}
.header-subtitle{font-size:.9rem;color:var(--slate-500);margin:.2rem 0 0}
.session-pill{font-size:.65rem;font-weight:700;padding:.3rem .6rem;border-radius:100px;background:var(--teal-bg);color:var(--teal);display:inline-flex;align-items:center;gap:.3rem;text-transform:uppercase;letter-spacing:.5px}

/* Info Card */
.info-card { background:#fff; border-radius:var(--radius-md); box-shadow:var(--card-shadow); padding:1.5rem; margin-bottom:1.5rem; display:grid; grid-template-columns:1fr 1fr; gap:2rem; }
.info-section h3 { font-size:1.05rem; font-weight:700; color:var(--slate-900); margin-bottom:1rem; border-bottom:1px solid var(--slate-100); padding-bottom:.5rem; }
.info-row { display:flex; justify-content:space-between; margin-bottom:.75rem; font-size:.875rem; border-bottom:1px dashed var(--slate-100); padding-bottom:.5rem; }
.info-label { color:var(--slate-500); font-weight:500; }
.info-value { color:var(--slate-900); font-weight:600; text-align:right; }

/* Status Badges */
.status-badge { font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:100px; text-transform:uppercase; }
.status-active { background:var(--green); color:#fff; }
.status-upcoming { background:#3b82f6; color:#fff; }
.status-completed { background:var(--slate-500); color:#fff; }
.status-archived { background:var(--slate-700); color:#fff; }

/* Action Buttons */
.action-grid { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:.5rem; }
.btn-outline-soft { background:#fff; color:var(--slate-700); border:1.5px solid var(--slate-200); padding:.5rem .9rem; font-size:.85rem; font-weight:600; border-radius:var(--radius-sm); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
.btn-outline-soft:hover { border-color:var(--teal); color:var(--teal); background:var(--teal-bg); }
.btn-premium { background:var(--teal); color:#fff; border:none; padding:.5rem .9rem; font-size:.85rem; font-weight:600; border-radius:var(--radius-sm); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
.btn-premium:hover { background:var(--teal-dark); color:#fff; }

/* Form Fields */
.field-input {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    color: var(--slate-900);
    background-color: #fff;
    border: 1.5px solid var(--slate-200);
    border-radius: var(--radius-sm);
    transition: all 0.2s;
}
.field-input:focus {
    outline: none;
    border-color: var(--teal);
    box-shadow: 0 0 0 3px var(--teal-bg);
}
select.field-input {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right .75rem center;
    background-size: 1.2em;
    padding-right: 2.2rem !important;
}
select.field-input:focus {
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23037b90' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
}

/* Custom Tabs */
.custom-tabs { display:flex; gap:1.5rem; border-bottom:1.5px solid var(--slate-200); margin-bottom:1.5rem; }
.custom-tab { background:none; border:none; padding:.75rem 0; font-size:.9rem; font-weight:600; color:var(--slate-500); cursor:pointer; position:relative; transition:color .2s; }
.custom-tab:hover { color:var(--slate-900); }
.custom-tab.active { color:var(--teal); }
.custom-tab.active::after { content:''; position:absolute; bottom:-1.5px; left:0; width:100%; height:3px; background:var(--teal); border-radius:3px 3px 0 0; }

@media(max-width:768px) { .info-card { grid-template-columns:1fr; gap:1.5rem; } }
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">
    
    <nav class="breadcrumb-nav stagger-1">
        <a href="{{ route('admin.academic-sessions.index') }}"><i class="bi bi-calendar2-week"></i> Academic Sessions</a>
        <span class="sep">/</span>
        <span class="current">{{ Str::limit($academicSession->name, 30) }}</span>
    </nav>

    <div class="page-header stagger-1">
        <a href="{{ route('admin.academic-sessions.index') }}" class="btn-outline-soft" style="padding:.5rem .9rem;flex-shrink:0;" title="Back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="header-icon"><i class="bi bi-journal-text"></i></div>
        <div>
            <h1 class="header-title">
                {{ $academicSession->name }}
                @if($academicSession->is_current)
                    <span class="session-pill"><i class="bi bi-star-fill"></i> Current</span>
                @endif
            </h1>
            <p class="header-subtitle">View and manage this academic session's details and programmes.</p>
        </div>
        
        @if(auth()->user()->hasRole('admin'))
        <div style="margin-left:auto;">
            <a href="{{ route('admin.academic-sessions.edit', $academicSession) }}" class="btn-premium">
                <i class="bi bi-pencil"></i> Edit Session
            </a>
        </div>
        @endif
    </div>

    <div class="info-card stagger-2">
        <div class="info-section">
            <h3>Session Details</h3>
            <div class="info-row">
                <span class="info-label">Code</span>
                <span class="info-value">{{ $academicSession->code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Duration</span>
                <span class="info-value">{{ $academicSession->duration }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="status-badge status-{{ $academicSession->status }}">
                        {{ ucfirst($academicSession->status) }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Start Date</span>
                <span class="info-value">{{ $academicSession->start_date->format('F d, Y') }}</span>
            </div>
            <div class="info-row" style="border:none;">
                <span class="info-label">End Date</span>
                <span class="info-value">{{ $academicSession->end_date->format('F d, Y') }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3>Description & Actions</h3>
            <p style="font-size:.875rem;color:var(--slate-700);margin-bottom:1.5rem;">
                {{ $academicSession->description ?? 'No description provided.' }}
            </p>

            @if(!auth()->user()->hasRole('timetabler'))
                <h4 style="font-size:.85rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;margin-bottom:.5rem;">Quick Actions</h4>
                <div class="action-grid">
                    @if(!$hasProgrammes && $otherSessions->isNotEmpty())
                        <button type="button" class="btn-outline-soft" data-bs-toggle="modal" data-bs-target="#copyFromPreviousSessionModal">
                            <i class="bi bi-files"></i> Use Previous Session
                        </button>
                    @endif
                    
                    @if(!$academicSession->is_current)
                        <form action="{{ route('admin.academic-sessions.set-current', $academicSession) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-outline-soft" style="color:var(--teal);">
                                <i class="bi bi-check-circle"></i> Set as Current
                            </button>
                        </form>
                    @endif

                    @if($academicSession->status !== 'archived')
                        <form action="{{ route('admin.academic-sessions.archive', $academicSession) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="button" class="btn-outline-soft btn-confirm-trigger" 
                                    data-confirm-title="Archive Session"
                                    data-confirm-text="Are you sure you want to archive this session?"
                                    data-confirm-color="#f59e0b"
                                    data-confirm-icon="bi-archive">
                                <i class="bi bi-archive"></i> Archive
                            </button>
                        </form>
                    @endif

                    @if(!$academicSession->timetables()->exists())
                        <form action="{{ route('admin.academic-sessions.destroy', $academicSession) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-outline-soft btn-confirm-trigger" style="color:var(--coral);border-color:rgba(255,127,80,.4);"
                                    data-confirm-title="Delete Session"
                                    data-confirm-text="Are you sure you want to delete this session? This action cannot be undone."
                                    data-confirm-color="#ff7f50"
                                    data-confirm-icon="bi-trash">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="nav custom-tabs stagger-3" role="tablist">
        <button class="nav-link custom-tab {{ request('tab', 'programmes') === 'programmes' ? 'active' : '' }}" id="programmes-tab" data-bs-toggle="tab" data-bs-target="#programmes" type="button" role="tab" aria-controls="programmes" aria-selected="{{ request('tab', 'programmes') === 'programmes' ? 'true' : 'false' }}" style="padding-left:0;padding-right:0;">
            <i class="bi bi-diagram-3"></i> Programmes
        </button>
        <button class="nav-link custom-tab {{ request('tab') === 'timetables' ? 'active' : '' }}" id="timetables-tab" data-bs-toggle="tab" data-bs-target="#timetables" type="button" role="tab" aria-controls="timetables" aria-selected="{{ request('tab') === 'timetables' ? 'true' : 'false' }}" style="padding-left:0;padding-right:0;">
            <i class="bi bi-calendar3"></i> Timetables
        </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content stagger-3" id="sessionTabsContent">
        <!-- Programmes Tab -->
        <div class="tab-pane fade {{ request('tab', 'programmes') === 'programmes' ? 'show active' : '' }}" id="programmes" role="tabpanel" aria-labelledby="programmes-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="font-size:1.1rem;font-weight:700;color:var(--slate-900);margin:0;">Programmes</h2>
                @if(!auth()->user()->hasRole('timetabler'))
                <div class="d-flex align-items-center gap-3">
                    @if(isset($schools) && $schools->count() > 0)
                        <div class="d-flex align-items-center gap-2">
                            <label for="schoolFilter" style="font-size:.85rem;color:var(--slate-500);font-weight:500;margin:0;">School:</label>
                            <select id="schoolFilter" class="field-input" style="padding:.4rem 2rem .4rem .75rem;font-size:.85rem;border-radius:6px;border:1.5px solid var(--slate-200);width:auto;">
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
                        <a href="{{ route('admin.academic-sessions.select-programmes', $academicSession) }}" class="btn-premium">
                            <i class="bi bi-plus-lg"></i> Add Programmes
                        </a>
                        <a href="{{ route('admin.programmes.create') }}" class="btn-outline-soft">
                            <i class="bi bi-plus-circle"></i> New
                        </a>
                    </div>
                </div>
                @endif
            </div>
            <div class="info-card" style="padding:0;display:block;">
                <!-- Programs Table Container (will be updated via AJAX) -->
                <div id="programmes-container">
                    @include('admin.academic-sessions.partials.programmes-table', ['programmes' => $programmes, 'academicSession' => $academicSession])
                </div>
                
                <!-- Loading Indicator -->
                <div id="loadingIndicator" class="text-center d-none" style="padding:3rem 0;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2" style="color:var(--slate-500);font-size:.875rem;">Loading programmes...</p>
                </div>
            </div>
        </div>

        <!-- Timetables Tab -->
        <div class="tab-pane fade {{ request('tab') === 'timetables' ? 'show active' : '' }}" id="timetables" role="tabpanel" aria-labelledby="timetables-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 style="font-size:1.1rem;font-weight:700;color:var(--slate-900);margin:0;">Programme Timetables</h2>
                <div class="d-flex align-items-center gap-3">
                    @if(!auth()->user()->hasRole('timetabler'))
                        @if(isset($schools) && $schools->count() > 0)
                            <div class="d-flex align-items-center gap-2">
                                <label for="timetableSchoolFilter" style="font-size:.85rem;color:var(--slate-500);font-weight:500;margin:0;">School:</label>
                                <select id="timetableSchoolFilter" class="field-input" style="padding:.4rem 2rem .4rem .75rem;font-size:.85rem;border-radius:6px;border:1.5px solid var(--slate-200);width:auto;">
                                    <option value="all">All Schools</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        
                        @if($programmesForTimetable->isNotEmpty())
                            <div class="dropdown">
                                <button class="btn-premium dropdown-toggle" type="button" id="addTimetableDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-plus"></i> Add Timetable
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="addTimetableDropdown" style="border:none;box-shadow:var(--card-shadow);border-radius:8px;font-size:.85rem;">
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
            <div class="info-card" style="padding:0;display:block;">
                <!-- Timetables Table Container (will be updated via AJAX) -->
                <div id="timetables-container">
                    @include('admin.academic-sessions.partials.timetables-table', [
                        'programmes' => $programmes,
                        'academicSession' => $academicSession
                    ])
                </div>
                
                <!-- Loading Indicator -->
                <div id="timetableLoadingIndicator" class="text-center d-none" style="padding:3rem 0;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2" style="color:var(--slate-500);font-size:.875rem;">Loading timetables...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.confirm-modal')
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
        
        // Use provided schoolId or fall back to filter value
        const effectiveSchoolId = schoolId || (schoolFilter && schoolFilter.value !== 'all' ? schoolFilter.value : null);
        
        if (effectiveSchoolId && effectiveSchoolId !== 'all') {
            url.searchParams.set('school_id', effectiveSchoolId);
        } else {
            url.searchParams.delete('school_id');
        }
        
        // Update browser URL without reloading the page
        window.history.pushState({}, '', url);
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Make AJAX request with proper headers
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
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
                
                // Update pagination links with new styles
                updatePaginationLinks('programmes');
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
        if (timetableLoadingIndicator) {
            timetableLoadingIndicator.classList.remove('d-none');
        }
        
        // Build URL with query parameters
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        url.searchParams.set('tab', 'timetables');
        
        // Use provided schoolId or fall back to filter value
        const effectiveSchoolId = schoolId || (timetableSchoolFilter && 
            timetableSchoolFilter.value !== 'all' ? timetableSchoolFilter.value : null);
        
        if (effectiveSchoolId && effectiveSchoolId !== 'all') {
            url.searchParams.set('school_id', effectiveSchoolId);
        } else {
            url.searchParams.delete('school_id');
        }
        
        // Update browser URL without reloading the page
        window.history.pushState({}, '', url);
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        
        // Make AJAX request with proper headers
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
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
        
        if (data.html) {
            // Create a temporary div to hold the new content
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            
            // Find the pagination in the new content
            const newPagination = tempDiv.querySelector('.pagination');
            
            if (newPagination) {
                // Find or create the pagination container
                let paginationContainer = container.querySelector('.pagination-container');
                
                if (!paginationContainer) {
                    paginationContainer = document.createElement('div');
                    paginationContainer.className = 'mt-3 d-flex justify-content-center';
                    
                    // Add the container after the table or at the end of the container
                    const table = container.querySelector('table');
                    if (table) {
                        table.insertAdjacentElement('afterend', paginationContainer);
                    } else {
                        container.appendChild(paginationContainer);
                    }
                }
                
                // Update the pagination content
                paginationContainer.innerHTML = '';
                const nav = document.createElement('nav');
                nav.innerHTML = newPagination.outerHTML;
                paginationContainer.appendChild(nav);
                
                // Update pagination links
                updatePaginationLinks(containerType);
            } else if (container.querySelector('.pagination-container')) {
                // Remove pagination if no pages
                container.querySelector('.pagination-container').remove();
            }
        }
    }
    
    // Update pagination links to use AJAX with new styles
    function updatePaginationLinks(containerType = 'programmes') {
        const container = containerType === 'programmes' ? programmesContainer : timetablesContainer;
        const paginationLinks = container.querySelectorAll('.pagination a');
        const schoolFilterElement = containerType === 'programmes' ? schoolFilter : timetableSchoolFilter;
        const loadFunction = containerType === 'programmes' ? loadProgrammes : loadTimetables;
        
        paginationLinks.forEach(link => {
            // Skip if already processed or doesn't have a href
            if (!link.getAttribute('href') || link.hasAttribute('data-handled')) {
                return;
            }
            
            // Get the page number from the URL
            const url = new URL(link.href);
            const page = url.searchParams.get('page') || 1;
            const schoolId = schoolFilterElement ? schoolFilterElement.value : null;
            
            // Update the link to include all current query parameters
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('page', page);
            if (schoolId && schoolId !== 'all') {
                newUrl.searchParams.set('school_id', schoolId);
            } else {
                newUrl.searchParams.delete('school_id');
            }
            newUrl.searchParams.set('tab', containerType);
            
            // Update the link's href
            link.href = newUrl.toString();
            
            // Add click handler
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update URL without reloading
                window.history.pushState({}, '', newUrl);
                
                // Load the data
                loadFunction(page, schoolId);
                
                // Scroll to top of container
                container.scrollIntoView({ behavior: 'smooth' });
            });
            
            // Mark as handled to prevent duplicate event listeners
            link.setAttribute('data-handled', 'true');
        });
        
        // Update active state based on current page
        const currentPage = new URL(window.location).searchParams.get('page') || 1;
        container.querySelectorAll('.page-item').forEach(item => {
            item.classList.remove('active');
            const pageLink = item.querySelector('.page-link');
            if (pageLink && !pageLink.getAttribute('href')) {
                const pageText = pageLink.textContent.trim();
                if (pageText === currentPage.toString() || 
                    (pageText === '« Prev' && currentPage > 1) ||
                    (pageText === 'Next »' && currentPage < (container.dataset.lastPage || 1))) {
                    item.classList.add('active');
                }
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
