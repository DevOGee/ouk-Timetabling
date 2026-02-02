@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">{{ isset($specialisation) ? 'Edit' : 'Add New' }} Specialisation</h5>
                </div>
                <div class="card-body">
                    @if(isset($specialisation))
                        <form action="{{ route('admin.specialisations.update', $specialisation) }}" method="POST">
                            @method('PUT')
                    @else
                        <form action="{{ route('admin.specialisations.store') }}" method="POST">
                    @endif
                        @csrf
                        
                        <div class="mb-3">
                            <label for="programme_id" class="form-label">Programme</label>
                            <select name="programme_id" id="programme_id" class="form-select @error('programme_id') is-invalid @enderror" required>
                                <option value="">-- Select Programme --</option>
                                @foreach($programmes as $programme)
                                    <option value="{{ $programme->id }}" 
                                        {{ (old('programme_id', $specialisation->programme_id ?? $selectedProgrammeId ?? '') == $programme->id) ? 'selected' : '' }}>
                                        {{ $programme->name }} ({{ $programme->programme_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('programme_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Note: Only programmes with "Has Specialisations" enabled are listed.</div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Specialisation Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $specialisation->name ?? '') }}" 
                                   required placeholder="e.g. Cloud Computing">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="code" class="form-label">Specialisation Code (Optional)</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" 
                                   value="{{ old('code', $specialisation->code ?? '') }}" 
                                   placeholder="e.g. CC">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.specialisations.index', ['programme_id' => $specialisation->programme_id ?? $selectedProgrammeId ?? '']) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> {{ isset($specialisation) ? 'Update' : 'Create' }} Specialisation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
