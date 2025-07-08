@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.academic-sessions.*') ? 'active' : '' }}" 
                           href="{{ route('admin.academic-sessions.index') }}">
                            <i class="bi bi-calendar3 me-2"></i>
                            Academic Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.programmes.*') ? 'active' : '' }}" 
                           href="{{ route('admin.programmes.index') }}">
                            <i class="bi bi-journal-text me-2"></i>
                            Programmes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" 
                           href="{{ route('course_units.index') }}">
                            <i class="bi bi-book me-2"></i>
                            Course Units
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" 
                           href="{{ route('instructors.index') }}">
                            <i class="bi bi-people me-2"></i>
                            Lecturers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" 
                           href="#">
                            <i class="bi bi-building me-2"></i>
                            Rooms (Coming Soon)
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-white bg-primary rounded" 
                           href="{{ route('admin.academic-sessions.programmes.scheduling.show', [session('current_academic_session_id') ?? 1, 1]) }}">
                            <i class="bi bi-calendar-plus me-2"></i>
                            Timetable Scheduling
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">@yield('title', 'Admin Dashboard')</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    @yield('actions')
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@push('styles')
<style>
    .sidebar {
        position: fixed;
        top: 56px; /* Height of navbar */
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 48px 0 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }

    .sidebar .nav-link {
        font-weight: 500;
        color: #adb5bd;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }

    .sidebar .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar .nav-link.active {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .sidebar .nav-link i {
        margin-right: 4px;
        color: #6c757d;
    }

    .sidebar .nav-link:hover i,
    .sidebar .nav-link.active i {
        color: #fff;
    }

    main {
        padding-top: 1.5rem;
    }

    .navbar-brand {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        font-size: 1.2rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Enable Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
</script>
@stack('page-scripts')
@endpush
@endsection
