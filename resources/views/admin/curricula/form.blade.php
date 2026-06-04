@php
    $isEdit = isset($curriculum);
    $route = $isEdit 
        ? route('admin.academic-sessions.curricula.update', [$academicSession, $curriculum])
        : route('admin.academic-sessions.curricula.store', $academicSession);
    $method = $isEdit ? 'PUT' : 'POST';
    $title = $isEdit ? 'Edit Curriculum' : 'Create New Curriculum';
    $buttonText = $isEdit ? 'Update Curriculum' : 'Create Curriculum';
    $programmes = $programmes ?? collect();
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $title }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}">{{ $academicSession->name }}</a>
                    </li>
                    @if($isEdit)
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum]) }}">{{ $curriculum->name }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    @else
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    @endif
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ $isEdit 
                ? route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum])
                : route('admin.academic-sessions.curricula.index', $academicSession) }}" 
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to {{ $isEdit ? 'Curriculum' : 'List' }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ $route }}" method="POST">
                @csrf
                @method($method)
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Curriculum Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $curriculum->name ?? '') }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="academic_session_id" class="form-label">Academic Session</label>
                        <input type="text" 
                               class="form-control bg-light" 
                               value="{{ $academicSession->name }}" 
                               disabled>
                        <input type="hidden" name="academic_session_id" value="{{ $academicSession->id }}">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3">{{ old('description', $curriculum->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Associated Programmes</label>
                    
                    @if($programmes->isEmpty())
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No programmes available. 
                            <a href="{{ route('programmes.create') }}" target="_blank">Create a programme</a> 
                            first to associate it with this curriculum.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">
                                            <div class="form-check
                                                @if($errors->has('programmes')) 
                                                    is-invalid 
                                                @endif">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="selectAllProgrammes">
                                            </div>
                                        </th>
                                        <th>Programme</th>
                                        <th>Code</th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programmes as $programme)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input programme-checkbox" 
                                                           type="checkbox" 
                                                           name="programmes[]" 
                                                           value="{{ $programme->id }}"
                                                           id="programme{{ $programme->id }}"
                                                           {{ in_array($programme->id, old('programmes', $curriculum ? $curriculum->programmes->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="programme{{ $programme->id }}">
                                                    {{ $programme->name }}
                                                </label>
                                            </td>
                                            <td>{{ $programme->code ?? 'N/A' }}</td>
                                            <td>{{ $programme->department->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('programmes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
                
                @if($isEdit)
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $curriculum->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Set as active curriculum for this academic session
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Only one curriculum can be active per academic session. Activating this curriculum will deactivate any currently active one.
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="d-flex justify-content-between">
                    <div>
                        @if($isEdit)
                            <button type="button" 
                                    class="btn btn-outline-danger"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteCurriculumModal">
                                <i class="bi bi-trash"></i> Delete Curriculum
                            </button>
                        @endif
                    </div>
                    <div>
                        <a href="{{ $isEdit 
                            ? route('admin.academic-sessions.curricula.show', [$academicSession, $curriculum])
                            : route('admin.academic-sessions.curricula.index', $academicSession) }}" 
                           class="btn btn-outline-secondary me-2">
                            <i class="bi bi-x-lg"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ $buttonText }}
                        </button>
                    </div>
                </div>
            </form>
            
            @if($isEdit)
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteCurriculumModal" tabindex="-1" 
                     aria-labelledby="deleteCurriculumModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteCurriculumModalLabel">Delete Curriculum</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete the curriculum <strong>{{ $curriculum->name }}</strong>?</p>
                                
                                @if($curriculum->courseUnitProgrammeMappings()->exists())
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        This curriculum contains {{ $curriculum->courseUnitProgrammeMappings()->count() }} course unit mappings that will also be deleted.
                                    </div>
                                @endif
                                
                                @if($curriculum->is_active)
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        This is the active curriculum. You must set another curriculum as active before deleting this one.
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('admin.academic-sessions.curricula.destroy', [$academicSession, $curriculum]) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            {{ $curriculum->is_active ? 'disabled' : '' }}
                                            onclick="return confirm('Are you sure you want to delete this curriculum? This action cannot be undone.')">
                                        <i class="bi bi-trash me-1"></i> Delete Curriculum
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select/Deselect all programmes
    const selectAllCheckbox = document.getElementById('selectAllProgrammes');
    const programmeCheckboxes = document.querySelectorAll('.programme-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            programmeCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Update "Select All" checkbox when individual checkboxes change
        programmeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(programmeCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
        
        // Initialize "Select All" state on page load
        const allCheckedOnLoad = Array.from(programmeCheckboxes).every(cb => cb.checked);
        selectAllCheckbox.checked = allCheckedOnLoad;
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(event) {
            // Client-side validation can be added here if needed
            // For example, ensuring at least one programme is selected
            const checkedProgrammes = document.querySelectorAll('.programme-checkbox:checked');
            if (checkedProgrammes.length === 0) {
                event.preventDefault();
                alert('Please select at least one programme for this curriculum.');
                return false;
            }
            
            return true;
        });
    }
});
</script>
@endpush
@endsection
