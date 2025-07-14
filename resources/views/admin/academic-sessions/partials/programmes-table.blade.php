<div class="d-flex justify-content-between align-items-center mb-3">
    <h5>Programmes in {{ $academicSession->name }}</h5>
    <a href="{{ route('admin.academic-sessions.bulk-upload', $academicSession) }}" class="btn btn-primary">
        <i class="bi bi-upload me-1"></i> Bulk Upload Mappings
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($programmes->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>School</th>
                    <th>Course Mapping</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $programme)
                    <tr>
                        <td>{{ $programme->programme_code ?? $programme->code ?? 'N/A' }}</td>
                        <td>{{ $programme->name }}</td>
                        <td>{{ $programme->school->name ?? 'N/A' }}</td>
                        <td>
                            {{ $programme->mapped_course_units_count ?? 0 }}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Manage Schedule">
                                    <i class="bi bi-calendar-plus"></i>
                                </a>
                                <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                   class="btn btn-sm btn-outline-secondary" 
                                   title="Map Course Units">
                                    <i class="bi bi-list-check"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-secondary" 
                                        disabled 
                                        title="View Programme (Disabled)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <form action="{{ route('admin.academic-sessions.programmes.detach', ['academicSession' => $academicSession, 'programme' => $programme]) }}" 
                                      method="POST" 
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Remove from Session" 
                                            onclick="return confirm('Are you sure you want to remove this programme from the session?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($programmes, 'hasPages') && $programmes->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            <nav>
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($programmes->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">« Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $programmes->previousPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" rel="prev">« Prev</a>
                        </li>
                    @endif

                    {{-- Page Number Links --}}
                    @for ($page = 1; $page <= $programmes->lastPage(); $page++)
                        <li class="page-item {{ $page == $programmes->currentPage() ? 'active' : '' }}">
                            <a class="page-link"
                                href="{{ $programmes->url($page) }}&{{ http_build_query(request()->except('page', '_token')) }}">{{ $page }}</a>
                        </li>
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($programmes->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $programmes->nextPageUrl() }}&{{ http_build_query(request()->except('page', '_token')) }}" rel="next">Next »</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next »</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
@else
    <div class="alert alert-info">No programmes found for this academic session.</div>
@endif

@include('admin.academic-sessions.partials.bulk-upload-modal')
