@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Academic Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['academicSessions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar3 fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Programmes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['programmes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-journal-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Course Units</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['courseUnits'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Instructors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['instructors'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Session and Recent Sessions -->
    <div class="row">
        <!-- Current Session -->
        @if($currentSession)
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Current Academic Session</h6>
                </div>
                <div class="card-body">
                    <h4 class="mb-3">{{ $currentSession->name }}</h4>
                    <p class="mb-1"><strong>Duration:</strong> {{ $currentSession->start_date->format('M d, Y') }} - {{ $currentSession->end_date->format('M d, Y') }}</p>
                    <p class="mb-1"><strong>Status:</strong> 
                        <span class="badge bg-{{ $currentSession->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($currentSession->status) }}
                        </span>
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('admin.academic-sessions.show', $currentSession) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-right-circle me-1"></i> View Session
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Sessions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Academic Sessions</h6>
                    <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse($recentSessions as $session)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $session->name }}</h6>
                                    <small>{{ $session->status === 'active' ? 'Active' : 'Inactive' }}</small>
                                </div>
                                <p class="mb-1">{{ $session->start_date->format('M d, Y') }} - {{ $session->end_date->format('M d, Y') }}</p>
                                <small><a href="{{ route('admin.academic-sessions.show', $session) }}">View Details</a></small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No academic sessions found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
