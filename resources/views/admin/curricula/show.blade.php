@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>{{ $curriculum->name }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.index') }}">Academic Sessions</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.academic-sessions.show', $academicSession) }}">{{ $academicSession->name }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $curriculum->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <div class="btn-group">
                <a href="{{ route('admin.academic-sessions.curricula.edit', [$academicSession, $curriculum]) }}" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" 
                           href="{{ route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum]) }}">
                            <i class="bi bi-list-ul me-2"></i> Manage Course Units
                        </a>
                    </li>
                    @if(!$curriculum->is_active)
                        <li>
                            <form action="{{ route('admin.academic-sessions.curricula.set-active', [$academicSession, $curriculum]) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-check-circle me-2"></i> Set as Active
                                </button>
                            </form>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button type="button" 
                                class="dropdown-item text-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteCurriculumModal">
                            <i class="bi bi-trash me-2"></i> Delete Curriculum
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Curriculum Details</h5>
                    @if($curriculum->is_active)
                        <span class="badge bg-success">Active</span>
                    @endif
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $curriculum->name }}</dd>
                        
                        <dt class="col-sm-3">Academic Session</dt>
                        <dd class="col-sm-9">{{ $academicSession->name }}</dd>
                        
                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            @if($curriculum->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Created</dt>
                        <dd class="col-sm-9">
                            {{ $curriculum->created_at->format('M d, Y') }}
                            <small class="text-muted">by {{ $curriculum->creator->name ?? 'System' }}</small>
                        </dd>
                        
                        @if($curriculum->updated_at != $curriculum->created_at)
                            <dt class="col-sm-3">Last Updated</dt>
                            <dd class="col-sm-9">
                                {{ $curriculum->updated_at->format('M d, Y') }}
                                @if($curriculum->updater)
                                    <small class="text-muted">by {{ $curriculum->updater->name }}</small>
                                @endif
                            </dd>
                        @endif
                        
                        @if($curriculum->description)
                            <dt class="col-sm-3">Description</dt>
                            <dd class="col-sm-9">{{ $curriculum->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Programmes in this Curriculum</h5>
                </div>
                <div class="card-body">
                    @if($programmes->isEmpty())
                        <div class="alert alert-info mb-0">
                            No programmes have been added to this curriculum yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Programme</th>
                                        <th>Code</th>
                                        <th>Department</th>
                                        <th class="text-end">Course Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($programmes as $programme)
                                        <tr>
                                            <td>{{ $programme->name }}</td>
                                            <td>{{ $programme->code ?? 'N/A' }}</td>
                                            <td>{{ $programme->department->name ?? 'N/A' }}</td>
                                            <td class="text-end">
                                                {{ $programme->curriculumMappings($curriculum->id)->count() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Total Course Units</h6>
                                <small class="text-muted">Across all programmes</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $totalCourseUnits }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Programmes</h6>
                                <small class="text-muted">In this curriculum</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $programmes->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Core Courses</h6>
                                <small class="text-muted">Required for all students</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $coreCourseCount }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0">Elective Courses</h6>
                                <small class="text-muted">Optional for students</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ $electiveCourseCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.academic-sessions.curricula.mappings.index', [$academicSession, $curriculum]) }}" 
                           class="btn btn-primary">
                            <i class="bi bi-list-ul me-2"></i> Manage Course Units
                        </a>
                        
                        @if(!$curriculum->is_active)
                            <form action="{{ route('admin.academic-sessions.curricula.set-active', [$academicSession, $curriculum]) }}" 
                                  method="POST" 
                                  class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="bi bi-check-circle me-2"></i> Set as Active
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.academic-sessions.curricula.edit', [$academicSession, $curriculum]) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i> Edit Curriculum
                        </a>
                        
                        @if($previousSession && $previousSession->curricula->isNotEmpty())
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                        id="rollForwardDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-arrow-90deg-right me-2"></i> Roll Forward From
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="rollForwardDropdown">
                                    @foreach($previousSession->curricula as $prevCurriculum)
                                        <li>
                                            <form action="{{ route('admin.academic-sessions.curricula.roll-forward', [$academicSession, $prevCurriculum]) }}" 
                                                  method="POST" 
                                                  class="d-inline w-100">
                                                @csrf
                                                <button type="submit" 
                                                        class="dropdown-item" 
                                                        onclick="return confirm('Roll forward {{ $prevCurriculum->name }} from {{ $previousSession->name }}? This will copy all course unit mappings but not scheduling information.')">
                                                    {{ $prevCurriculum->name }}
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Course Units by Year and Semester</h5>
        </div>
        <div class="card-body p-0">
            @if($programmes->isEmpty())
                <div class="text-center p-4">
                    <p class="text-muted">No programmes found in this curriculum.</p>
                    <a href="{{ route('admin.academic-sessions.curricula.mappings.create', [$academicSession, $curriculum]) }}" 
                       class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Course Units
                    </a>
                </div>
            @else
                <div class="accordion" id="programmeAccordion">
                    @foreach($programmes as $programme)
                        @php
                            $mappingsByYearSemester = $programme->getCurriculumStructure($curriculum->id);
                        @endphp
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $programme->id }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $programme->id }}" 
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                        aria-controls="collapse{{ $programme->id }}">
                                    {{ $programme->name }}
                                    <span class="badge bg-primary ms-2">
                                        {{ $programme->curriculumMappings($curriculum->id)->count() }} course units
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{ $programme->id }}" 
                                 class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                 aria-labelledby="heading{{ $programme->id }}" 
                                 data-bs-parent="#programmeAccordion">
                                <div class="accordion-body p-0">
                                    @if(empty($mappingsByYearSemester))
                                        <div class="p-3 text-center text-muted">
                                            No course units have been added to this programme yet.
                                        </div>
                                    @else
                                        <div class="accordion" id="yearAccordion{{ $programme->id }}">
                                            @foreach($mappingsByYearSemester as $yearId => $semesters)
                                                @php
                                                    $year = $years->firstWhere('id', $yearId);
                                                @endphp
                                                
                                                <div class="accordion-item border-0">
                                                    <h2 class="accordion-header" id="yearHeading{{ $programme->id }}{{ $yearId }}">
                                                        <button class="accordion-button bg-light py-2 px-3" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#yearCollapse{{ $programme->id }}{{ $yearId }}" 
                                                                aria-expanded="true" 
                                                                aria-controls="yearCollapse{{ $programme->id }}{{ $yearId }}">
                                                            {{ $year ? $year->name : 'Year ' . $yearId }}
                                                        </button>
                                                    </h2>
                                                    <div id="yearCollapse{{ $programme->id }}{{ $yearId }}" 
                                                         class="accordion-collapse collapse show" 
                                                         aria-labelledby="yearHeading{{ $programme->id }}{{ $yearId }}" 
                                                         data-bs-parent="#yearAccordion{{ $programme->id }}">
                                                        <div class="accordion-body p-0">
                                                            @foreach($semesters as $semesterId => $mappings)
                                                                @php
                                                                    $semester = $semestersList->firstWhere('id', $semesterId);
                                                                @endphp
                                                                
                                                                <div class="card border-0 rounded-0">
                                                                    <div class="card-header bg-white border-bottom py-2 px-3">
                                                                        <h6 class="mb-0">
                                                                            {{ $semester ? $semester->name : 'Semester ' . $semesterId }}
                                                                            <span class="badge bg-secondary ms-2">{{ count($mappings) }}</span>
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body p-0">
                                                                        <ul class="list-group list-group-flush">
                                                                            @foreach($mappings as $mapping)
                                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                    <div>
                                                                                        <div class="fw-bold">{{ $mapping->courseUnit->code }}</div>
                                                                                        <div class="text-muted small">{{ $mapping->courseUnit->name }}</div>
                                                                                        @if($mapping->lecturer)
                                                                                            <div class="small mt-1">
                                                                                                <i class="bi bi-person me-1"></i> {{ $mapping->lecturer->name }}
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="text-end">
                                                                                        @if($mapping->is_elective)
                                                                                            <span class="badge bg-warning text-dark">Elective</span>
                                                                                        @else
                                                                                            <span class="badge bg-primary">Core</span>
                                                                                        @endif
                                                                                        @if($mapping->max_students)
                                                                                            <div class="small text-muted">Max: {{ $mapping->max_students }}</div>
                                                                                        @endif
                                                                                    </div>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCurriculumModal" tabindex="-1" aria-labelledby="deleteCurriculumModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCurriculumModalLabel">Delete Curriculum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the curriculum <strong>{{ $curriculum->name }}</strong>?</p>
                
                @if($curriculum->courseUnitProgrammeMappings()->exists())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        This curriculum contains {{ $curriculum->courseUnitProgrammeMappings()->count() }} course unit mappings that will also be deleted.
                    </div>
                @endif
                
                @if($curriculum->is_active)
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        This is the active curriculum. You must set another curriculum as active before deleting this one.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.academic-sessions.curricula.destroy', [$academicSession, $curriculum]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" {{ $curriculum->is_active ? 'disabled' : '' }}>
                        <i class="bi bi-trash me-1"></i> Delete Curriculum
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
