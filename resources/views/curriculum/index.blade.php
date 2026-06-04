@extends('layouts.app')

@section('title', 'Curriculum Setup')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">Curriculum Setup</h2>
                <div class="text-muted">
                    <span class="badge bg-primary">{{ $activeSession->name }}</span>
                    <span class="ms-2">{{ $activeCurriculum->name }}</span>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.academic-sessions.curricula.index', $activeSession) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Curricula
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bulk Upload Mappings</h5>
                <a href="{{ route('curriculum.download-sample') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download"></i> Download Sample
                </a>
            </div>
            <div class="card-body">
                <form id="bulkUploadForm" action="{{ route('curriculum.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input type="file" class="form-control" name="file" id="file" accept=".xlsx,.xls" required>
                            <div class="form-text">Upload an Excel file with course unit mappings</div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100" id="uploadBtn">
                                <i class="bi bi-upload"></i> Upload Mappings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @forelse($schools as $school)
                @if($school->programmes->count() > 0)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $school->name }}</h5>
                                <span class="badge bg-primary">{{ $school->programmes->count() }} Programmes</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($school->programmes as $programme)
                                        <a href="{{ route('curriculum.show', $programme->id) }}" class="list-group-item list-group-item-action">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $programme->name }}</h6>
                                                    <small class="text-muted">{{ $programme->code }}</small>
                                                </div>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $programme->course_units_count ?? 0 }} Courses
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No programmes found with curriculum mappings in the current session.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('uploadBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
    });

    // Show toast notifications for bulk upload results
    @if(session('success_report') || session('error_report'))
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success_report'))
                showToast('success', 'Success', '{{ count(session('success_report')) }} mappings were imported successfully!');
            @endif
            
            @if(session('error_report'))
                showToast('danger', 'Warning', '{{ count(session('error_report')) }} mappings failed to import.');
            @endif
        });
    @endif

    function showToast(type, title, message) {
        const toastContainer = document.createElement('div');
        toastContainer.className = `toast align-items-center text-white bg-${type} border-0`;
        toastContainer.setAttribute('role', 'alert');
        toastContainer.setAttribute('aria-live', 'assertive');
        toastContainer.setAttribute('aria-atomic', 'true');
        
        toastContainer.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(toastContainer);
        const toast = new bootstrap.Toast(toastContainer);
        toast.show();
        
        // Remove the toast after it's hidden
        toastContainer.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toastContainer);
        });
    }
</script>
@endpush
