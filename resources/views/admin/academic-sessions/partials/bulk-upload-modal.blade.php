<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkUploadModalLabel">Bulk Upload Course Unit Mappings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.academic-sessions.bulk-upload', $academicSession) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
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
                    
                    @if(session('show_report_link') || session('skipped_items') || session('error_items'))
                        <div class="mt-3">
                            @if(session('show_report_link') && session('report_filename'))
                                <div class="alert alert-success">
                                    <h6 class="alert-heading"><i class="bi bi-check-circle"></i> Upload Complete</h6>
                                    <p class="mb-2">Your bulk upload has been processed successfully.</p>
                                    <a href="{{ route('admin.academic-sessions.bulk-upload.report', [$academicSession, session('report_filename')]) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-download"></i> Download Full Report
                                    </a>
                                </div>
                            @endif
                            
                            @if(session('skipped_items'))
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Skipped Items (First 50)</h6>
                                    <ul class="mb-0">
                                        @foreach(session('skipped_items') as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                    @if(count(session('skipped_items', [])) >= 50)
                                        <p class="mb-0 mt-2"><small>Showing first 50 items. Download the full report for complete details.</small></p>
                                    @endif
                                </div>
                            @endif
                            
                            @if(session('error_items'))
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading"><i class="bi bi-x-circle"></i> Errors (First 50)</h6>
                                    <ul class="mb-0">
                                        @foreach(session('error_items') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    @if(count(session('error_items', [])) >= 50)
                                        <p class="mb-0 mt-2"><small>Showing first 50 errors. Download the full report for complete details.</small></p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload and Process</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle bulk upload form submission
    const bulkUploadForm = document.querySelector('#bulkUploadModal form');
    if (bulkUploadForm) {
        bulkUploadForm.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please select a file to upload.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        });
    }
});
</script>
@endpush
