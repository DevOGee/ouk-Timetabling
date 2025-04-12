@extends('layouts.app')

@section('title', 'Programme Scheduling')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Programme Scheduling</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('programmes.create') }}" class="mb-3 btn btn-primary">Add Programme</a>


        @foreach ($schools as $school)
            <div class="mb-5">
                <h5 class="mb-3">{{ $school->name }}</h5>

                @if ($school->programmes->isEmpty())
                    <p class="text-muted">No programmes available in this school.</p>
                @else
                    <ul class="list-group">
                        @foreach ($school->programmes->sortBy('programme_code') as $programme)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $programme->programme_code }}</strong> – {{ $programme->name }}
                                </div>
                                <div>
                                    <a href="{{ route('programmes.show', $programme) }}"
                                        class="btn btn-info btn-sm me-1">View Scheduling</a>
                                    {{-- <a href="{{ route('programmes.edit', $programme) }}"
                                        class="btn btn-warning btn-sm me-1">Edit</a>
                                    <form action="{{ route('programmes.destroy', $programme) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this programme?')">
                                            Delete
                                        </button>
                                    </form> --}}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
@endsection
