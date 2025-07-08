@extends('layouts.app')

@section('content')
@push('styles')
<style>
    .file-upload-container {
        border: 2px dashed #dee2e6;
        border-radius: 0.375rem;
        padding: 2rem;
        text-align: center;
        background-color: #f8f9fa;
        transition: all 0.2s ease-in-out;
    }
    .file-upload-container:hover {
        border-color: #0d6efd;
        background-color: #f0f7ff;
    }
    .file-upload-container.drag-over {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }
</style>
@endpush
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bulk Upload Programmes</h5>
                </div>
                <div class="card-body">
                    @if(session('error') || session('warning'))
                        <div class="alert alert-{{ session('error') ? 'danger' : 'warning' }} mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') ?? session('warning') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-octagon-fill me-2"></i>Validation Errors</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('import_errors') && is_array(session('import_errors')))
                        <div class="alert alert-warning mb-4">
                            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Import Warnings</h6>
                            <p>The following issues were encountered during import:</p>
                            <ul class="mb-0">
                                @foreach(session('import_errors') as $error)
                                    @if(is_array($error))
                                        <li>Row {{ $error['row'] ?? 'N/A' }}: {{ implode(', ', $error['errors'] ?? ['Unknown error']) }}</li>
                                    @else
                                        <li>{{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Instructions</h6>
                        <ol class="mb-0">
                            <li>Download the template file below</li>
                            <li>Fill in the programme details</li>
                            <li>Upload the completed file</li>
                        </ol>
                    </div>

                    <form action="{{ route('admin.programmes.process-bulk-upload') }}" method="POST" enctype="multipart/form-data" id="bulkUploadForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="school_id" class="form-label">School <span class="text-danger">*</span></label>
                            <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                <option value="">-- Select School --</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">All programmes in this import will be associated with the selected school.</div>
                        </div>

                        <div class="mb-4">
                            <label for="file" class="form-label">CSV/Excel File <span class="text-danger">*</span></label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".csv, .xlsx, .xls" 
                                   required
                                   @if(!old('school_id')) disabled @endif>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Maximum file size: 10MB. Accepted formats: .csv, .xlsx, .xls
                            </div>
                        </div>
                        
                        <div class="alert alert-light border mb-4">
                            <h6 class="alert-heading"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Quick Tip</h6>
                            <p class="mb-0">
                                For best results, use the template file and ensure all required fields are filled in.
                                The first row should contain column headers exactly as shown in the template.
                            </p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.programmes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Programmes
                            </a>
                            <div>
                                <a href="{{ route('admin.programmes.download-template') }}" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-download"></i> Download Template
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Upload Programmes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">File Format</h5>
            </div>
            <div class="card-body">
                <p>Your CSV/Excel file should have the following columns (in order):</p>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Column</th>
                            <th>Description</th>
                            <th>Required</th>
                            <th>Example</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>programme_code</td>
                            <td>Unique code for the programme</td>
                            <td><span class="badge bg-success">Yes</span></td>
                            <td>BSC-IT</td>
                        </tr>
                        <tr>
                            <td>name</td>
                            <td>Full name of the programme</td>
                            <td><span class="badge bg-success">Yes</span></td>
                            <td>Bachelor of Science in Information Technology</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file');
        const schoolSelect = document.getElementById('school_id');
        const fileUploadContainer = document.querySelector('.file-upload-container');
        
        // Enable/disable file input based on school selection
        schoolSelect.addEventListener('change', function() {
            fileInput.disabled = !this.value;
            if (!this.value) {
                fileInput.value = '';
            }
        });
        
        // Handle drag and drop
        if (fileUploadContainer) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadContainer.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadContainer.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadContainer.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                fileUploadContainer.classList.add('drag-over');
            }
            
            function unhighlight() {
                fileUploadContainer.classList.remove('drag-over');
            }
            
            fileUploadContainer.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    fileInput.files = files;
                    updateFileName(files[0].name);
                }
            }
            
            fileInput.addEventListener('change', function() {
                if (this.files.length) {
                    updateFileName(this.files[0].name);
                }
            });
            
            function updateFileName(fileName) {
                const fileNameElement = document.getElementById('file-name');
                if (fileNameElement) {
                    fileNameElement.textContent = fileName;
                    fileNameElement.classList.remove('text-muted');
                }
            }
        }
    });
</script>
@endpush

@endsection
