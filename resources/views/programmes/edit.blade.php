@extends('layouts.app')

@section('title', 'Edit Programme')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Edit Programme</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('programmes.update', $programme) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="school_id" class="form-label">School</label>
                <select class="form-control" id="school_id" name="school_id" required>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}" {{ $programme->school_id == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="programme_code">Programme Code</label>
                <input type="text" name="programme_code" class="form-control" value="{{ $programme->programme_code }}"
                    required>
            </div>


            <div class="mb-3">
                <label for="name" class="form-label">Programme Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $programme->name) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('programmes.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
