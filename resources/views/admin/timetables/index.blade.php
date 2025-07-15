@extends('layouts.admin')

@section('title', 'Manage Timetables')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
    @if($academicSession)
        Timetable Publication - {{ $academicSession->name }}
        <span class="badge bg-success">Active Session</span>
    @else
        Timetable Publication - No Active Session
    @endif
</h1>
    </div>
    
    @if(!$academicSession)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No active academic session found. Please set an academic session as active to view timetables.
        </div>
    @endif

    <div class="row">
        <!-- Programs List -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Programs</h5>
                    <span class="badge bg-light text-dark"><span id="program-count">{{ count($programs) }}</span> Programs</span>
                </div>
                <div class="card-body p-0">
                    <!-- Enhanced Tabs Navigation -->
                    <div class="tabs-container">
                        <ul class="nav nav-pills nav-fill mb-3" id="timetableTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active d-flex align-items-center justify-content-center" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                                    <span>All Programs</span>
                                    <span class="badge rounded-pill bg-primary ms-2">{{ count($programs) }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center justify-content-center" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button" role="tab">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <span>Published</span>
                                    <span class="badge rounded-pill bg-success ms-2">{{ $programs->filter(fn($p) => ($p->programmeTimetables->first()?->status ?? '') === 'published')->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center justify-content-center" id="ready-tab" data-bs-toggle="tab" data-bs-target="#ready" type="button" role="tab">
                                    <i class="bi bi-check2-all me-2"></i>
                                    <span>Ready</span>
                                    <span class="badge rounded-pill bg-info ms-2">{{ $programs->filter(fn($p) => ($p->programmeTimetables->first()?->status ?? '') === 'ready')->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center justify-content-center" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    <span>Pending</span>
                                    <span class="badge rounded-pill bg-warning ms-2">{{ $programs->filter(fn($p) => in_array($p->programmeTimetables->first()?->status ?? '', ['pending', 'draft', 'in_progress', 'not_started']))->count() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Tab Content -->
                    <div class="tab-content p-3" id="timetableTabsContent">
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Programme</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="programs-table-body">
                                    @if($academicSession)
                                        @php
                                            // Store program data for JavaScript
                                            $programsData = [];
                                        @endphp
                                        @forelse($programs as $program)
                                            @php
                                                // Get the timetable and mapping status for this program
                                                $timetable = $program->programmeTimetables->first();
                                                
                                                // Get mapping counts for this program in the current session
                                                $mappings = $program->courseUnitMappings()
                                                    ->where('academic_session_id', $academicSession->id)
                                                    ->get();
                                                
                                                // Count completed and in-progress mappings based on actual scheduled times
                                                $completed = 0;
                                                $inProgress = 0;
                                                
                                                foreach ($mappings as $mapping) {
                                                    $hasMorning = $mapping->morning_start_time !== null && $mapping->morning_duration !== null;
                                                    $hasEvening = $mapping->evening_start_time !== null && $mapping->evening_duration !== null;
                                                    
                                                    if ($hasMorning || $hasEvening) {
                                                        $completed++;
                                                    } elseif ($mapping->day_id !== null || $mapping->user_id !== null) {
                                                        $inProgress++;
                                                    }
                                                }
                                                
                                                $totalMappings = $mappings->count();
                                                $notStarted = $totalMappings - $completed - $inProgress;
                                                
                                                // Determine status based on mappings and timetable status
                                                if ($timetable && $timetable->status === 'published') {
                                                    // Published status takes highest priority
                                                    $status = 'published';
                                                    $statusText = 'Published';
                                                    $statusClass = 'success';
                                                } elseif ($totalMappings === 0) {
                                                    // No mappings exist yet
                                                    $status = 'not_started';
                                                    $statusText = 'Not Started';
                                                    $statusClass = 'secondary';
                                                } elseif ($completed === $totalMappings) {
                                                    // All mappings are complete - ready for publishing
                                                    $status = 'ready';
                                                    $statusText = 'Ready';
                                                    $statusClass = 'info';
                                                } elseif ($completed > 0 || $inProgress > 0) {
                                                    // Some progress has been made
                                                    $status = 'in_progress';
                                                    $statusText = 'In Progress';
                                                    $statusClass = 'primary';
                                                } else {
                                                    // Default case
                                                    $status = 'not_started';
                                                    $statusText = 'Not Started';
                                                    $statusClass = 'secondary';
                                                }

                                                // Store program data for JavaScript
                                                $programsData[] = [
                                                    'id' => $program->id,
                                                    'name' => $program->name,
                                                    'programme_code' => $program->programme_code,
                                                    'status' => $status,
                                                    'statusText' => $statusText,
                                                    'statusClass' => $statusClass,
                                                    'timetable' => $timetable ? [
                                                        'id' => $timetable->id,
                                                        'status' => $timetable->status
                                                    ] : null,
                                                    'mappings' => [
                                                        'completed' => $completed,
                                                        'inProgress' => $inProgress,
                                                        'notStarted' => $notStarted,
                                                        'total' => $totalMappings
                                                    ]
                                                ];
                                            @endphp
                                        <tr class="program-row" data-status="{{ $status }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $program->programme_code }}:</strong> {{ $program->name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusClass }}" 
                                                      data-bs-toggle="tooltip" 
                                                      title="Scheduled: {{ $completed }} | In Progress: {{ $inProgress }} | Not Started: {{ $notStarted }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($status === 'in_progress' && !$timetable)
                                                    <a href="{{ route('admin.timetables.create', ['programme_id' => $program->id, 'academic_session_id' => $academicSession->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Create Timetable
                                                    </a>
                                                @else
                                                    <div class="btn-group" role="group">
                                                        @if($status === 'published')
                                                            <button class="btn btn-sm btn-outline-warning btn-unpublish" 
                                                                    data-timetable-id="{{ $timetable->id }}" 
                                                                    data-program-name="{{ $program->name }}"
                                                                    title="Unpublish">
                                                                <i class="bi bi-x-circle"></i> Unpublish
                                                            </button>
                                                        @elseif($status === 'ready')
                                                            <button class="btn btn-sm btn-success btn-publish" 
                                                                    data-timetable-id="{{ $timetable->id }}" 
                                                                    data-program-name="{{ $program->name }}"
                                                                    title="Publish">
                                                                <i class="bi bi-check-circle"></i> Publish
                                                            </button>
                                                        @endif
                                                        
                                                        @if($timetable)
                                                            <a href="/academic-sessions/{{ $academicSession->id }}/programmes/{{ $program->id }}/scheduling" class="btn btn-sm btn-outline-primary" title="Edit">
                                                                <i class="bi bi-pencil"></i> Edit
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    No programs are mapped to the current academic session.
                                                </div>
                                                <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="bi bi-plus-circle me-1"></i> Map Programs to This Session
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                    
                                    <!-- Store programs data for JavaScript -->
                                    @if(isset($programsData))
                                        <div id="programs-data" data-programs='@json($programsData)'></div>
                                    @endif
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                No active academic session found. Please set an active academic session first.
                                            </div>
                                            <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="bi bi-calendar-plus me-1"></i> Manage Academic Sessions
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Published Tab -->
                        <div class="tab-pane fade" id="published" role="tabpanel" aria-labelledby="published-tab">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Programme</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="published-programs">
                                        <!-- Will be populated by JavaScript -->
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    Loading published programs...
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Ready Tab -->
                        <div class="tab-pane fade" id="ready" role="tabpanel" aria-labelledby="ready-tab">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Programme</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ready-programs">
                                        <!-- Will be populated by JavaScript -->
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    Loading ready programs...
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Pending Tab -->
                        <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Programme</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pending-programs">
                                        <!-- Will be populated by JavaScript -->
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    Loading pending programs...
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Timetable Status</h5>
                </div>
                <div class="card-body d-flex flex-column" style="min-height: 300px;">
                    <div style="flex: 1; position: relative; height: 200px;">
                        <canvas id="timetableStatusChart"></canvas>
                    </div>
                    <div class="chart-legend mt-3">
                        <div class="chart-legend-item">
                            <span class="chart-legend-color bg-success"></span>
                            <span>Published</span>
                        </div>
                        <div class="chart-legend-item">
                            <span class="chart-legend-color bg-warning"></span>
                            <span>Pending/Draft</span>
                        </div>
                        <div class="chart-legend-item">
                            <span class="chart-legend-color bg-danger"></span>
                            <span>Not Assigned</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Publish/Unpublish Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                Are you sure you want to perform this action?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize programs data from PHP
const programsData = [];
const programsDataElement = document.getElementById('programs-data');
if (programsDataElement) {
    try {
        programsData.push(...JSON.parse(programsDataElement.dataset.programs));
    } catch (e) {
        console.error('Error parsing programs data:', e);
    }
}

// Function to render programs in a specific tab
function renderPrograms(containerId, filterFn) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const filteredPrograms = programsData.filter(filterFn);
    
    if (filteredPrograms.length === 0) {
        container.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-inbox me-1"></i>
                        No programs found in this category.
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    filteredPrograms.forEach((program, index) => {
        const { id, name, programme_code, status, statusText, statusClass, timetable, mappings } = program;
        
        // Generate action buttons based on status
        let actionButtons = '';
        
        if (status === 'in_progress' && !timetable) {
            actionButtons = `
                <a href="{{ route('admin.timetables.create', ['programme_id' => '${id}', 'academic_session_id' => '${$academicSession->id}']) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Create Timetable
                </a>
            `;
        } else {
            actionButtons = `
                <div class="btn-group" role="group">
                    ${status === 'published' ? `
                        <button class="btn btn-sm btn-outline-warning btn-unpublish" 
                                data-timetable-id="${timetable.id}" 
                                data-program-name="${name}"
                                title="Unpublish">
                            <i class="bi bi-x-circle"></i> Unpublish
                        </button>
                    ` : ''}
                    
                    ${status === 'ready' ? `
                        <button class="btn btn-sm btn-success btn-publish" 
                                data-timetable-id="${timetable.id}" 
                                data-program-name="${name}"
                                title="Publish">
                            <i class="bi bi-check-circle"></i> Publish
                        </button>
                    ` : ''}
                    
                    ${timetable ? `
                        <a href="/academic-sessions/{{ $academicSession->id }}/programmes/${id}/scheduling" class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    ` : ''}
                </div>
            `;
        }
        
        // Add program row HTML
        html += `
            <tr class="program-row" data-status="${status}">
                <td>${index + 1}</td>
                <td><strong>${programme_code}:</strong> ${name}</td>
                <td>
                    <span class="badge bg-${statusClass}" 
                          data-bs-toggle="tooltip" 
                          title="Scheduled: ${mappings.completed} | In Progress: ${mappings.inProgress} | Not Started: ${mappings.notStarted}">
                        ${statusText}
                    </span>
                </td>
                <td class="text-end">
                    ${actionButtons}
                </td>
            </tr>
        `;
    });
    
    container.innerHTML = html;
    
    // Reinitialize tooltips for the new elements
    const tooltipTriggerList = [].slice.call(container.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Reattach event listeners for publish/unpublish buttons
    attachPublishHandlers();
}

// Function to attach event handlers for publish/unpublish buttons
function attachPublishHandlers() {
    // Publish button handler
    document.querySelectorAll('.btn-publish').forEach(button => {
        button.addEventListener('click', function() {
            const timetableId = this.dataset.timetableId;
            const programName = this.dataset.programName;
            
            if (confirm(`Are you sure you want to publish the timetable for ${programName}?`)) {
                publishTimetable(timetableId, this);
            }
        });
    });

    // Unpublish button handler
    document.querySelectorAll('.btn-unpublish').forEach(button => {
        button.addEventListener('click', function() {
            const timetableId = this.dataset.timetableId;
            const programName = this.dataset.programName;
            
            if (confirm(`Are you sure you want to unpublish the timetable for ${programName}?`)) {
                unpublishTimetable(timetableId, this);
            }
        });
    });
}

// Initialize tabs and render programs when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initial render of all programs
    renderPrograms('programs-table-body', () => true);
    
    // Set up tab change handlers
    const tabEls = document.querySelectorAll('#timetableTabs button[data-bs-toggle="tab"]');
    tabEls.forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target').substring(1);
            
            switch(targetId) {
                case 'published':
                    renderPrograms('published-programs', p => p.status === 'published');
                    break;
                case 'ready':
                    renderPrograms('ready-programs', p => p.status === 'ready');
                    break;
                case 'pending':
                    renderPrograms('pending-programs', p => ['in_progress', 'not_started'].includes(p.status));
                    break;
                default:
                    renderPrograms('programs-table-body', () => true);
            }
        });
    });
    
    // Initial render of published programs (for the first tab that's not active)
    renderPrograms('published-programs', p => p.status === 'published');
    renderPrograms('ready-programs', p => p.status === 'ready');
    renderPrograms('pending-programs', p => ['in_progress', 'not_started'].includes(p.status));
});

document.addEventListener('DOMContentLoaded', function() {
    // Publish button handler
    document.querySelectorAll('.btn-publish').forEach(button => {
        button.addEventListener('click', function() {
            const timetableId = this.dataset.timetableId;
            const programName = this.dataset.programName;
            
            if (confirm(`Are you sure you want to publish the timetable for ${programName}?`)) {
                publishTimetable(timetableId, this);
            }
        });
    });

    // Unpublish button handler
    document.querySelectorAll('.btn-unpublish').forEach(button => {
        button.addEventListener('click', function() {
            const timetableId = this.dataset.timetableId;
            const programName = this.dataset.programName;
            
            if (confirm(`Are you sure you want to unpublish the timetable for ${programName}?`)) {
                unpublishTimetable(timetableId, this);
            }
        });
    });

    function publishTimetable(timetableId, button) {
        const url = `/admin/timetables/${timetableId}/publish`;
        const row = button.closest('tr');
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', data.message);
                
                // Reload the page to reflect changes
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to publish timetable');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', error.message || 'An error occurred while publishing the timetable.');
        });
    }

    function unpublishTimetable(timetableId, button) {
        const url = `/admin/timetables/${timetableId}/unpublish`;
        const row = button.closest('tr');
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('success', data.message);
                
                // Reload the page to reflect changes
                window.location.reload();
            } else {
                throw new Error(data.message || 'Failed to unpublish timetable');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', error.message || 'An error occurred while unpublishing the timetable.');
        });
    }

    function showAlert(type, message) {
        // Remove any existing alerts
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create and show new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at the top of the main container
        const container = document.querySelector('.container');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Enhanced Tabs Styling */
    .tabs-container {
        background: #fff;
        padding: 0.5rem 1rem 0;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        border-bottom: 2px solid #f0f0f0;
    }
    
    .nav-pills {
        --bs-nav-pills-link-active-bg: #037b90;
        --bs-nav-link-padding-x: 1.5rem;
        --bs-nav-link-padding-y: 0.75rem;
        gap: 0.25rem;
    }
    
    .nav-pills .nav-link {
        border-radius: 0;
        color: #5a5c69;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        background-color: transparent;
        position: relative;
        overflow: hidden;
        margin: 0 2px;
    }
    
    .nav-pills .nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: transparent;
        transition: all 0.2s ease;
    }
    
    .nav-pills .nav-link:hover {
        background-color: rgba(3, 123, 144, 0.1);
        transform: translateY(-1px);
    }
    
    .nav-pills .nav-link.active {
        background-color: #037b90;
        color: white;
        box-shadow: none;
        transform: none;
    }
    
    .nav-pills .nav-link.active::after {
        background: #ff7f50;
    }
    
    .nav-pills .nav-link i {
        font-size: 1.1em;
        transition: transform 0.3s ease;
    }
    
    .nav-pills .nav-link:hover i {
        transform: scale(1.1);
    }
    
    .nav-pills .nav-link.active i {
        color: white;
    }
    
    .tab-content {
        background: #fff;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        padding: 1.5rem;
        border: 1px solid #f0f0f0;
        border-top: none;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.7em;
        border-radius: 2px;
        background-color: #ff7f50;
        color: white;
    }
    
    /* Button group styling */
    .btn-group .btn {
        margin-right: 2px;
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-pills {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
        }
        
        .nav-pills .nav-link {
            white-space: nowrap;
            padding: 0.5rem 1rem;
        }
    }
