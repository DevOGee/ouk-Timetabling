@extends('layouts.app')

@section('title', 'Programmes')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Programmes</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('programmes.create') }}" class="mb-3 btn btn-primary">Add Programme</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Programme Name</th>
                    <th>School</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($programmes as $programme)
                    <tr>
                        <td>{{ $programme->id }}</td>
                        <td>{{ $programme->programme_code }} - {{ $programme->name }}</td>
                        <td>{{ $programme->school->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('programmes.show', $programme) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('programmes.edit', $programme) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('programmes.destroy', $programme) }}" method="POST" class="d-inline">
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
