@extends('layouts.app')

@section('title', 'My Course Units')

@push('styles')
<style>
    .course-code {
        font-family: 'Fira Code', 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
        font-weight: 600;
    }
    .programme-badge {
        font-size: 0.8rem;
        margin: 2px;
    }
    .level-badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-journal-text me-2 text-primary"></i>My Course Units
            @if(isset($activeSession))
                <span class="text-muted fs-6 fw-normal ms-2">{{ $activeSession->name }}</span>
            @endif
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-9">
            @if($courseUnits->isEmpty())
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                    <div>You don't have any assigned course units for the current academic session.</div>
                </div>
            @else
                @foreach($courseUnits as $courseCode => $mappings)
                    @php
                        $firstMapping = $mappings->first();
                        $courseUnit = $firstMapping->courseUnit;
                        $year = $courseUnit->yearOfStudy->name ?? null;
                        $semester = $courseUnit->semester->name ?? null;
                        $level = $year && $semester ? $year . '.' . $semester : null;
                    @endphp
                    
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <h5 class="m-0 fw-bold text-primary d-flex align-items-center">
                                    <span class="course-code me-2">{{ $courseCode }}</span>
                                    <span class="fs-6 fw-normal text-muted">{{ $courseUnit->name ?? 'N/A' }}</span>
                                </h5>
                                @if($level)
                                    <span class="badge bg-info bg-opacity-10 text-info level-badge">
                                        <i class="bi bi-layers me-1"></i>Level {{ $level }}
                                    </span>
                                @endif
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                <i class="bi bi-collection me-1"></i>
                                {{ $mappings->count() }} programme{{ $mappings->count() > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 ps-4">Programme</th>
                                            <th class="border-0 text-center">Level</th>
                                            <th class="border-0 text-center">Day</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($mappings as $mapping)
                                            <tr class="border-top">
                                                <td class="ps-4">
                                                    <div class="fw-medium">
                                                        <span class="badge bg-light text-dark border me-2">
                                                            {{ $mapping->programme->programme_code ?? 'N/A' }}
                                                        </span>
                                                        {{ $mapping->programme->name ?? 'N/A' }}
                                                    </div>
                                                    @if($mapping->programme->short_name ?? false)
                                                        <div class="text-muted small">{{ $mapping->programme->short_name }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info bg-opacity-10 text-info">
                                                        {{ $mapping->yearOfStudy->name ?? 'N/A' }}.{{ $mapping->semester->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($mapping->day)
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                                            <i class="bi bi-calendar3 me-1"></i>{{ $mapping->day->name }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted">Not scheduled</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            
            <!-- Placeholder for future content -->
            <div class="mt-4">
                <!-- Additional content can go here -->
            </div>
        </div>
        
        <!-- Right sidebar (1/4 width) -->
        <div class="col-lg-3">
            <!-- Placeholder for future content -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Information</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">
                        This section can be used for additional information, quick actions, or other relevant content.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@endsection
