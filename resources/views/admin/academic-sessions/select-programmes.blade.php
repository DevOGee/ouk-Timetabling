@extends('layouts.admin')

@section('title', 'Select Programmes - ' . $academicSession->name)

@section('content')
<div class="container">
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-8">
            <h2>Select Programmes for {{ $academicSession->name }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Academic Session
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.academic-sessions.programmes.store', $academicSession) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label">Select Programmes</label>
                    <div class="border rounded p-3" style="max-height: 500px; overflow-y: auto;">
                        @foreach($programmes->groupBy('school.name') as $school => $schoolProgrammes)
                            <div class="mb-3">
                                <h5>{{ $school }}</h5>
                                @foreach($schoolProgrammes as $programme)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                            name="programmes[]" 
                                            value="{{ $programme->id }}"
                                            id="programme-{{ $programme->id }}"
                                            {{ in_array($programme->id, $selectedProgrammeIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="programme-{{ $programme->id }}">
                                            {{ $programme->name }} ({{ $programme->programme_code }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.academic-sessions.show', $academicSession) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Programmes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
