<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

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
    </style>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Brand Logo -->
            <a class="text-white navbar-brand fw-bold" href="{{ route('timetable.index') }}">
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
                        <a class="text-white nav-link" href="{{ route('course_mapping.upload') }}">
                            <i class="fas fa-project-diagram"></i> Course Mapping
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
                </ul>
            </div>
        </div>
    </nav>



    <div class="container mt-4">
        @yield('content')
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
