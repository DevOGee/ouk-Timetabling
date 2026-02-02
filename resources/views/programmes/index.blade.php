@extends('layouts.app')

@section('title', 'Programme Scheduling')

@section('content')
    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Programmes</h2>
            <div>
                <a href="{{ route('admin.specialisations.index') }}" class="btn btn-outline-primary me-2">Manage Specialisations</a>
                <a href="{{ route('admin.programmes.create') }}" class="btn btn-success">Add Programme</a>
            </div>
        </div>

        @foreach ($schools as $school)
            <div class="mb-5">
                <h4 class="mb-3 text-primary border-bottom pb-2">{{ $school->name }}</h4>

                @if ($school->departments->isEmpty())
                    <p class="text-muted ms-3">No departments available in this school.</p>
                @else
                    @foreach ($school->departments as $department)
                        <div class="ms-3 mb-4">
                            <h5 class="mb-2 text-secondary">{{ $department->name }}</h5>
                            
                            @if ($department->programmes->isEmpty())
                                <p class="text-muted ms-3">No programmes available in this department.</p>
                            @else
                                <ul class="list-group ms-3">
                                    @foreach ($department->programmes->sortBy('programme_code') as $programme)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $programme->programme_code }}</strong> – {{ $programme->name }}
                                            </div>
                                            <div>
                                                @if($programme->has_specialisations)
                                                    <a href="{{ route('admin.specialisations.index', ['programme_id' => $programme->id]) }}"
                                                        class="btn btn-outline-primary btn-sm me-1">View Specialisations</a>
                                                @endif
                                                <a href="{{ route('admin.programmes.edit', $programme) }}"
                                                    class="btn btn-warning btn-sm me-1">Edit</a>
                                                <form action="{{ route('admin.programmes.destroy', $programme) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this programme?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
@endsection
