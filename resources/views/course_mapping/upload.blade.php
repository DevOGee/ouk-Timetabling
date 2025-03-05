@extends('layouts.app')

@section('title', 'Bulk Upload Course Mapping')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Bulk Upload Course Mappings</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('course_mapping.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Choose CSV File</label>
                <input type="file" name="file" class="form-control" required accept=".csv">
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <a href="{{ route('course_mapping.sample') }}" class="mt-3 btn btn-secondary">Download Sample CSV</a>
    </div>
@endsection
