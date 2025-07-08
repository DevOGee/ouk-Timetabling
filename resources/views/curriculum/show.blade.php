@extends('layouts.app')

@section('title', 'Curriculum for ' . $programme->name)

@push('styles')
<style>
    .mapping-card {
        transition: all 0.3s ease;
        border-left: 4px solid #0d6efd;
    }
    .mapping-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .year-header {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 10px 15px;
        margin: 20px 0 10px 0;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('curriculum.index') }}">Curriculum</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $programme->name }}</li>
                </ol>
            </nav>
            <h2 class="mb-0">{{ $programme->name }} <small class="text-muted">{{ $programme->code }}</small></h2>
            <div class="text-muted">
                <span class="badge bg-primary">{{ $activeSession->name }}</span>
                <span class="ms-2">{{ $activeCurriculum->name }}</span>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="bi bi-plus-lg"></i> Add Course Unit
            </button>
            <a href="{{ route('curriculum.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Programmes
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> 
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Years of Study Tabs -->
    <ul class="nav nav-tabs mb-4" id="yearTabs" role="tablist">
        @foreach($mappedCourseUnits as $yearId => $yearMappings)
            @php $year = $years->find($yearId); @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                        id="year-{{ $yearId }}-tab" 
                        data-bs-toggle="tab" 
                        data-bs-target="#year-{{ $yearId }}" 
                        type="button" 
                        role="tab" 
                        aria-controls="year-{{ $yearId }}" 
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    {{ $year->name ?? 'Year ' . $yearId }}
                    <span class="badge bg-primary rounded-pill ms-1">{{ count($yearMappings) }}</span>
                </button>
            </li>
        @endforeach
        
        @if(count($mappedCourseUnits) === 0)
            <li class="nav-item">
                <span class="nav-link text-muted">No course units mapped yet</span>
            </li>
        @endif
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="yearTabsContent">
        @forelse($mappedCourseUnits as $yearId => $yearMappings)
            @php $year = $years->find($yearId); @endphp
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                 id="year-{{ $yearId }}" 
                 role="tabpanel" 
                 aria-labelledby="year-{{ $yearId }}-tab">
                 
                <div class="row">
                    <!-- Semester 1 -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Semester 1</h5>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $semesterMappings = $yearMappings->filter(function($mapping) {
                                        return $mapping->semester_id == 1; // Assuming 1 is the ID for Semester 1
                                    });
                                @endphp
                                
                                @if($semesterMappings->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($semesterMappings as $mapping)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $mapping->courseUnit->code }} - {{ $mapping->courseUnit->name }}</h6>
                                                        @if($mapping->lecturer)
                                                            <small class="text-muted">
                                                                <i class="bi bi-person"></i> {{ $mapping->lecturer->name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary edit-mapping" 
                                                                data-mapping-id="{{ $mapping->id }}"
                                                                data-course-unit-id="{{ $mapping->course_unit_id }}"
                                                                data-year-id="{{ $mapping->year_of_study_id }}"
                                                                data-semester-id="{{ $mapping->semester_id }}"
                                                                data-lecturer-id="{{ $mapping->lecturer_id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger delete-mapping" 
                                                                data-mapping-id="{{ $mapping->id }}"
                                                                data-course-name="{{ $mapping->courseUnit->code }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 text-center text-muted">
                                        <i class="bi bi-info-circle"></i> No course units mapped to this semester
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Semester 2 -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Semester 2</h5>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $semesterMappings = $yearMappings->filter(function($mapping) {
                                        return $mapping->semester_id == 2; // Assuming 2 is the ID for Semester 2
                                    });
                                @endphp
                                
                                @if($semesterMappings->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($semesterMappings as $mapping)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $mapping->courseUnit->code }} - {{ $mapping->courseUnit->name }}</h6>
                                                        @if($mapping->lecturer)
                                                            <small class="text-muted">
                                                                <i class="bi bi-person"></i> {{ $mapping->lecturer->name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-outline-primary edit-mapping" 
                                                                data-mapping-id="{{ $mapping->id }}"
                                                                data-course-unit-id="{{ $mapping->course_unit_id }}"
                                                                data-year-id="{{ $mapping->year_of_study_id }}"
                                                                data-semester-id="{{ $mapping->semester_id }}"
                                                                data-lecturer-id="{{ $mapping->lecturer_id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger delete-mapping" 
                                                                data-mapping-id="{{ $mapping->id }}"
                                                                data-course-name="{{ $mapping->courseUnit->code }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 text-center text-muted">
                                        <i class="bi bi-info-circle"></i> No course units mapped to this semester
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No course units have been mapped to this programme yet.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Add Course Unit Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addCourseForm" action="{{ route('curriculum.mapping.store', $programme) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCourseModalLabel">Add Course Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="course_unit_id" class="form-label">Course Unit</label>
                            <select class="form-select" id="course_unit_id" name="course_unit_id" required>
                                <option value="">Select a course unit</option>
                                @foreach($availableCourseUnits as $courseUnit)
                                    <option value="{{ $courseUnit->id }}">
                                        {{ $courseUnit->code }} - {{ $courseUnit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year_of_study_id" class="form-label">Year of Study</label>
                                    <select class="form-select" id="year_of_study_id" name="year_of_study_id" required>
                                        <option value="">Select year</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester_id" class="form-label">Semester</label>
                                    <select class="form-select" id="semester_id" name="semester_id" required>
                                        <option value="">Select semester</option>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="lecturer_id" class="form-label">Lecturer (Optional)</label>
                            <select class="form-select" id="lecturer_id" name="lecturer_id">
                                <option value="">Select a lecturer (optional)</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}">
                                        {{ $lecturer->name }} ({{ $lecturer->code ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Course Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong id="courseToDelete"></strong> from this programme?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize select2 for better select inputs
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Handle delete button click
        document.querySelectorAll('.delete-mapping').forEach(button => {
            button.addEventListener('click', function() {
                const courseName = this.getAttribute('data-course-name');
                const mappingId = this.getAttribute('data-mapping-id');
                
                document.getElementById('courseToDelete').textContent = courseName;
                document.getElementById('deleteForm').action = `/curriculum/mapping/${mappingId}`;
                
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                modal.show();
            });
        });
        
        // Handle edit button click
        document.querySelectorAll('.edit-mapping').forEach(button => {
            button.addEventListener('click', function() {
                const mappingId = this.getAttribute('data-mapping-id');
                const courseUnitId = this.getAttribute('data-course-unit-id');
                const yearId = this.getAttribute('data-year-id');
                const semesterId = this.getAttribute('data-semester-id');
                const lecturerId = this.getAttribute('data-lecturer-id');
                
                // Set form values
                document.getElementById('course_unit_id').value = courseUnitId;
                document.getElementById('year_of_study_id').value = yearId;
                document.getElementById('semester_id').value = semesterId;
                if (lecturerId) {
                    document.getElementById('lecturer_id').value = lecturerId;
                }
                
                // Change form action to update
                const form = document.getElementById('addCourseForm');
                form.action = `/curriculum/mapping/${mappingId}`;
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
                
                // Update modal title and button text
                document.getElementById('addCourseModalLabel').textContent = 'Edit Course Mapping';
                document.querySelector('#addCourseForm button[type="submit"]').textContent = 'Update Mapping';
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('addCourseModal'));
                modal.show();
            });
        });
        
        // Reset form when modal is hidden
        document.getElementById('addCourseModal').addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('addCourseForm');
            form.reset();
            form.action = '{{ route("curriculum.mapping.store", $programme) }}';
            document.getElementById('addCourseModalLabel').textContent = 'Add Course Unit';
            document.querySelector('#addCourseForm button[type="submit"]').textContent = 'Add Course Unit';
            
            // Remove any existing _method input
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
        });
        
        // Handle form submission
        document.getElementById('addCourseForm').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        });
    });
</script>
@endpush

@endsection
                    <button type="submit" class="btn btn-primary w-100">
                        Add Mapping
                    </button>
                </div>
            </div>
        </form>




        <h5 class="mt-4">Mapped Course Units</h5>

        @forelse($groupedMappings as $group => $mappings)
            <h4 class="text-primary">{{ $group }}</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mappings->sortBy(fn($m) => $m->courseUnit->code) as $mapping)
                        <tr>
                            <td>
                                <!-- Color Circle -->
                                <span
                                    style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; 
                            background-color: {{ $mapping->courseUnit->color ?? '#000000' }}; margin-right: 10px;">
                                </span>
                                {{ $mapping->courseUnit->code }} - {{ $mapping->courseUnit->name }}
                            </td>
                            <td>
                                <form action="{{ route('curriculum.unmap', $mapping->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @empty
            <p>No course units mapped yet.</p>
        @endforelse

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('curriculum.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Curriculum
            </a>
            <a href="{{ route('programmes.show', $programme->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-week"></i> View Scheduling
            </a>
        </div>
    </div>
@endsection
