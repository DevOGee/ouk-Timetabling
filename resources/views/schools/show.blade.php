@extends('layouts.app')

@section('title', 'School Details')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">{{ $school->name }}</h2>

        <div class="mb-3">
            <a href="{{ route('programmes.create', ['school_id' => $school->id]) }}" class="btn btn-primary">Add Programme</a>
            <a href="{{ route('schools.edit', $school) }}" class="btn btn-warning">Edit School</a>
        </div>

        <h4>Programmes in this School</h4>

        @if ($school->programmes->isEmpty())
            <p>No programmes available for this school.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Programme Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($school->programmes as $programme)
                        <tr>
                            <td>{{ $programme->id }}</td>
                            <td>{{ $programme->name }}</td>
                            <td>
                                <a href="{{ route('programmes.edit', $programme) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('programmes.destroy', $programme) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <a href="{{ route('schools.index') }}" class="btn btn-secondary">Back to Schools</a>
    </div>
@endsection
