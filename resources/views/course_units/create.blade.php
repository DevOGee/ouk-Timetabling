@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Add Course Unit</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('course_units.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="code" class="form-label">Course Code</label>
                <input type="text" class="form-control" name="code" id="code" required value="{{ old('code') }}">
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Course Name</label>
                <input type="text" class="form-control" name="name" id="name" required
                    value="{{ old('name') }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="color" class="form-label">Color (optional)</label>
                <input type="color" class="form-control form-control-color" name="color" id="color"
                    value="{{ old('color', '#000000') }}">
            </div>

            <button type="submit" class="btn btn-primary">Save Course Unit</button>
        </form>
    </div>
@endsection
