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
        @php
            // Group programmes by school
            $programmesBySchool = $programmes->groupBy(function($programme) {
                return $programme->school->name ?? 'No School';
            });
        @endphp
        
        @foreach($programmesBySchool as $schoolName => $schoolProgrammes)
            <h6 class="mt-4 mb-3 text-primary">
                <i class="bi bi-building"></i> {{ $schoolName }}
                <span class="badge bg-secondary">{{ $schoolProgrammes->count() }}</span>
            </h6>
            
            <table class="table table-hover table-sm mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Department</th>
                        <th>Programme</th>
                        <th>Mapped Units</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolProgrammes->sortBy([['department.name', 'asc'], ['programme_code', 'asc']]) as $programme)
                        <tr>
                            <td>{{ $programme->department->name ?? 'N/A' }}</td>
                            <td>
                                <strong>{{ $programme->programme_code ?? $programme->code ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $programme->name }}</small>
                            </td>
                            <td>
                                {{ $programme->mapped_course_units_count ?? 0 }}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if(isset($viewMode) && $viewMode == 'curriculum')
                                        <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Map Course Units">
                                            <i class="bi bi-list-check"></i> Map Course Units
                                        </a>
                                        <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Manage Schedule">
                                            <i class="bi bi-calendar-plus"></i>
                                        </a>
                                    @elseif(isset($viewMode) && $viewMode == 'allocation')
                                        <a href="{{ route('admin.academic-sessions.programmes.scheduling.show', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Manage Schedule">
                                            <i class="bi bi-calendar-plus"></i> Allocate Teachers
                                        </a>
                                        <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Map Course Units">
                                            <i class="bi bi-list-check"></i>
                                        </a>
                                    @else
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
                                    @endif
                                    
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            disabled 
                                            title="View Programme (Disabled)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if(!auth()->user()->hasRole('timetabler'))
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
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
    
    <div class="alert alert-info">No programmes found for this academic session.</div>
@endif

@include('admin.academic-sessions.partials.bulk-upload-modal')
