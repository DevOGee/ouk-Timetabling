@extends('layouts.app')

@section('title', 'Semesters')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Semesters</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('semesters.create') }}" class="mb-3 btn btn-primary">Add Semester</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Semester Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($semesters as $semester)
                    <tr>
                        <td>{{ $semester->id }}</td>
                        <td>{{ $semester->name }}</td>
                        <td>
                            <a href="{{ route('semesters.edit', $semester) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('semesters.destroy', $semester) }}" method="POST" class="d-inline">
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
