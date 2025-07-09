@extends('layouts.app')

@section('title', 'Edit Lecturer')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Edit Lecturer</h2>
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
                <form action="{{ route('instructors.update', $lecturer) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Personal Information</h5>
                            
                            <div class="mb-3">
                                <label for="title_id" class="form-label">Title <span class="text-danger">*</span></label>
                                <select class="form-select" id="title_id" name="title_id" required>
                                    <option value="" disabled>Select Title</option>
                                    @foreach ($titles as $title)
                                        <option value="{{ $title->id }}" 
                                            {{ old('title_id', $lecturer->title_id) == $title->id ? 'selected' : '' }}>
                                            {{ $title->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="{{ old('name', $lecturer->name) }}" required placeholder="Enter full name">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" 
                                        value="{{ old('email', $lecturer->email) }}" required placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                        value="{{ old('phone', $lecturer->phone) }}" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Profile Image</h5>
                            
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img id="imagePreview" 
                                        src="{{ $lecturer->image_path ? asset('storage/' . $lecturer->image_path) : asset('images/default-avatar.png') }}" 
                                        class="rounded-circle border border-3 border-primary" 
                                        style="width: 150px; height: 150px; object-fit: cover;"
                                        alt="Profile Preview">
                                    @if($lecturer->image_path)
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                            onclick="removeImage()" title="Remove image">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label">Change Profile Picture</label>
                                <input type="file" class="form-control" id="image" name="image" 
                                    accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Recommended size: 400x400px, Max: 2MB</div>
                                <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ old('status', $lecturer->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $lecturer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('instructors.show', $lecturer) }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-1"></i> View Profile
                            </a>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Lecturer
                            </button>
                        </div>
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
                // Reset remove image flag if user selects a new image
                document.getElementById('removeImageFlag').value = '0';
                
                // Show the remove button if it doesn't exist
                const removeBtn = preview.nextElementSibling;
                if (!removeBtn || !removeBtn.classList.contains('btn-danger')) {
                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-1';
                    removeButton.innerHTML = '<i class="bi bi-x-lg"></i>';
                    removeButton.title = 'Remove image';
                    removeButton.onclick = removeImage;
                    preview.parentNode.appendChild(removeButton);
                }
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            const preview = document.getElementById('imagePreview');
            const fileInput = document.getElementById('image');
            const removeImageFlag = document.getElementById('removeImageFlag');
            
            // Reset file input
            fileInput.value = '';
            
            // Set default image
            preview.src = "{{ asset('images/default-avatar.png') }}";
            
            // Set flag to indicate image should be removed
            removeImageFlag.value = '1';
            
            // Remove the remove button
            const removeBtn = preview.nextElementSibling;
            if (removeBtn && removeBtn.classList.contains('btn-danger')) {
                removeBtn.remove();
            }
        }
    </script>
    @endpush
@endsection
