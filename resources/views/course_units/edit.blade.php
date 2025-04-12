@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Edit Course Unit</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('course_units.update', $course_unit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="code" class="form-label">Course Code</label>
                <input type="text" class="form-control" name="code" id="code" required
                    value="{{ old('code', $course_unit->code) }}">
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Course Name</label>
                <input type="text" class="form-control" name="name" id="name" required
                    value="{{ old('name', $course_unit->name) }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="color" class="form-label">Color (optional)</label>
                <input type="color" class="form-control form-control-color" name="color" id="color"
                    value="{{ old('color', $course_unit->color ?? '#000000') }}">
            </div>

            <button type="submit" class="btn btn-primary">Update Course Unit</button>
            <a href="{{ route('course_units.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
