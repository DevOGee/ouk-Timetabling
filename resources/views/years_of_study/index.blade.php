@extends('layouts.app')

@section('title', 'Years of Study')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Years of Study</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('years_of_study.create') }}" class="mb-3 btn btn-primary">Add Year of Study</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Year of Study</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($yearsOfStudy as $yearOfStudy)
                    <tr>
                        <td>{{ $yearOfStudy->id }}</td>
                        <td>{{ $yearOfStudy->name }}</td>
                        <td>
                            <a href="{{ route('years_of_study.edit', $yearOfStudy) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('years_of_study.destroy', $yearOfStudy) }}" method="POST"
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
    </div>
@endsection
