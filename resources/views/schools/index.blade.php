@extends('layouts.app')

@section('title', 'Schools')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Schools</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('schools.create') }}" class="mb-3 btn btn-primary">Add School</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schools as $school)
                    <tr>
                        <td>{{ $school->id }}</td>
                        <td>{{ $school->name }}</td>
                        <td>
                            <a href="{{ route('schools.show', $school) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('schools.edit', $school) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('schools.destroy', $school) }}" method="POST" class="d-inline">
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
    </div>
@endsection
