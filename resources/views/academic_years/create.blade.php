@extends('layouts.app')

@section('title', 'Add Academic Year')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Add Academic Year</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('academic_years.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="year" class="form-label">Academic Year (e.g., 2023/2024)</label>
                <input type="text" class="form-control" id="year" name="year" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('academic_years.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
