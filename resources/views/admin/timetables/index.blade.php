@extends('layouts.admin')

@section('title', 'Manage Timetables')

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
}
.content-wrapper { background:transparent!important; box-shadow:none!important; padding:1.8rem 2rem!important; }
.page-fade-in { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) both; }
.stagger-1    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .05s both; }
.stagger-2    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .12s both; }
.stagger-3    { animation:fadeIn .4s cubic-bezier(.34,1.56,.64,1) .20s both; }
@keyframes fadeIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

/* Page Header */
.page-header{display:flex;align-items:center;gap:1rem;margin-bottom:1.75rem;justify-content:space-between;}
.header-icon{width:48px;height:48px;background:var(--teal-bg);color:var(--teal);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.3rem}
.header-title{font-size:1.4rem;font-weight:700;color:var(--slate-900);margin:0;display:flex;align-items:center;gap:.75rem}
.session-pill{font-size:.65rem;font-weight:700;padding:.3rem .6rem;border-radius:100px;background:var(--green);color:#fff;display:inline-flex;align-items:center;gap:.3rem;text-transform:uppercase;letter-spacing:.5px}

/* Info Card */
.info-card { background:#fff; border-radius:var(--radius-md); box-shadow:var(--card-shadow); padding:0; margin-bottom:1.5rem; overflow:hidden;}

/* Action Buttons */
.btn-outline-soft { background:#fff; color:var(--slate-700); border:1.5px solid var(--slate-200); padding:.5rem .9rem; font-size:.85rem; font-weight:600; border-radius:var(--radius-sm); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
.btn-outline-soft:hover { border-color:var(--teal); color:var(--teal); background:var(--teal-bg); }
.btn-premium { background:var(--teal); color:#fff; border:none; padding:.5rem .9rem; font-size:.85rem; font-weight:600; border-radius:var(--radius-sm); cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.4rem; text-decoration:none; }
.btn-premium:hover { background:var(--teal-dark); color:#fff; }

/* Custom Tabs */
.custom-tabs { display:flex; gap:1.5rem; border-bottom:1.5px solid var(--slate-200); padding:0 1.5rem; background:#fff; border-radius:var(--radius-md) var(--radius-md) 0 0; }
.custom-tab { background:none; border:none; padding:1rem 0; font-size:.875rem; font-weight:600; color:var(--slate-500); cursor:pointer; position:relative; transition:color .2s; display:flex; align-items:center; gap:.4rem; }
.custom-tab:hover { color:var(--slate-900); }
.custom-tab.active { color:var(--teal); }
.custom-tab.active::after { content:''; position:absolute; bottom:-1.5px; left:0; width:100%; height:3px; background:var(--teal); border-radius:3px 3px 0 0; }
.tab-badge { background:var(--slate-100); color:var(--slate-700); padding:.1rem .4rem; border-radius:100px; font-size:.65rem; margin-left:.2rem; }
.custom-tab.active .tab-badge { background:var(--teal-bg); color:var(--teal); }

.chart-card { background:#fff; border-radius:var(--radius-md); box-shadow:var(--card-shadow); padding:1.5rem; height:100%; }
.chart-header { font-size:1rem; font-weight:700; color:var(--slate-900); margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; }
</style>
@endpush

@section('content')
<div class="content-wrapper page-fade-in">
    <div class="page-header stagger-1">
        <div class="d-flex align-items-center gap-3">
            <div class="header-icon"><i class="bi bi-calendar3"></i></div>
            <div>
                <h1 class="header-title">
                    @if($academicSession)
                        Timetables - {{ $academicSession->name }}
                        <span class="session-pill"><i class="bi bi-star-fill"></i> Active</span>
                    @else
                        Timetables
                    @endif
                </h1>
                <p style="font-size:.9rem;color:var(--slate-500);margin:.2rem 0 0;">Manage timetable publication status across programmes.</p>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            @if(auth()->user()->hasRole('timetabler') && auth()->user()->school)
                <div style="font-size:.85rem;color:var(--slate-500);font-weight:500;">
                    <i class="bi bi-building"></i> {{ auth()->user()->school->name }}
                </div>
            @endif
        </div>
    </div>
    
    @if(!$academicSession)
        <div class="alert alert-warning stagger-1" style="border:none;border-left:4px solid #f59e0b;background:rgba(245,158,11,.1);color:#b45309;border-radius:var(--radius-sm);margin-bottom:1.5rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No active academic session found. Please set an academic session as active to view timetables.
        </div>
    @endif

    <div class="row stagger-2">
        <!-- Programs List -->
        <div class="col-lg-8">
            <div class="info-card">
                <!-- Enhanced Tabs Navigation -->
                <div class="custom-tabs" role="tablist">
                    <button class="custom-tab active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        <i class="bi bi-grid-3x3-gap-fill"></i> All
                        <span class="tab-badge">{{ count($programs) }}</span>
                    </button>
                    <button class="custom-tab" id="published-tab" data-bs-toggle="tab" data-bs-target="#published" type="button" role="tab">
                        <i class="bi bi-check-circle-fill"></i> Published
                        <span class="tab-badge">{{ $publishedPrograms->count() }}</span>
                    </button>
                    <button class="custom-tab" id="ready-tab" data-bs-toggle="tab" data-bs-target="#ready" type="button" role="tab">
                        <i class="bi bi-check2-all"></i> Ready
                        <span class="tab-badge">{{ $readyPrograms->count() }}</span>
                    </button>
                    <button class="custom-tab" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="bi bi-hourglass-split"></i> Pending
                        <span class="tab-badge">{{ $pendingPrograms->count() }}</span>
                    </button>
                </div>
                
                <!-- Tab Content -->
                <div class="tab-content" id="timetableTabsContent">
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table mb-0" style="width:100%;border-collapse:collapse;">
                                <thead style="background:var(--slate-50);border-bottom:1px solid var(--slate-200);">
                                    <tr>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;width:50px;">#</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">Programme Details</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:center;">Status</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($programs as $program)
                                        @include('admin.timetables.partials.program-row', ['program' => $program])
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5" style="border:none;">
                                                <div style="font-size:2.5rem;color:var(--slate-200);margin-bottom:1rem;"><i class="bi bi-grid-3x3-gap"></i></div>
                                                <h5 style="color:var(--slate-700);font-weight:600;font-size:1rem;margin-bottom:.5rem;">No Programs Found</h5>
                                                <p style="color:var(--slate-500);font-size:.85rem;margin:0;">There are no programs to display.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Published Tab -->
                    <div class="tab-pane fade" id="published" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table mb-0" style="width:100%;border-collapse:collapse;">
                                <thead style="background:var(--slate-50);border-bottom:1px solid var(--slate-200);">
                                    <tr>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;width:50px;">#</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">Programme Details</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:center;">Status</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($publishedPrograms as $program)
                                        @include('admin.timetables.partials.program-row', ['program' => $program])
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5" style="border:none;">
                                                <div style="font-size:2.5rem;color:var(--slate-200);margin-bottom:1rem;"><i class="bi bi-check-circle"></i></div>
                                                <h5 style="color:var(--slate-700);font-weight:600;font-size:1rem;margin-bottom:.5rem;">No Published Programs</h5>
                                                <p style="color:var(--slate-500);font-size:.85rem;margin:0;">No timetables have been published yet.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Ready Tab -->
                    <div class="tab-pane fade" id="ready" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table mb-0" style="width:100%;border-collapse:collapse;">
                                <thead style="background:var(--slate-50);border-bottom:1px solid var(--slate-200);">
                                    <tr>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;width:50px;">#</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">Programme Details</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:center;">Status</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($readyPrograms as $program)
                                        @include('admin.timetables.partials.program-row', ['program' => $program])
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5" style="border:none;">
                                                <div style="font-size:2.5rem;color:var(--slate-200);margin-bottom:1rem;"><i class="bi bi-check2-all"></i></div>
                                                <h5 style="color:var(--slate-700);font-weight:600;font-size:1rem;margin-bottom:.5rem;">No Ready Programs</h5>
                                                <p style="color:var(--slate-500);font-size:.85rem;margin:0;">No timetables are fully completed and ready for publishing.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pending Tab -->
                    <div class="tab-pane fade" id="pending" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table premium-table mb-0" style="width:100%;border-collapse:collapse;">
                                <thead style="background:var(--slate-50);border-bottom:1px solid var(--slate-200);">
                                    <tr>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;width:50px;">#</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;">Programme Details</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:center;">Status</th>
                                        <th style="padding:1rem 1.5rem;font-size:.75rem;font-weight:700;color:var(--slate-500);text-transform:uppercase;letter-spacing:1px;border:none;text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingPrograms as $program)
                                        @include('admin.timetables.partials.program-row', ['program' => $program])
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5" style="border:none;">
                                                <div style="font-size:2.5rem;color:var(--slate-200);margin-bottom:1rem;"><i class="bi bi-hourglass-split"></i></div>
                                                <h5 style="color:var(--slate-700);font-weight:600;font-size:1rem;margin-bottom:.5rem;">No Pending Programs</h5>
                                                <p style="color:var(--slate-500);font-size:.85rem;margin:0;">All programs are either ready, published, or not started.</p>
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

        <!-- Chart -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <span>Timetable Status</span>
                    <i class="bi bi-pie-chart" style="color:var(--slate-400);"></i>
                </div>
                <div class="d-flex flex-column justify-content-center" style="height:calc(100% - 3rem);">
                    <div style="position: relative; width:100%; height: 220px; display:flex; justify-content:center;">
                        <canvas id="timetableStatusChart"></canvas>
                    </div>
                    <div class="chart-legend mt-4" style="padding:0 1rem;">
                        <div class="chart-legend-item d-flex align-items-center mb-2">
                            <span class="chart-legend-color" style="background:var(--green);width:12px;height:12px;border-radius:3px;margin-right:10px;"></span>
                            <span style="font-size:.85rem;color:var(--slate-700);font-weight:500;">Published</span>
                        </div>
                        <div class="chart-legend-item d-flex align-items-center mb-2">
                            <span class="chart-legend-color" style="background:#f59e0b;width:12px;height:12px;border-radius:3px;margin-right:10px;"></span>
                            <span style="font-size:.85rem;color:var(--slate-700);font-weight:500;">Pending/Draft</span>
                        </div>
                        <div class="chart-legend-item d-flex align-items-center">
                            <span class="chart-legend-color" style="background:var(--coral);width:12px;height:12px;border-radius:3px;margin-right:10px;"></span>
                            <span style="font-size:.85rem;color:var(--slate-700);font-weight:500;">Not Assigned</span>
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