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
                    <span class="badge bg-light text-dark">{{ count($programs) }} Programs</span>
                </div>
                <div class="card-body p-0">
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
                            <tbody>
                                @if($academicSession)
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
                                        @endphp
                                        <tr>
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
    .btn-group .btn {
        margin-right: 2px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
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