@extends('layouts.app')

@section('title', 'Edit School')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Edit School</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('schools.update', $school) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">School Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $school->name) }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('schools.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
