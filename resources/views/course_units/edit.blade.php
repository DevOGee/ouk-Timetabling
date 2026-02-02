@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Edit Course Unit</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('course_units.update', $course_unit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="code" class="form-label">Course Code</label>
                <input type="text" class="form-control" name="code" id="code" required
                    value="{{ old('code', $course_unit->code) }}">
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Course Name</label>
                <input type="text" class="form-control" name="name" id="name" required
                    value="{{ old('name', $course_unit->name) }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select" name="department_id" id="department_id">
                    <option value="">Select Department (Optional)</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ (old('department_id', $course_unit->department_id) == $department->id) ? 'selected' : '' }}>
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
                        <input class="form-check-input" type="radio" name="color" id="color1" value="#3BB994"
                            {{ old('color', $course_unit->color) == '#3BB994' ? 'checked' : '' }}>
                        <label class="form-check-label" for="color1">
                            <span
                                style="background-color: #3BB994; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color2" value="#279EFF"
                            {{ old('color', $course_unit->color) == '#279EFF' ? 'checked' : '' }}>
                        <label class="form-check-label" for="color2">
                            <span
                                style="background-color: #279EFF; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color3" value="#D83F31"
                            {{ old('color', $course_unit->color) == '#D83F31' ? 'checked' : '' }}>
                        <label class="form-check-label" for="color3">
                            <span
                                style="background-color: #D83F31; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color4" value="#FF7F50"
                            {{ old('color', $course_unit->color) == '#FF7F50' ? 'checked' : '' }}>
                        <label class="form-check-label" for="color4">
                            <span
                                style="background-color: #FF7F50; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="color" id="color5" value="#6C3428"
                            {{ old('color', $course_unit->color) == '#6C3428' ? 'checked' : '' }}>
                        <label class="form-check-label" for="color5">
                            <span
                                style="background-color: #6C3428; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="color" id="color6" value="#940B92"
                            {{ old('color', $course_unit->color) == '#940B92' ? 'checked' : '' }}>
                        <label class="form-check-label" for="color6">
                            <span
                                style="background-color: #940B92; width: 30px; height: 30px; display: inline-block; border: 1px solid #ccc;"></span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Course Unit</button>
            <a href="{{ route('course_units.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
