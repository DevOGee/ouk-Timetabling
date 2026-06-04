@extends('layouts.app')

@section('title', 'Add Year of Study')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Add Year of Study</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('years_of_study.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Year of Study</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('years_of_study.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
