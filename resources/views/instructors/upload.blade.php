@extends('layouts.app')

@section('title', 'Upload Lecturers')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Bulk Upload Lecturers</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('instructors.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Upload CSV File</label>
                <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
            </div>

            <button type="submit" class="btn btn-success">Import</button>
            <a href="{{ route('instructors.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
