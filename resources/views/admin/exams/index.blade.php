@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Exam Timetables</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.exams.create') }}" class="btn btn-sm btn-primary">
                <span data-feather="plus"></span>
                Create New Schedule
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($schedules->isEmpty())
                <p class="text-muted text-center py-4">No exam schedules found. Create one to get started.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Academic Session</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.exams.show', $schedule) }}" class="fw-bold text-decoration-none">
                                            {{ $schedule->name }}
                                        </a>
                                    </td>
                                    <td>{{ $schedule->academicSession->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ $schedule->start_date->format('M d, Y') }} - 
                                        {{ $schedule->end_date->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-{{ $schedule->is_active ? 'success' : 'secondary' }}">
                                                {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span class="badge bg-{{ $schedule->is_published ? 'info' : 'warning' }}">
                                                {{ $schedule->is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($schedule->is_published)
                                                <form action="{{ route('admin.exams.unpublish', $schedule) }}" method="POST" onsubmit="return confirm('Are you sure you want to unpublish this schedule?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Unpublish">
                                                        <i class="bi bi-eye-slash-fill"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.exams.publish', $schedule) }}" method="POST" onsubmit="return confirm('Are you sure you want to publish this schedule?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Publish">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.exams.toggle-status', $schedule) }}" method="POST" onsubmit="return confirm('Are you sure you want to toggle the status of this schedule?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info" title="{{ $schedule->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="bi bi-power"></i>
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('admin.exams.show', $schedule) }}" class="btn btn-sm btn-primary" title="Manage">
                                                <i class="bi bi-gear-fill"></i>
                                            </a>
                                            <form action="{{ route('admin.exams.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $schedules->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
