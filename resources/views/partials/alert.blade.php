@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('skipped_rows') && count(session('skipped_rows')) > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ count(session('skipped_rows')) }} rows were skipped during import.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="mt-2">
            <button class="btn btn-sm btn-outline-warning" type="button" data-bs-toggle="collapse" data-bs-target="#skippedRows" aria-expanded="false" aria-controls="skippedRows">
                Show details
            </button>
            <div class="collapse mt-2" id="skippedRows">
                <div class="card card-body bg-light">
                    <ul class="mb-0">
                        @foreach(session('skipped_rows') as $row)
                            <li>{{ $row }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
