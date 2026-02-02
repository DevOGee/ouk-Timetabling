@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Department</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="school_id" class="form-label">School</label>
                            <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                <option value="">-- Select School --</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ (old('school_id', $department->school_id) == $school->id) ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Department Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $department->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="code" class="form-label">Department Code (Optional)</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" 
                                   value="{{ old('code', $department->code) }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
