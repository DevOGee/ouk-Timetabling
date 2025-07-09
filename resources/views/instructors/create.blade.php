@extends('layouts.app')

@section('title', 'Add Lecturer')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Add New Lecturer</h2>
            <a href="{{ route('instructors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">Please fix the following errors</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('instructors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Personal Information</h5>
                            
                            <div class="mb-3">
                                <label for="title_id" class="form-label">Title <span class="text-danger">*</span></label>
                                <select class="form-select" id="title_id" name="title_id" required>
                                    <option value="" disabled selected>Select Title</option>
                                    @foreach ($titles as $title)
                                        <option value="{{ $title->id }}" {{ old('title_id') == $title->id ? 'selected' : '' }}>
                                            {{ $title->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="{{ old('name') }}" required placeholder="Enter full name">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="{{ old('email') }}" required placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                        value="{{ old('phone') }}" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Profile Image</h5>
                            
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img id="imagePreview" src="{{ asset('images/default-avatar.png') }}" 
                                        class="rounded-circle border border-3 border-primary" 
                                        style="width: 150px; height: 150px; object-fit: cover;"
                                        alt="Profile Preview">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label">Upload Profile Picture</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                    accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Recommended size: 400x400px, Max: 2MB</div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Lecturer
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
                preview.src = "{{ asset('images/default-avatar.png') }}";
            }
        }
    </script>
    @endpush
@endsection
