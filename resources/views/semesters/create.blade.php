@extends('layouts.app')

@section('title', 'Add Semester')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Add Semester</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('semesters.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Semester Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('semesters.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
