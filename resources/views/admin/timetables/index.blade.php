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

    <div class="card-header text-secondary">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Programs</h5>
            <span class="badge bg-light text-dark"><span id="program-count">{{ count($programs) }}</span> Programs</span>
        </div>
        @if(auth()->user()->hasRole('timetabler') && auth()->user()->school)
            <div class="small">
                <i class="bi bi-building me-1"></i> School: {{ auth()->user()->school->name }}
            </div>
        @endif
    </div>

    <div class="row">
        <!-- Programs List -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body p-0">
                    <!-- Enhanced Tabs Navigation -->
                    <div class="tabs-container">
                        <ul class="nav nav-tabs mb-0" id="timetableTabs" role="tablist">
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
                                    <span class="badge rounded-pill bg-success ms-2">{{ $publishedPrograms->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center justify-content-center" id="ready-tab" data-bs-toggle="tab" data-bs-target="#ready" type="button" role="tab">
                                    <i class="bi bi-check2-all me-2"></i>
                                    <span>Ready</span>
                                    <span class="badge rounded-pill bg-info ms-2">{{ $readyPrograms->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center justify-content-center" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                                    <i class="bi bi-hourglass-split me-2"></i>
                                    <span>Pending</span>
                                    <span class="badge rounded-pill bg-warning ms-2">{{ $pendingPrograms->count() }}</span>
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
                                        @forelse($programs as $program)
                                            @include('admin.timetables.partials.program-row', ['program' => $program])
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-info-circle me-1"></i>
                                                        No programs found.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
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
                                        @forelse($publishedPrograms as $program)
                                            @include('admin.timetables.partials.program-row', ['program' => $program])
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-inbox me-1"></i>
                                                        No published programs found.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
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
                                        @forelse($readyPrograms as $program)
                                            @include('admin.timetables.partials.program-row', ['program' => $program])
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-inbox me-1"></i>
                                                        No ready programs found.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
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
                                        @forelse($pendingPrograms as $program)
                                            @include('admin.timetables.partials.program-row', ['program' => $program])
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-inbox me-1"></i>
                                                        No pending programs found.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
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
    // Legacy client-side rendering logic removed in favor of server-side rendering
    


// Global functions for timetable actions
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
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 5000);
}

    function publishTimetable(timetableId, button) {
        // Show loading state
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Publishing...';
        button.disabled = true;

        const url = `/admin/timetables/${timetableId}/publish`;
        
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
                showAlert('success', data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to publish timetable');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', error.message || 'An error occurred while publishing the timetable.');
            // Reset button
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    function unpublishTimetable(timetableId, button) {
        // Show loading state
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Unpublishing...';
        button.disabled = true;

        const url = `/admin/timetables/${timetableId}/unpublish`;
        
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
                showAlert('success', data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to unpublish timetable');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', error.message || 'An error occurred while unpublishing the timetable.');
            // Reset button
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
</script>
@endpush

@push('styles')
{{-- Additional styles will be pushed here --}}
<style>
    /* Modern Tabs Styling */
    .tabs-container {
        padding: 0 1rem;
        background: #fff;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .nav-tabs {
        border-bottom: none;
        gap: 1rem;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #858796;
        font-weight: 600;
        padding: 1rem 0.5rem;
        transition: all 0.2s ease;
        background: transparent;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #4e73df;
    }
    
    .nav-tabs .nav-link.active {
        color: #4e73df;
        background: transparent;
        border-bottom-color: #4e73df;
    }
    
    .nav-tabs .nav-link i {
        margin-right: 0.5rem;
    }

    .nav-tabs .badge {
        font-size: 0.7rem;
        padding: 0.35em 0.6em;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.7em;
        border-radius: 2px;
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
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
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

    // Handle publish/unpublish actions via delegation (since buttons are dynamic)
    document.addEventListener('click', function(e) {
        // Handle publish button clicks
        const publishBtn = e.target.closest('.btn-publish');
        if (publishBtn) {
            e.preventDefault();
            const timetableId = publishBtn.dataset.timetableId;
            const programName = publishBtn.dataset.programName;
            
            if (confirm(`Are you sure you want to publish the timetable for ${programName}?`)) {
                publishTimetable(timetableId, publishBtn);
            }
            return;
        }
        
        // Handle unpublish button clicks
        const unpublishBtn = e.target.closest('.btn-unpublish');
        if (unpublishBtn) {
            e.preventDefault();
            const timetableId = unpublishBtn.dataset.timetableId;
            const programName = unpublishBtn.dataset.programName;
            
            if (confirm(`Are you sure you want to unpublish the timetable for ${programName}?`)) {
                unpublishTimetable(timetableId, unpublishBtn);
            }
            return;
        }
    });

    // Handle delete button
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const timetableId = this.dataset.timetableId;
            const programName = this.dataset.programName || 'this timetable';
            
            // Show confirmation dialog
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            const modalBody = document.getElementById('confirmDeleteModalBody');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            
            // Update modal content
            modalBody.textContent = `Are you sure you want to delete the timetable for ${programName}? This action cannot be undone.`;
            
            // Remove previous event listeners
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
            
            // Add click handler for the confirm button
            newConfirmBtn.onclick = function() {
                const url = `{{ route('admin.timetables.destroy', ['timetable' => '__ID__']) }}`.replace('__ID__', timetableId);
                
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
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
            };
            
            modal.show();
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