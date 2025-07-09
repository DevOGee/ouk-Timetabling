@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Edit Profile</h2>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
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
                                    
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                    
                    <!-- Right Column: Profile Picture -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-image text-primary me-2"></i>Profile Picture
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    @if ($user->image_path)
                                        <img src="{{ asset('storage/' . $user->image_path) }}" 
                                             class="rounded-circle border border-4 border-primary" 
                                             style="width: 150px; height: 150px; object-fit: cover;"
                                             id="imagePreview"
                                             alt="{{ $user->name }}">
                                    @else
                                        <img src="https://ouk.ac.ke/sites/default/files/Facilitators/alt.png"
                                             class="rounded-circle border border-4 border-primary"
                                             style="width: 150px; height: 150px; object-fit: cover;"
                                             id="imagePreview"
                                             alt="Default Image">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Max size: 2MB. Allowed: JPG, PNG, GIF</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Password -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield-lock text-primary me-2"></i>Security
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Enter your current password to save changes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onloadend = function() {
            preview.src = reader.result;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "{{ $user->image_path ? asset('storage/' . $user->image_path) : 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png' }}";
        }
    }
</script>
@endpush

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.25rem;
        border-radius: 10px 10px 0 0 !important;
    }
    
    .card-header h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
        color: #2c3e50;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-select, .form-control {
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    
    .btn i {
        margin-right: 0.5rem;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
    
    .border-primary {
        border-color: #0d6efd !important;
    }
    
    .bg-primary {
        background-color: #0d6efd !important;
    }
    
    .form-text {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
@endpush
@endsection
