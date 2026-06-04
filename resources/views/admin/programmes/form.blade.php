@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($programme) ? 'Edit' : 'Add New' }} Programme</h5>
                </div>
                <div class="card-body">
                    @if(isset($programme))
                        <form action="{{ route('admin.programmes.update', $programme) }}" method="POST">
                            @method('PUT')
                    @else
                        <form action="{{ route('admin.programmes.store') }}" method="POST">
                    @endif
                        @csrf
                        
                        <div class="mb-3">
                            <label for="programme_code" class="form-label">Programme Code</label>
                            <input type="text" class="form-control @error('programme_code') is-invalid @enderror" 
                                   id="programme_code" name="programme_code" 
                                   value="{{ old('programme_code', $programme->programme_code ?? '') }}" 
                                   required>
                            @error('programme_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">A unique code to identify this programme (e.g., BSC-IT, BBA, etc.)</div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Programme Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $programme->name ?? '') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="school_id" class="form-label">School</label>
                            <select name="school_id" id="school_id" class="form-select @error('school_id') is-invalid @enderror" required>
                                <option value="">-- Select School --</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" 
                                        {{ (old('school_id', $programme->school_id ?? '') == $school->id) ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.programmes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> {{ isset($programme) ? 'Update' : 'Create' }} Programme
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
