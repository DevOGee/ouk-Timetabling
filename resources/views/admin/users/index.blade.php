@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid px-4">
    <!-- Import Report -->
    @include('admin.users.partials.import-report')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">User Management</h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                <i class="bi bi-upload me-1"></i> Bulk Upload Users
            </button>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add New User
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search by name or email...">
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments->groupBy('school.name') as $schoolName => $deptGroup)
                            <optgroup label="{{ $schoolName }}">
                                @foreach($deptGroup as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center p-5">
                    <div class="text-muted mb-3">
                        <i class="bi bi-people" style="font-size: 3rem;"></i>
                    </div>
                    <h5>No users found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'role']))
                            Try adjusting your search or filter criteria
                        @else
                            No users have been added yet
                        @endif
                    </p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New User
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                @if ($user->image_path)
                                                    <img src="{{ asset('storage/' . $user->image_path) }}" 
                                                         class="rounded-circle" 
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         alt="{{ $user->name }}">
                                                @else
                                                    <img src="https://ouk.ac.ke/sites/default/files/Facilitators/alt.png"
                                                         class="rounded-circle"
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         alt="Default Image">
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">{{ $user->name }}</div>
                                                <small class="text-muted">{{ optional($user->title)->abbreviation ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary bg-opacity-10 text-primary mb-1">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip" 
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-sm btn-outline-secondary"
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to ' + ({{ $user->status === 'active' ? "'deactivate'" : "'activate'"}}) + ' this user?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-{{ $user->status === 'active' ? 'outline-danger' : 'outline-success' }}"
                                                        data-bs-toggle="tooltip" 
                                                        title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                    <i class="bi bi-person-{{ $user->status === 'active' ? 'x' : 'check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-center">
                        <nav>
                            {{ $users->appends(request()->except('page'))->links() }}
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
    
    .table td {
        vertical-align: middle;
    }
    

</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role" class="form-label">Assign Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Download the <a href="{{ asset('templates/users_import_template.csv') }}" download>CSV template</a> for the correct format.
                            Required columns: name, email, title
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="send_welcome_email" name="send_welcome_email">
                        <label class="form-check-label" for="send_welcome_email">Send welcome email to users</label>
                    </div>
                    
                    <div id="preview" class="d-none">
                        <h6>Preview (first 5 rows)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead id="preview-head"></thead>
                                <tbody id="preview-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">Upload Users</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('csv_file');
        const preview = document.getElementById('preview');
        const previewHead = document.getElementById('preview-head');
        const previewBody = document.getElementById('preview-body');
        const form = document.getElementById('bulkUploadForm');
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const text = e.target.result;
                const rows = text.split('\n').filter(row => row.trim() !== '');
                
                if (rows.length < 2) {
                    alert('CSV file must have at least one data row');
                    return;
                }
                
                // Parse headers
                const headers = rows[0].split(',').map(h => h.trim().replace(/^"|"$/g, ''));
                
                // Display headers
                previewHead.innerHTML = '<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>';
                
                // Display first 5 rows
                const rowCount = Math.min(5, rows.length - 1);
                let bodyHtml = '';
                
                for (let i = 1; i <= rowCount; i++) {
                    const cells = rows[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
                    bodyHtml += '<tr>' + cells.map(c => `<td>${c}</td>`).join('') + '</tr>';
                }
                
                previewBody.innerHTML = bodyHtml;
                preview.classList.remove('d-none');
            };
            
            reader.readAsText(file);
        });
        
        // Form submission handling
        form.addEventListener('submit', function(e) {
            const file = fileInput.files[0];
            if (!file) {
                e.preventDefault();
                alert('Please select a CSV file');
                return false;
            }
            
            const uploadBtn = document.getElementById('uploadBtn');
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
            
            return true;
        });
    });
</script>
@endpush

@endsection
