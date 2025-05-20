@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit User Roles - {{ $user->name }}</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('roles.update', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="school_id" class="form-label">School</label>
                            <select name="school_id" id="school_id" class="form-select">
                                <option value="">Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ $user->school_id == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Required for Deans, School Timetablers, and Instructors
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Roles</label>
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                name="roles[]" 
                                                value="{{ $role->name }}"
                                                id="role_{{ $role->id }}"
                                                {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                                                data-role="{{ $role->name }}">
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                                @if($role->name == 'dean' || $role->name == 'school_timetabler' || $role->name == 'instructor')
                                                    <span class="text-danger">(Requires School Assignment)</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="school-section" class="mb-3" style="display: none;">
                            <label for="school_id" class="form-label">School</label>
                            <select name="school_id" id="school_id" class="form-select">
                                <option value="">Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ $user->school_id == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Required for Deans, School Timetablers, and Instructors
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Get all role checkboxes
    const roleCheckboxes = document.querySelectorAll('input[type="checkbox"][data-role]');
    const schoolSection = document.getElementById('school-section');
    const schoolSelect = document.getElementById('school_id');

    // Function to check if any school-required role is selected
    function hasSchoolRequiredRole() {
        return roleCheckboxes.some(checkbox => {
            const role = checkbox.getAttribute('data-role');
            return checkbox.checked && 
                (role === 'dean' || role === 'school_timetabler' || role === 'instructor');
        });
    }

    // Function to update school section visibility
    function updateSchoolSection() {
        if (hasSchoolRequiredRole()) {
            schoolSection.style.display = 'block';
            schoolSelect.required = true;
        } else {
            schoolSection.style.display = 'none';
            schoolSelect.required = false;
        }
    }

    // Initialize based on current selections
    updateSchoolSection();

    // Add change event listeners to all role checkboxes
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSchoolSection);
    });
</script>
@endpush
@endsection