</style>
@endpush

@endsection

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="statusToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Operation completed successfully!
        </div>
    </div>
</div>

@push('styles')
<style>
    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .chart-legend-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        padding: 0.25rem 0;
    }
    .chart-legend-color {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        margin-right: 10px;
        display: inline-block;
        flex-shrink: 0;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize the pie chart
    const chartContainer = document.getElementById('timetableStatusChart').parentNode;
    const canvas = document.getElementById('timetableStatusChart');
    
    // Set explicit dimensions
    const maxDimension = 1000; // Maximum dimension to prevent canvas size issues
    const containerWidth = chartContainer.offsetWidth;
    const containerHeight = 300; // Match the card body height
    
    // Set canvas dimensions
    canvas.width = Math.min(containerWidth, maxDimension);
    canvas.height = Math.min(containerHeight, maxDimension);
    
    const ctx = canvas.getContext('2d');
    const chartData = @json($chartData);
    
    // Create chart with responsive options
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: [
                    chartData.published,
                    chartData.pending,
                    chartData.not_assigned
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',  // Green for Published
                    'rgba(255, 193, 7, 0.8)',  // Yellow for Pending
                    'rgba(220, 53, 69, 0.8)'   // Red for Not Assigned
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            responsiveAnimationDuration: 0,
            maintainAspectRatio: true,
            aspectRatio: 1,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '50%',
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });

    // Handle publish/unpublish actions
    document.addEventListener('DOMContentLoaded', function() {
        // Publish/Unpublish buttons
        document.querySelectorAll('.btn-publish, .btn-unpublish').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const timetableId = this.dataset.timetableId;
                const isPublish = this.classList.contains('btn-publish');
                const action = isPublish ? 'publish' : 'unpublish';
                const programName = this.dataset.programName || 'this timetable';
                
                // Show confirmation dialog
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                const modalTitle = document.getElementById('confirmModalLabel');
                const modalBody = document.getElementById('confirmModalBody');
                const confirmBtn = document.getElementById('confirmActionBtn');
                
                modalTitle.textContent = isPublish ? 'Publish Timetable' : 'Unpublish Timetable';
                modalBody.innerHTML = isPublish 
                    ? `Are you sure you want to publish the timetable for <strong>${programName}</strong>? This will unpublish any other timetables for this academic session.`
                    : `Are you sure you want to unpublish the timetable for <strong>${programName}</strong>?`;
                
                // Set up the confirm button
                confirmBtn.textContent = isPublish ? 'Publish' : 'Unpublish';
                confirmBtn.className = isPublish ? 'btn btn-success' : 'btn btn-warning';
                confirmBtn.onclick = null; // Remove previous event listeners
                
                // Handle confirm action
                confirmBtn.addEventListener('click', function() {
                    const url = isPublish 
                        ? `{{ route('admin.timetables.publish', ['timetable' => '__ID__']) }}`.replace('__ID__', timetableId)
                        : `{{ route('admin.timetables.unpublish', ['timetable' => '__ID__']) }}`.replace('__ID__', timetableId);
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Network response was not ok');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const toast = new bootstrap.Toast(document.getElementById('successToast'));
                            const toastMessage = document.getElementById('toastMessage');
                            toastMessage.textContent = data.message;
                            toast.show();
                            
                            // Reload the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const toast = new bootstrap.Toast(document.getElementById('errorToast'));
                        const toastMessage = document.getElementById('errorToastMessage');
                        toastMessage.textContent = error.message || 'An error occurred while processing your request';
                        toast.show();
                    })
                    .finally(() => {
                        modal.hide();
                    });
                });
                
                modal.show();
            });
        });
        
        // Delete button
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const timetableId = this.dataset.timetableId;
                const programName = this.dataset.programName || 'this timetable';
                
                // Show confirmation dialog
                const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                const modalBody = document.getElementById('confirmDeleteModalBody');
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                
                modalBody.textContent = `Are you sure you want to delete the timetable for ${programName}? This action cannot be undone.`;
                
                // Set up the confirm button
                confirmBtn.onclick = null; // Remove previous event listeners
                
                // Handle confirm action
                confirmBtn.addEventListener('click', function() {
                    const url = `{{ route('admin.timetables.destroy', ['timetable' => '__ID__']) }}`.replace('__ID__', timetableId);
                    
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.message || 'Network response was not ok');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const toast = new bootstrap.Toast(document.getElementById('successToast'));
                            const toastMessage = document.getElementById('toastMessage');
                            toastMessage.textContent = data.message;
                            toast.show();
                            
                            // Reload the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const toast = new bootstrap.Toast(document.getElementById('errorToast'));
                        const toastMessage = document.getElementById('errorToastMessage');
                        toastMessage.textContent = error.message || 'An error occurred while processing your request';
                        toast.show();
                    })
                    .finally(() => {
                        modal.hide();
                    });
                });
                
                modal.show();
            });
        });
    });
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmDeleteModalBody">
                Are you sure you want to delete this timetable? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Operation completed successfully.
        </div>
    </div>
</div>

<!-- Error Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="errorToastMessage">
            An error occurred while processing your request.
        </div>
    </div>
</div>

@endpush