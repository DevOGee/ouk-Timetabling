@extends('layouts.app')

@section('title', 'Edit Year of Study')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Edit Year of Study</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('years_of_study.update', $yearOfStudy) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Year of Study</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $yearOfStudy->name) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('years_of_study.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
