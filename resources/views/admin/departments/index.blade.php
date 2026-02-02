@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Departments</h1>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add New Department
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>School</th>
                            <th>Programmes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>{{ $department->code }}</td>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->school->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $department->programmes_count ?? $department->programmes()->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No departments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
