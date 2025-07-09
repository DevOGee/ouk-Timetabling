@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Bulk Upload Course Unit Mappings</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic-sessions.show', $academicSession) }}">{{ $academicSession->name }}</a></li>
                    <li class="breadcrumb-item active">Bulk Upload</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Upload CSV File</h5>
        </div>
        <div class="card-body
            @if(session('show_report')) 
                d-none
            @endif" 
            id="uploadSection">
            <form action="{{ route('admin.academic-sessions.bulk-upload', $academicSession) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="mb-3">
                    <label for="mappingFile" class="form-label">CSV File</label>
                    <input class="form-control" type="file" id="mappingFile" name="mapping_file" accept=".csv" required>
                    <div class="form-text">
                        <a href="{{ asset('templates/course_units_mapping_template.csv') }}" download>
                            <i class="bi bi-download"></i> Download CSV Template
                        </a>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> The CSV should contain columns: programme_code, course_unit_code, year_of_study, semester
                </div>
                <button type="submit" class="btn btn-primary">
                    <span id="submitText">Upload and Process</span>
                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
                <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    @if(session('show_report'))
    <div class="card mt-4" id="reportSection">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Upload Results</h5>
            <div>
                <a href="{{ route('admin.academic-sessions.bulk-upload.report', [$academicSession, session('report_filename')]) }}" 
                   class="btn btn-sm btn-outline-primary me-2">
                    <i class="bi bi-download"></i> Download Full Report
                </a>
                <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-check-lg"></i> Done
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> {!! session('success') !!}
            </div>

            @if(session('skipped_items'))
                <div class="alert alert-warning">
                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Skipped Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Programme Code</th>
                                    <th>Course Unit</th>
                                    <th>Year</th>
                                    <th>Semester</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('skipped_items') as $item)
                                    @php
                                        $parts = explode(':', $item, 2);
                                        $reason = trim($parts[1] ?? 'Unknown reason');
                                        $code = trim($parts[0] ?? '');
                                    @endphp
                                    <tr>
                                        <td>{{ $record['programme_code'] ?? $code }}</td>
                                        <td>{{ $record['course_unit_code'] ?? '' }}</td>
                                        <td>{{ $record['year_of_study'] ?? '' }}</td>
                                        <td>{{ $record['semester'] ?? '' }}</td>
                                        <td>{{ $reason }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(session('error_items'))
                <div class="alert alert-danger">
                    <h6 class="alert-heading"><i class="bi bi-x-circle"></i> Errors</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Row</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('error_items') as $error)
                                    @php
                                        $parts = explode(':', $error, 2);
                                        $row = trim(str_replace('Error processing row', '', $parts[0] ?? ''));
                                        $message = trim($parts[1] ?? $error);
                                    @endphp
                                    <tr>
                                        <td>{{ $row ?: 'N/A' }}</td>
                                        <td>{{ $message }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitText.textContent = 'Processing...';
            submitSpinner.classList.remove('d-none');
        });
    }
    
    // Auto-scroll to report section if it exists
    const reportSection = document.getElementById('reportSection');
    if (reportSection) {
        reportSection.scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
@endpush
@endsection
