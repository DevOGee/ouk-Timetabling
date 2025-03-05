@extends('layouts.app')

@section('title', 'Edit Lecturer')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Edit Lecturer</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('instructors.update', $lecturer) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title_id" class="form-label">Title</label>
                <select class="form-control" id="title_id" name="title_id" required>
                    <option value="">Select Title</option>
                    @foreach ($titles as $title)
                        <option value="{{ $title->id }}" {{ $lecturer->title_id == $title->id ? 'selected' : '' }}>
                            {{ $title->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $lecturer->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="{{ old('email', $lecturer->email) }}" required>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Profile Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                @if ($lecturer->image_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $lecturer->image_path) }}" alt="Current Image" width="100">
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update Lecturer</button>
            <a href="{{ route('instructors.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
