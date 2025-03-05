@extends('layouts.app')

@section('title', 'Academic Years')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Academic Years</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('academic_years.create') }}" class="mb-3 btn btn-primary">Add Academic Year</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($academicYears as $year)
                    <tr>
                        <td>{{ $year->id }}</td>
                        <td>{{ $year->year }}</td>
                        <td>
                            <a href="{{ route('academic_years.edit', $year) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('academic_years.destroy', $year) }}" method="POST" class="d-inline">
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
