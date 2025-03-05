@extends('layouts.app')

@section('title', 'Upload Course Units')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Upload Course Units</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('course_units.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Choose CSV File</label>
                <input type="file" name="file" class="form-control" required accept=".csv">
            </div>
            <button type="submit" class="mt-3 btn btn-primary">Upload</button>
            <a href="{{ route('course_units.sample') }}" class="mt-3 btn btn-secondary">Download Sample CSV</a>
        </form>


    </div>
@endsection
