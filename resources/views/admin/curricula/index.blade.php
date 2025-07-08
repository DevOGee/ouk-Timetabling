@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Curricula for {{ $academicSession->name }}</h2>
            <p class="text-muted">Manage curriculum setups for this academic session.</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.academic-sessions.curricula.create', $academicSession) }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create New Curriculum
            </a>
            
            @if($previousSession && $previousSession->curricula->isNotEmpty())
            <div class="dropdown d-inline-block ms-2">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="rollForwardDropdown" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-arrow-90deg-right"></i> Roll Forward From
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="rollForwardDropdown">
                    @foreach($previousSession->curricula as $prevCurriculum)
                    <li>
                        <form action="{{ route('admin.academic-sessions.curricula.roll-forward', [$academicSession, $prevCurriculum]) }}" 
                              method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item" 
                                    onclick="return confirm('Roll forward {{ $prevCurriculum->name }} from {{ $previousSession->name }}?')">
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

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            @if($curricula->isEmpty())
                <div class="text-center p-4">
                    <p class="text-muted">No curricula found for this session.</p>
                    <a href="{{ route('admin.academic-sessions.curricula.create', $academicSession) }}" 
                       class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create Your First Curriculum
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Course Units</th>
                                <th>Last Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($curricula as $curriculum)
                                <tr>
                                    <td>
                                        <strong>{{ $curriculum->name }}</strong>
                                        @if($curriculum->is_active)
                                            <span class="badge bg-success ms-2">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($curriculum->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <form action="{{ route('admin.academic-sessions.curricula.set-active', [$academicSession, $curriculum]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                    Set as Active
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>{{ $curriculum->course_units_count }} course units</td>
                                    <td>{{ $curriculum->updated_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="if(confirm('Are you sure you want to delete this curriculum?')) { document.getElementById('delete-curriculum-{{ $curriculum->id }}').submit(); }">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-curriculum-{{ $curriculum->id }}" 
                                                  action="{{ route('admin.academic-sessions.curricula.destroy', [$academicSession, $curriculum]) }}" 
                                                  method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $curricula->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
