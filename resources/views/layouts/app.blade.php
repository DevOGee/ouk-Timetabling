<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('ouk-logo-fav.png') }}" type="image/png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
</head>

<body>
    <style>
        .navbar {
            padding: 12px 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.4rem;
        }

        .navbar-nav .nav-item {
            margin-left: 15px;
        }

        .navbar-nav .nav-link {
            font-size: 1rem;
            padding: 8px 12px;
            transition: 0.3s;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu .dropdown-item {
            padding: 10px 15px;
            font-size: 14px;
        }

        a {
            text-decoration: none;
        }
    </style>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Brand Logo -->
            <a class="text-white navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="fas fa-clock"></i> University Timetable
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Timetables (Home) -->
                    <li class="nav-item">
                        <a class="text-white nav-link" href="{{ route('timetable.index') }}">
                            <i class="fas fa-home"></i> Timetables
                        </a>
                    </li>

                    <!-- Course Mapping -->
                    <li class="nav-item">
                        <a class="text-white nav-link" href="{{ route('curriculum.index') }}">
                            <i class="fas fa-project-diagram"></i> Curriculum Setup
                        </a>
                    </li>

                    <!-- Scheduling -->
                    <li class="nav-item">
                        <a class="text-white nav-link" href="{{ route('programmes.index') }}">
                            <i class="fas fa-calendar-alt"></i> Scheduling
                        </a>
                    </li>

                    <!-- Setup Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="text-white nav-link dropdown-toggle" href="#" id="setupDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-cogs"></i> Setup
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('course_units.index') }}"><i
                                        class="fas fa-book"></i> Courses</a></li>
                            <li><a class="dropdown-item" href="{{ route('instructors.index') }}"><i
                                        class="fas fa-user-tie"></i> Instructors</a></li>
                            <li><a class="dropdown-item" href="{{ route('semesters.index') }}"><i
                                        class="fas fa-layer-group"></i> Semesters</a></li>
                            <li><a class="dropdown-item" href="{{ route('years_of_study.index') }}"><i
                                        class="fas fa-graduation-cap"></i> Years of Study</a></li>
                            <li><a class="dropdown-item" href="{{ route('academic_years.index') }}"><i
                                        class="fas fa-university"></i> Academic Years</a></li>
                            <li><a class="dropdown-item" href="{{ route('schools.index') }}"><i
                                        class="fas fa-school"></i> Schools</a></li>
                        </ul>
                    </li>

                    <!-- Profile Dropdown -->
                    @auth
                        <li class="nav-item dropdown">
                            <a class="text-white nav-link dropdown-toggle" href="#" id="profileDropdown"
                                role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                            class="fas fa-user-edit"></i> Edit Profile</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.notifications') }}"><i
                                            class="fas fa-bell"></i> Notifications</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
            </div>
        </div>
    </nav>



    {{-- <div class="container mt-4"> --}}
    @yield('content')
    {{-- </div> --}}

    <!-- resources/views/components/footer.blade.php -->
    <footer class="py-4 text-white bg-dark">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-md-4">
                    <img src="https://somasold.ouk.ac.ke/ouk_logo.png" alt="Open University of Kenya Logo"
                        class="img-fluid" style="max-width: 300px;">
                </div>

                <!-- Quick Links Section -->
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-white">Home</a></li>
                        <li><a href="https://ouk.ac.ke/ouk-programmes" class="text-white">Programmes</a></li>
                        <li><a href="https://ouk.ac.ke/timetable" class="text-white">Timetables</a></li>
                        <li><a href="https://ouk.ac.ke/application-process" class="text-white">How to Apply</a></li>
                        <li><a href="https://ouk.ac.ke/contact-us" class="text-white">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Contact Information Section -->
                <div class="col-md-4">
                    <h5>Contact Information</h5>
                    <p>The Cradle, Silicon Savanna, Konza Technopolis, P.O. Box 2440-00606,
                        Nairobi, Kenya</p>
                    <p><strong>Email:</strong> <a href="mailto:info@ouk.ac.ke" class="text-white">info@ouk.ac.ke</a>
                    </p>
                    <p><strong>Phone:</strong> +254 (020) 2000211 / 212</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Footer Bottom (White Background) -->
    <div class="mt-4 bg-white">
        <div class="container">
            <div class="row">
                <div class="text-center col">
                    <p>&copy; 2025 Open University of Kenya. All rights reserved.</p>
                    <p>
                        <a href="#" class="text-dark">Privacy Policy</a> |
                        <a href="#" class="text-dark">Terms of Service</a> |
                        <a href="#" class="text-dark">Cookie Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <script>
        $(document).ready(function() {
            $('.instructor-select').select2({
                placeholder: "Search Instructor...",
                allowClear: true,
                ajax: {
                    url: "{{ route('search.instructors') }}",
                    dataType: 'json',
                    delay: 250, // Delay to avoid excessive server requests
                    data: function(params) {
                        return {
                            q: params.term // Send the search term as "q"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(instructor) {
                                return {
                                    id: instructor.id,
                                    text: (instructor.title ? instructor.title.name + " " :
                                        "") + instructor.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
</body>

</html>
