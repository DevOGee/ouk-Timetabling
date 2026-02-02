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
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select" name="department_id" id="department_id">
                    <option value="">Select Department (Optional)</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="color" class="form-label">Color (optional)</label>
                <div class="d-flex">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color1" value="#3BB994">
                        <label class="form-check-label" for="color1">
                            <span
                                style="background-color: #3BB994; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color2" value="#279EFF">
                        <label class="form-check-label" for="color2">
                            <span
                                style="background-color: #279EFF; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color3" value="#D83F31">
                        <label class="form-check-label" for="color3">
                            <span
                                style="background-color: #D83F31; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color4" value="#FF7F50">
                        <label class="form-check-label" for="color4">
                            <span
                                style="background-color: #FF7F50; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color5" value="#6C3428">
                        <label class="form-check-label" for="color5">
                            <span
                                style="background-color: #6C3428; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="color" id="color6" value="#940B92">
                        <label class="form-check-label" for="color6">
                            <span
                                style="background-color: #940B92; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Course Unit</button>
        </form>
    </div>
@endsection
