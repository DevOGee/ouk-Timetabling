<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('ouk-logo-fav.png') }}" type="image/png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        :root {
            /* Layout */
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 65px;
            
            /* Brand Colors */
            --primary: #FF7F50;      /* Coral */
            --secondary: #03B5AA;    /* Teal */
            --dark: #023436;         /* Dark Teal */
            
            /* Sidebar */
            --sidebar-bg: var(--dark);
            --sidebar-header-bg: var(--dark);
            --sidebar-text: rgba(255, 255, 255, 0.9);
            --sidebar-hover: rgba(3, 181, 170, 0.1);
            --sidebar-active: var(--secondary);
            --sidebar-submenu-bg: rgba(0, 0, 0, 0.2);
            --sidebar-border: rgba(255, 255, 255, 0.1);
            
            /* Top Bar */
            --topbar-bg: #ffffff;
            --topbar-text: var(--dark);
            --topbar-hover: #f8f9fa;
            --topbar-shadow: 0 2px 10px rgba(2, 52, 54, 0.1);
            
            /* Content */
            --content-bg: #f8fafa;
            --card-bg: #ffffff;
            --border-color: #e0e0e0;
            
            /* Text */
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            
            /* Status Colors */
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            background-color: var(--content-bg);
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
            overflow: hidden;
        }
        
        .sidebar.collapsed .sidebar-logo-img {
            width: 32px;
            height: auto;
            margin: 0 auto;
        }

        .sidebar-header {
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: var(--topbar-height);
        }
        
        .topbar-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .topbar-logo img {
            height: 40px;
        }

        .sidebar-logo {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .sidebar-logo:hover {
            opacity: 0.9;
        }

        .sidebar.collapsed .sidebar-logo span {
            display: none;
        }

        .sidebar.collapsed .sidebar-logo i {
            font-size: 1.5rem;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--sidebar-text);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }
        
        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .bg-opacity-10 {
            background-color: rgba(13, 110, 253, 0.1) !important;
        }
        
        .stat-icon {
            font-size: 1.5rem;
            width: 1.5em;
            height: 1.5em;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .rounded-circle {
            border-radius: 50% !important;
            width: 3.5rem;
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar.collapsed .toggle-btn {
            margin: 0 auto;
        }

        .sidebar-menu {
            padding: 0;
            list-style: none;
            margin: 0;
        }

        .menu-title {
            padding: 10px 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .menu-title {
            display: none;
        }

        .menu-item {
            position: relative;
        }

        .menu-item > .menu-link {
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 0.85rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            min-height: auto;
            box-sizing: border-box;
            width: 100%;
        }
        
        .menu-link:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        
        .menu-link i {
            font-size: 1.5rem;
            width: 32px;
            text-align: center;
            margin-right: 16px;
            flex-shrink: 0;
        }
        
        .menu-link span {
            font-size: 1rem;
            line-height: 1.3;
            text-align: left;
            flex-grow: 1;
            padding: 0.5rem 0;
        }
        
        .menu-link .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            margin-left: 0.5rem;
        }

        /* Active state for exact route matches only */
        .menu-item.active > .menu-link:not(.collapsed) {
            background: rgba(255, 127, 80, 0.1);
            color: var(--primary);
            font-weight: 600;
            border-left: 3px solid var(--primary);
        }
        
        .menu-item.active > .menu-link:not(.collapsed) i {
            color: var(--primary);
        }
        
        /* For parent items that contain active children */
        .menu-item.menu-open > .menu-link {
            background: rgba(3, 181, 170, 0.1);
            color: var(--secondary);
            border-left: 3px solid var(--secondary);
        }
        
        .menu-item.menu-open > .menu-link i {
            color: var(--secondary);
        }

        .sidebar.collapsed .menu-link span {
            display: none;
        }
        
        .sidebar.collapsed .menu-link {
            justify-content: center;
            padding: 1rem 0;
        }
        
        .sidebar.collapsed .menu-link span,
        .sidebar.collapsed .menu-link .badge {
            display: none;
        }
        
        .menu-item {
            width: 100%;
            display: block;
        }
        
        .menu-title {
            padding: 1rem 1rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.5px;
        }

        .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            background: var(--sidebar-submenu-bg);
            display: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .submenu.show,
        .menu-open > .submenu {
            display: block;
        }
        
        /* Remove the old menu-open style as it's replaced by the more specific one above */

        .submenu .menu-link {
            padding-left: 56px;
            font-size: 0.9rem;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .menu-item.has-submenu > .menu-link:after {
            content: '\f282';
            font-family: 'bootstrap-icons';
            margin-left: auto;
            transition: transform 0.3s;
        }

        .menu-item.has-submenu.active > .menu-link:after {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .menu-item.has-submenu > .menu-link:after {
            display: none;
        }

        .sidebar.collapsed .submenu .menu-link {
            padding-left: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 1.5rem;
            min-height: calc(100vh - var(--topbar-height));
            transition: all 0.3s ease;
            background-color: var(--body-bg);
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .content-wrapper {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Top Bar */
        .top-bar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: var(--topbar-height);
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem 0 calc(var(--sidebar-width) + 1.5rem);
            transition: all 0.3s ease;
            border-bottom: 1px solid #e9ecef;
        }
        
        .sidebar.collapsed ~ .top-bar {
            padding-left: calc(var(--sidebar-collapsed-width) + 1.5rem);
        }

        /* Search Bar */
        .search-bar {
            flex: 1;
            max-width: 500px;
            margin: 0 2rem;
        }

        .search-bar .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: #6c757d;
        }

        .search-bar .form-control {
            border-left: none;
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            height: 40px;
        }

        .search-bar .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-menu .dropdown-toggle {
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-menu .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-left: 15px;
            cursor: pointer;
        }

        /* Notifications Dropdown */
        .notification-item {
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .notification-details h6 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .notification-details p {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
            color: #6c757d;
        }

        .notification-details span {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            padding: 0.5rem 0;
            min-width: 280px;
        }

        .dropdown-header {
            padding: 0.75rem 1.25rem;
            background-color: #f8f9fa;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid #e9ecef;
        }

        .dropdown-item {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            color: #212529;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 0.75rem;
            color: #6c757d;
            transition: color 0.2s;
        }

        .dropdown-item:hover, .dropdown-item:focus {
            background-color: #f8f9fa;
            color: #0d6efd;
        }

        .dropdown-item:hover i, .dropdown-item:focus i {
            color: #0d6efd;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            font-size: 1.5rem;
            padding: 0.5rem;
            margin-right: 1rem;
        }

        /* Content Area */
        .content-wrapper {
            flex: 1;
            padding: 25px;
            background-color: var(--content-bg);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .top-bar {
                padding-left: 15px;
            }

            .mobile-menu-btn {
                display: block !important;
                margin-right: 15px;
            }
        }

        /* Active Session Badge */
        .active-session-badge {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            border-radius: 6px;
            font-size: 0.85rem;
            border-left: 3px solid var(--secondary);
            transition: all 0.2s ease;
        }
        
        .active-session-badge:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .active-session-badge i {
            margin-right: 5px;
        }

        .sidebar.collapsed .active-session-badge {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="toggle-btn" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>
        </div>

        @if(isset($activeSession))
        <div class="active-session-badge">
            <i class="bi bi-calendar-check"></i>
            <span>{{ $activeSession->name }}</span>
            @if($activeSession->activeCurriculum)
                <div class="small mt-1">{{ $activeSession->activeCurriculum->name }}</div>
            @endif
        </div>
        @endif

        <ul class="sidebar-menu">
            <li class="menu-title">Main</li>
            
            {{-- Dashboard - Visible to all authenticated users --}}
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Timetables - Visible to all authenticated users --}}
            <li class="menu-item {{ request()->routeIs('timetable.*') ? 'active' : '' }}">
                <a href="{{ route('timetable.index') }}" class="menu-link">
                    <i class="bi bi-calendar3"></i>
                    <span>Timetables</span>
                </a>
            </li>

            {{-- Academic Sessions - Admin and Dean only --}}
            @if(auth()->user()->hasRole(['admin', 'dean']))
            <li class="menu-item {{ request()->routeIs('admin.academic-sessions.index') && !request()->routeIs('admin.academic-sessions.show') ? 'active' : '' }}">
                <a href="{{ route('admin.academic-sessions.index') }}" class="menu-link">
                    <i class="bi bi-calendar-week"></i>
                    <span>Academic Sessions</span>
                </a>
            </li>
            @endif

            {{-- Course Mapping - Admin, Dean, and Timetabler --}}
            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler']))
            <li class="menu-item {{ request()->routeIs('admin.academic-sessions.show') ? 'active' : '' }}">
                <a href="{{ isset($activeSession) ? url('/admin/academic-sessions/' . $activeSession->id) : '#' }}" class="menu-link {{ !isset($activeSession) ? 'disabled' : '' }}" {{ !isset($activeSession) ? 'aria-disabled="true"' : '' }}>
                    <i class="bi bi-diagram-3"></i>
                    <span>Course Mapping</span>
                    @if(!isset($activeSession))
                        <span class="badge bg-warning mt-1">No active session</span>
                    @endif
                </a>
            </li>
            @endif

            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler']))
            <li class="menu-title">Setup</li>
            @endif

            {{-- Courses - All roles except maybe basic instructor --}}
            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler', 'instructor']))
            <li class="menu-item {{ request()->routeIs('course_units.*') ? 'active' : '' }}">
                <a href="{{ route('course_units.index') }}" class="menu-link">
                    <i class="bi bi-book"></i>
                    <span>Courses</span>
                </a>
            </li>
            @endif

            {{-- Instructors - Admin, Dean, and Timetabler --}}
            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler']))
            <li class="menu-item {{ request()->routeIs('instructors.*') ? 'active' : '' }}">
                <a href="{{ route('instructors.index') }}" class="menu-link">
                    <i class="bi bi-people"></i>
                    <span>Instructors</span>
                </a>
            </li>
            @endif

            {{-- Programmes - Admin and Dean only --}}
            @if(auth()->user()->hasRole(['admin', 'dean']))
            <li class="menu-item {{ request()->routeIs('programmes.*') ? 'active' : '' }}">
                <a href="{{ route('programmes.index') }}" class="menu-link">
                    <i class="bi bi-building"></i>
                    <span>Programmes</span>
                </a>
            </li>
            @endif

            {{-- Users - Admin only --}}
            {{-- Temporarily disabled until user management is implemented --}}
            @if(auth()->user()->hasRole('admin'))
            <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <i class="bi bi-people"></i>
                    <span>Users (Coming Soon)</span>
                </a>
            </li>
            @endif
           

            {{-- Academic Setup - Admin and Dean only --}}
            @if(auth()->user()->hasRole(['admin', 'dean']))
            <li class="menu-item has-submenu">
                <a href="#" class="menu-link">
                    <i class="bi bi-gear"></i>
                    <span>Academic Setup</span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <ul class="submenu">
                    {{-- Schools - Using non-admin route since it's defined at root --}}
                    <li class="submenu-item {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                        <a href="{{ route('schools.index') }}" class="submenu-link">
                            <i class="bi bi-building me-2"></i>Schools
                        </a>
                    </li>
                    
                    {{-- Programmes - Using admin prefix --}}
                    <li class="submenu-item {{ request()->routeIs('admin.programmes.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.programmes.index') }}" class="submenu-link">
                            <i class="bi bi-journal-bookmark me-2"></i>Programmes
                        </a>
                    </li>
                    
                    {{-- Course Units - Using non-admin route since it's defined at root --}}
                    <li class="submenu-item {{ request()->routeIs('course_units.*') ? 'active' : '' }}">
                        <a href="{{ route('course_units.index') }}" class="submenu-link">
                            <i class="bi bi-journal-text me-2"></i>Course Units
                        </a>
                    </li>
                    
                    {{-- Instructors - Using non-admin route since it's defined at root --}}
                    <li class="submenu-item {{ request()->routeIs('instructors.*') ? 'active' : '' }}">
                        <a href="{{ route('instructors.index') }}" class="submenu-link">
                            <i class="bi bi-person-video3 me-2"></i>Instructors
                        </a>
                    </li>
                    
                    {{-- Semesters - Using non-admin route since it's defined at root --}}
                    <li class="submenu-item {{ request()->routeIs('semesters.*') ? 'active' : '' }}">
                        <a href="{{ route('semesters.index') }}" class="submenu-link">
                            <i class="bi bi-calendar3 me-2"></i>Semesters
                        </a>
                    </li>
                    
                    {{-- Years of Study - Using non-admin route since it's defined at root --}}
                    <li class="submenu-item {{ request()->routeIs('years_of_study.*') ? 'active' : '' }}">
                        <a href="{{ route('years_of_study.index') }}" class="submenu-link">
                            <i class="bi bi-123 me-2"></i>Years of Study
                        </a>
                    </li>
                    
                    {{-- Academic Years - Using non-admin route since it's defined at root --}}
                    <li class="submenu-item {{ request()->routeIs('academic_years.*') ? 'active' : '' }}">
                        <a href="{{ route('academic_years.index') }}" class="submenu-link">
                            <i class="bi bi-calendar-range me-2"></i>Academic Years
                        </a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="topbar-logo me-3">
                    <img src="https://my.ouk.ac.ke/assets/img/logo1-new.png" alt="University Logo" style="height: 40px;">
                </a>
                <button class="btn btn-link text-dark d-lg-none mobile-menu-btn" id="mobileMenuBtn">
                    <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                </button>
                
                <div class="search-bar ms-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search...">
                    </div>
                </div>
            </div>
            
            <div class="user-menu">
                <!-- Notifications Dropdown -->
                <div class="dropdown me-3">
                    <a class="btn btn-link text-dark position-relative p-2" href="#" role="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-end p-0" aria-labelledby="notificationsDropdown" style="min-width: 300px;">
                        <li class="dropdown-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Notifications</h6>
                                <span class="badge bg-primary rounded-pill">3 New</span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="notification-item">
                            <a href="#" class="dropdown-item d-flex align-items-center p-3">
                                <div class="notification-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="notification-details">
                                    <h6 class="mb-1">New Schedule Posted</h6>
                                    <p class="mb-0 text-muted small">Spring 2024 timetable is now available</p>
                                    <span class="text-muted small">2 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="notification-item">
                            <a href="#" class="dropdown-item d-flex align-items-center p-3">
                                <div class="notification-icon bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="notification-details">
                                    <h6 class="mb-1">New Message</h6>
                                    <p class="mb-0 text-muted small">You have 5 unread messages</p>
                                    <span class="text-muted small">5 hours ago</span>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="dropdown-footer text-center py-2">
                            <a href="#" class="text-decoration-none small">View all notifications</a>
                        </li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar me-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <div class="small text-muted">
                                {{ Auth::user()->roles->first() ? ucfirst(Auth::user()->roles->first()->name) : 'User' }}
                            </div>
                        </div>
                        <i class="bi bi-chevron-down ms-2 small"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="min-width: 220px;">
                        <li>
                            <div class="dropdown-header">
                                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                <small class="text-muted">{{ Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.notifications') }}">
                                <i class="bi bi-bell me-2"></i> Notifications
                                <span class="badge bg-primary rounded-pill float-end">3</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i> Settings
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="w-100">
                                @csrf
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="POST">
                                <input type="hidden" name="_redirect" value="{{ route('timetable.index') }}">
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-wrapper">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <x-footer />
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebar');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            
            // Toggle sidebar collapse
            function toggleSidebar() {
                if (!sidebar) return;
                
                sidebar.classList.toggle('collapsed');
                if (mainContent) {
                    mainContent.classList.toggle('expanded');
                }
                
                // Toggle icon
                if (toggleBtn) {
                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        if (sidebar.classList.contains('collapsed')) {
                            icon.classList.remove('bi-chevron-double-left');
                            icon.classList.add('bi-chevron-double-right');
                        } else {
                            icon.classList.remove('bi-chevron-double-right');
                            icon.classList.add('bi-chevron-double-left');
                        }
                    }
                }
                
                // Save state in localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
            
            // Toggle mobile menu
            function toggleMobileMenu() {
                if (sidebar) {
                    sidebar.classList.toggle('show');
                }
            }
            
            // Handle submenu toggle with event delegation
            function handleMenuClick(e) {
                const menuLink = e.target.closest('.menu-link');
                if (!menuLink) return;
                
                const menuItem = menuLink.closest('.has-submenu');
                if (!menuItem) return;
                
                // Only prevent default for dropdown toggles
                if (menuLink.getAttribute('href') === '#') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle the current submenu
                    menuItem.classList.toggle('menu-open');
                    const submenu = menuItem.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = menuItem.classList.contains('menu-open') ? 'block' : 'none';
                    }
                    
                    // Close other open submenus at the same level
                    const parentMenu = menuItem.parentNode;
                    if (parentMenu) {
                        const siblings = parentMenu.querySelectorAll('.has-submenu');
                        siblings.forEach(sibling => {
                            if (sibling !== menuItem) {
                                sibling.classList.remove('menu-open');
                                const otherSubmenu = sibling.querySelector('.submenu');
                                if (otherSubmenu) {
                                    otherSubmenu.style.display = 'none';
                                }
                            }
                        });
                    }
                }
            }
            
            // Initialize event listeners
            if (toggleBtn) {
                toggleBtn.addEventListener('click', toggleSidebar);
            }
            
            if (mobileMenuBtn) {
                mobileMenuBtn.style.display = 'block';
                mobileMenuBtn.addEventListener('click', toggleMobileMenu);
            }
            
            // Use event delegation for menu items
            if (sidebar) {
                sidebar.addEventListener('click', handleMenuClick);
            }
            
            // Initialize submenu states
            function initializeSubmenus() {
                const submenus = document.querySelectorAll('.has-submenu');
                submenus.forEach(menu => {
                    const submenu = menu.querySelector('.submenu');
                    if (submenu) {
                        // Check if this menu should be open by default
                        if (menu.classList.contains('active') || submenu.classList.contains('show')) {
                            menu.classList.add('menu-open');
                            submenu.style.display = 'block';
                        } else {
                            submenu.style.display = 'none';
                        }
                    }
                });
            }
            
            // Check for saved sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                if (sidebar) sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
                
                if (toggleBtn) {
                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('bi-chevron-double-left');
                        icon.classList.add('bi-chevron-double-right');
                    }
                }
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 992 && sidebar && !sidebar.contains(e.target) && 
                    mobileMenuBtn && !mobileMenuBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
            
            // Handle window resize
            function handleResize() {
                if (window.innerWidth > 992 && sidebar) {
                    sidebar.classList.remove('show');
                }
            }
            
            window.addEventListener('resize', handleResize);
            
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Initialize submenus
            initializeSubmenus();

            // Initialize Select2 for instructor search
            if ($.fn.select2) {
                $('.instructor-select').select2({
                    placeholder: "Search Instructor...",
                    allowClear: true,
                    ajax: {
                        url: "{{ route('search.instructors') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return { q: params.term };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(instructor) {
                                    return {
                                        id: instructor.id,
                                        text: (instructor.title ? instructor.title.name + " " : "") + instructor.name
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // Initialize Select2 for regular dropdowns
                $('.select2').select2({
                    placeholder: "Select an option",
                    allowClear: true
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>

</html>
