@extends('layouts.app')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Edit User: {{ $user->name }}</h2>
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to User
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Left Column: Personal Information -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-lines-fill text-primary me-2"></i>Personal Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="title_id" class="form-label">Title <span class="text-danger">*</span></label>
                                        <select class="form-select @error('title_id') is-invalid @enderror" id="title_id" name="title_id" required>
                                            <option value="">Select Title</option>
                                            @foreach($titles as $title)
                                                <option value="{{ $title->id }}" {{ old('title_id', $user->title_id) == $title->id ? 'selected' : '' }}>
                                                    {{ $title->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('title_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-9">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments->groupBy('school.name') as $schoolName => $deptGroup)
                                        <optgroup label="{{ $schoolName }}">
                                            @foreach($deptGroup as $department)
                                                <option value="{{ $department->id }}" {{ (old('department_id') ?? $user->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                                    
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield-lock text-primary me-2"></i>Change Password
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> Leave password fields blank to keep current password
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Minimum 8 characters</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Roles & Profile -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-badge text-primary me-2"></i>Roles & Permissions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Assign Roles <span class="text-danger">*</span></label>
                                    @error('roles')
                                        <div class="alert alert-danger py-2">{{ $message }}</div>
                                    @enderror
                                    
                                    @foreach($roles as $role)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input @error('roles') is-invalid @enderror" 
                                                   type="checkbox" 
                                                   name="roles[]" 
                                                   value="{{ $role->id }}" 
                                                   id="role-{{ $role->id }}"
                                                   {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role-{{ $role->id }}">
                                                {{ ucfirst($role->name) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-image text-primary me-2"></i>Profile Picture
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <div class="position-relative d-inline-block">
                                        <img id="image-preview" 
                                             src="{{ $user->image_path ? asset('storage/' . $user->image_path) : asset('images/default-avatar.png') }}" 
                                             class="rounded-circle border border-3 border-light" 
                                             style="width: 150px; height: 150px; object-fit: cover;"
                                             alt="Profile Preview">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image"
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Recommended size: 300x300 pixels</small>
                                    
                                    @if($user->image_path)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                            <label class="form-check-label text-danger" for="remove_image">
                                                Remove current image
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background-color: #f8f9fa;
    }
    
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
    
    #image-preview {
        transition: all 0.3s ease;
    }
    
    #image-preview:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
    // Image preview function
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onloadend = function() {
            preview.src = reader.result;
            
            // Show remove image checkbox when a new image is selected
            const removeCheckbox = document.getElementById('remove_image');
            if (removeCheckbox) {
                removeCheckbox.checked = false;
            }
        }
        
        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ $user->image_path ? asset('storage/' . $user->image_path) : asset('images/default-avatar.png') }}";
        }
    }
    
    // Handle remove image checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const removeCheckbox = document.getElementById('remove_image');
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const defaultImage = "{{ asset('images/default-avatar.png') }}";
        
        if (removeCheckbox) {
            removeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    preview.src = defaultImage;
                    if (imageInput) imageInput.value = '';
                } else {
                    preview.src = "{{ $user->image_path ? asset('storage/' . $user->image_path) : asset('images/default-avatar.png') }}";
                }
            });
        }
    });
</script>
@endpush
@endsection