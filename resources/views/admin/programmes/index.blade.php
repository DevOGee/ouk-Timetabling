@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Programmes</h2>
        <div>
            <a href="{{ route('admin.programmes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Programme
            </a>
            <a href="{{ route('admin.programmes.bulk-upload') }}" class="btn btn-outline-primary">
                <i class="bi bi-upload"></i> Bulk Upload
            </a>
        </div>
    </div>

    @if(session('success'))
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
                            <th>Code</th>
                            <th>Name</th>
                            <th>School</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programmes as $programme)
                            <tr>
                                <td>{{ $programme->programme_code }}</td>
                                <td>{{ $programme->name }}</td>
                                <td>{{ $programme->school->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.programmes.edit', $programme) }}" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    No programmes found. 
                                    <a href="{{ route('admin.programmes.create') }}">Add your first programme</a> or 
                                    <a href="{{ route('admin.programmes.bulk-upload') }}">bulk upload programmes</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $programmes->links() }}
    </div>
</div>
@endsection
