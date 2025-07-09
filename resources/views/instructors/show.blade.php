@extends('layouts.app')

@section('title', 'Lecturer Profile')

@section('content')
<div class="container-fluid px-4">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Lecturer Profile</h2>
        <a href="{{ route('instructors.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <!-- Profile Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Left Column: Profile Info -->
                <div class="col-md-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $lecturer->image_path ? asset('storage/' . $lecturer->image_path) : asset('images/default-avatar.png') }}"
                            class="rounded-circle border border-4 border-primary" 
                            style="width: 180px; height: 180px; object-fit: cover;"
                            alt="{{ $lecturer->name }}">
                        <span class="position-absolute bottom-0 end-0 bg-{{ $lecturer->status === 'active' ? 'success' : 'secondary' }} rounded-circle p-2" 
                              style="width: 24px; height: 24px;"
                              title="{{ ucfirst($lecturer->status) }}">
                        </span>
                    </div>
                    
                    <h4 class="mb-1">{{ optional($lecturer->title)->abbreviation ?? '' }} {{ $lecturer->name }}</h4>
                    <p class="text-muted mb-3">{{ $lecturer->email }}</p>
                    
                    @if($lecturer->phone)
                        <p class="mb-2">
                            <i class="bi bi-telephone me-2 text-primary"></i> {{ $lecturer->phone }}
                        </p>
                    @endif
                    
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a href="{{ route('instructors.edit', $lecturer) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <form action="{{ route('instructors.destroy', $lecturer) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to deactivate this lecturer?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-person-x me-1"></i> Deactivate
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Right Column: Details -->
                <div class="col-md-8">
                    <h5 class="mb-3 text-primary">Lecturer Details</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-3">
                                    <h6 class="card-subtitle mb-2 text-muted">Title</h6>
                                    <p class="card-text">{{ optional($lecturer->title)->name ?? 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body p-3">
                                    <h6 class="card-subtitle mb-2 text-muted">Status</h6>
                                    <p class="card-text">
                                        <span class="badge bg-{{ $lecturer->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($lecturer->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($lecturer->school)
                    <div class="card bg-light mb-3">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-2 text-muted">School/Department</h6>
                            <p class="card-text">{{ $lecturer->school->name }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <h6 class="card-subtitle mb-2 text-muted">Member Since</h6>
                            <p class="card-text">{{ $lecturer->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Courses Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Assigned Courses</h5>
        </div>
        <div class="card-body p-0">
            @if ($lecturer->courseUnitProgrammeMappings->isEmpty())
                <div class="text-center p-5">
                    <div class="text-muted mb-3">
                        <i class="bi bi-journal-x" style="font-size: 3rem;"></i>
                    </div>
                    <h5>No courses assigned yet</h5>
                    <p class="text-muted">This lecturer is not assigned to any courses.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Programme</th>
                                <th>Year & Semester</th>
                                <th>Schedule</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lecturer->courseUnitProgrammeMappings as $mapping)
                                <tr>
                                    <td class="fw-bold">{{ $mapping->courseUnit?->code ?? '-' }}</td>
                                    <td>{{ $mapping->courseUnit?->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ $mapping->programme?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $mapping->yearOfStudy?->name ?? 'N/A' }} • {{ $mapping->semester?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($mapping->day)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                {{ $mapping->day->name ?? 'Not Scheduled' }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                                Not Scheduled
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $mapping->is_active ? 'success' : 'secondary' }}">
                                            {{ $mapping->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .bg-opacity-10 {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
</style>
@endpush
@endsection
