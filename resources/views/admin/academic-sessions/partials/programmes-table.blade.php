@if($programmes->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>School</th>
                    <th>Course Units</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $programme)
                    <tr>
                        <td>{{ $programme->programme_code ?? $programme->code ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('admin.programmes.show', $programme) }}">
                                {{ $programme->name }}
                            </a>
                        </td>
                        <td>{{ $programme->school->name ?? 'N/A' }}</td>
                        <td>
                            {{ $programme->course_units_count ?? 0 }}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.academic-sessions.programmes.map-course-units', ['academicSession' => $academicSession->id, 'programme' => $programme->id]) }}" class="btn btn-sm btn-outline-primary" title="Map Course Units">
                                    <i class="bi bi-list-check"></i>
                                </a>
                                <a href="{{ route('admin.programmes.show', $programme) }}" class="btn btn-sm btn-outline-secondary" title="View Programme">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.academic-sessions.programmes.detach', ['academicSession' => $academicSession, 'programme' => $programme]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from Session" onclick="return confirm('Are you sure you want to remove this programme from the session?')">
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
    @if($programmes->hasPages())
        <div class="pagination-container mt-3">
            {{ $programmes->links() }}
        </div>
    @endif
@else
    <div class="alert alert-info">
        No programmes found for the selected school.
    </div>
@endif
