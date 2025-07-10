<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('ouk-logo-fav.png') }}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 0.5px;
            margin: 15px 0 10px 0;
            text-align: left;
            position: relative;
            padding-bottom: 12px;
        }
        
        .menu-title:after {
            content: '';
            position: absolute;
            left: 20px;
            right: 20px;
            bottom: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
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

        /* Submenu Item Styles */
        .submenu-item {
            position: relative;
            margin-bottom: 2px;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            padding: 0.6rem 1.5rem 0.6rem 3.5rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            position: relative;
        }

        .submenu-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            padding-left: 3.7rem;
        }

        .submenu-link i {
            margin-right: 10px;
            font-size: 0.9em;
            width: 20px;
            text-align: center;
            opacity: 0.8;
        }

        .submenu-link:hover i {
            opacity: 1;
            transform: translateX(2px);
        }

        /* Active state for submenu items */
        .submenu-item.active .submenu-link {
            background: rgba(3, 181, 170, 0.15);
            color: var(--secondary);
            border-left-color: var(--secondary);
        }

        /* Add a subtle indicator for the active submenu item */
        .submenu-item.active .submenu-link:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--secondary);
            border-radius: 0 3px 3px 0;
        }

        /* Animation for submenu items */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-5px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .submenu-item {
            animation: fadeIn 0.3s ease-out forwards;
            opacity: 0;
        }

        /* Delay the animation for each submenu item */
        .submenu-item:nth-child(1) { animation-delay: 0.05s; }
        .submenu-item:nth-child(2) { animation-delay: 0.1s; }
        .submenu-item:nth-child(3) { animation-delay: 0.15s; }
        .submenu-item:nth-child(4) { animation-delay: 0.2s; }

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

        /* Main Content Wrapper */
        .main-content-wrapper {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            background-color: var(--content-bg);
            position: relative;
        }
        
        /* Content Wrapper */
        .content-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* Main Content */
        .main-content {
            flex: 1 0 auto;
            width: 100%;
            padding-bottom: 2rem;
        }
        
        .container-fluid {
            padding: 0 2rem;
            max-width: 100%;
        }

        .main-content-wrapper.expanded {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }
        
        .content-wrapper {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            width: 100%;
            box-sizing: border-box;
            flex: 1;
            margin-bottom: 1rem;
        }
        
        .main-footer {
            width: 100%;
            background: #fff;
            border-top: 1px solid #e9ecef;
            padding: 1.5rem 2rem;
            margin-top: auto;
            flex-shrink: 0;
            position: relative;
            z-index: 10;
        }

        /* Top Bar */
        .top-bar {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: var(--topbar-height);
            background: var(--topbar-bg);
            box-shadow: var(--topbar-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 900;
            transition: left 0.3s ease;
            border-bottom: 1px solid #e9ecef;
        }
        
        /* Add padding to the main content to account for fixed top bar */
        .main-content-wrapper {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        /* Adjust content wrapper padding */
        .content-wrapper {
            padding: 2rem;
            min-height: calc(100vh - var(--topbar-height));
        }
        
        .sidebar.collapsed ~ .main-content-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }
        
        .sidebar.collapsed ~ .main-content-wrapper .top-bar {
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
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        /* Ensure cards have proper spacing */
        .card {
            margin-bottom: 1.5rem;
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
            text-align: left;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
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
            @auth
            @if(auth()->user()->hasRole(['admin', 'dean']))
            <li class="menu-item {{ request()->routeIs('admin.academic-sessions.index') && !request()->routeIs('admin.academic-sessions.show') ? 'active' : '' }}">
                <a href="{{ route('admin.academic-sessions.index') }}" class="menu-link">
                    <i class="bi bi-calendar-week"></i>
                    <span>Academic Sessions</span>
                </a>
            </li>
            @endif
            @endauth

            {{-- Course Mapping - Admin, Dean, and Timetabler --}}
            @auth
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
            @endauth

            @auth
            @if(auth()->user()->hasRole(['admin', 'dean']))
            <li class="menu-item {{ request()->routeIs('admin.timetables.*') ? 'active' : '' }}">
                <a href="{{ route('admin.timetables.manage') }}" class="menu-link">
                    <i class="bi bi-calendar-check"></i>
                    <span>Timetable Management</span>
                </a>
            </li>
            @endif
            @endauth

            @auth
            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler']))
            <li class="menu-title">Setup</li>
            @endif
            @endauth

            {{-- Courses - All roles except maybe basic instructor --}}
            @auth
            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler', 'instructor']))
            <li class="menu-item {{ request()->routeIs('course_units.*') ? 'active' : '' }}">
                <a href="{{ route('course_units.index') }}" class="menu-link">
                    <i class="bi bi-book"></i>
                    <span>Courses</span>
                </a>
            </li>
            @endif
            @endauth

            {{-- Instructors - Admin, Dean, and Timetabler --}}
            @auth
            @if(auth()->user()->hasRole(['admin', 'dean', 'timetabler']))
            <li class="menu-item {{ request()->routeIs('instructors.*') ? 'active' : '' }}">
                <a href="{{ route('instructors.index') }}" class="menu-link">
                    <i class="bi bi-people"></i>
                    <span>Instructors</span>
                </a>
            </li>
            @endif
            @endauth

            {{-- Programmes - Admin and Dean only --}}
            @auth
            @if(auth()->user()->hasRole(['admin', 'dean']))
            <li class="menu-item {{ request()->routeIs('programmes.*') ? 'active' : '' }}">
                <a href="{{ route('programmes.index') }}" class="menu-link">
                    <i class="bi bi-building"></i>
                    <span>Programmes</span>
                </a>
            </li>
            @endif
            @endauth

            {{-- Users - Admin only --}}
            @auth
            @if(auth()->user()->hasRole('admin'))
            <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a href="{{ route('admin.users.index') }}" class="menu-link">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </li>
            @endif
            @endauth
           

            {{-- Academic Setup - Admin and Dean only --}}
            @auth
            @if(auth()->user()->hasRole(['admin', 'dean']))
            
            <li class="menu-item has-submenu">
                <a href="#" class="menu-link">
                    <i class="bi bi-gear"></i>
                    <span>Academic Setup</span>
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
            @endauth
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper">
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
                @auth
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::check() && Auth::user())
                        <div class="user-avatar me-2 position-relative">
                            @php
                                $user = Auth::user();
                                $avatarContent = strtoupper(substr($user->name, 0, 1));
                                
                                // Check if image exists in storage
                                $hasImage = false;
                                $imageUrl = null;
                                
                                if ($user->image_path) {
                                    // Try different possible paths
                                    $possiblePaths = [
                                        $user->image_path,
                                        'storage/' . $user->image_path,
                                        'storage/app/public/' . $user->image_path,
                                        'storage/app/public/profile-photos/' . basename($user->image_path)
                                    ];
                                    
                                    foreach ($possiblePaths as $path) {
                                        if (file_exists(public_path($path))) {
                                            $imageUrl = asset($path);
                                            $hasImage = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if($hasImage && $imageUrl)
                                <img src="{{ $imageUrl }}" 
                                     alt="{{ $user->name }}" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.style.display='none'; this.parentNode.innerHTML='{$avatarContent}';">
                            @else
                                <span class="d-flex align-items-center justify-content-center w-100 h-100">
                                    {{ $avatarContent }}
                                </span>
                            @endif
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="small text-muted">
                                {{ $user->roles->first() ? ucfirst($user->roles->first()->name) : 'User' }}
                            </div>
                        </div>
                        @else
                        <div class="user-avatar me-2">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">Guest</div>
                            <div class="small text-muted">Not logged in</div>
                        </div>
                        @endif
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
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="bi bi-person me-2"></i> View Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-pencil me-2"></i> Edit Profile
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
                @endauth
            </div>
        </header>

        <div class="content-wrapper">
            <!-- Main Content Area -->
            <main class="main-content">
                <div class="container-fluid py-4">
                    @yield('content')
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="main-footer">
                <x-footer />
            </footer>
        </div>
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
