@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h2>Academic Sessions</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.academic-sessions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add New Session
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Current</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.academic-sessions.show', $session) }}" class="text-decoration-none">
                                        {{ $session->name }}
                                    </a>
                                </td>
                                <td>{{ $session->code }}</td>
                                <td>{{ $session->duration }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $session->status === 'active' ? 'success' : 
                                        ($session->status === 'upcoming' ? 'info' : 
                                        ($session->status === 'completed' ? 'secondary' : 'dark'))
                                    }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($session->is_current)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Current</span>
                                    @else
                                        <form action="{{ route('admin.academic-sessions.set-current', $session) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                Set as Current
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.academic-sessions.edit', $session) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if ($session->status !== 'archived')
                                            <form action="{{ route('admin.academic-sessions.archive', $session) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        onclick="return confirm('Are you sure you want to archive this session?')">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if (!$session->timetables()->exists())
                                            <form action="{{ route('admin.academic-sessions.destroy', $session) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this session?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No academic sessions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
