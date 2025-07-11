<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open University of Kenya - Timetable System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Open University of Kenya Color Scheme */
        :root {
            --primary-color: #037b90; /* Teal */
            --secondary-color: #ff7f50; /* Coral */
            --accent-color: #ffb74d; /* Light Orange */
            --dark-bg: #036075; /* Darker teal */
            --light-bg: #f0f9fb; /* Light teal tint */
            --text-color: #2c3e50; /* Dark blue-gray for text */
            --text-muted-color: #7f8c8d; /* Muted gray */
            --card-bg: #ffffff; /* White for cards */
            --card-border: #e0f2f1; /* Light teal border */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5; /* Light gray background */
            color: var(--text-color);
        }

        .navbar {
            transition: background-color 0.5s ease;
            padding: 15px 0;
        }

        .navbar-dark .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar.scrolled {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 10px 0;
        }

        .hero-section {
            background-image: linear-gradient(135deg, rgba(3, 123, 144, 0.9), rgba(3, 96, 117, 0.8)), url("{{ asset('assets/login/images/home-page.jpg') }}");
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding-top: 76px;
            color: white;
        }
        
        .hero-section h1 {
            font-weight: 700;
            font-size: 3.5rem;
            text-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 1.5rem;
            color: white;
        }

        .hero-section p {
            font-weight: 400;
            font-size: 1.25rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            color: rgba(255, 255, 255, 0.95);
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-block;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(3, 123, 144, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(3, 123, 144, 0.4);
            background: #036075;
            border-color: #025462;
            color: white;
        }

        .btn-login {
            border: 2px solid white;
            color: white;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-login:hover {
            background-color: white;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .stats-section {
            padding: 80px 0;
            background-color: var(--light-bg);
        }

        .stat-card {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--card-border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            background: rgba(3, 123, 144, 0.1);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 15px 0 10px;
            display: block;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-muted-color);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cta-section {
            padding: 100px 0;
            background-color: white;
            border-radius: 20px;
            margin: -50px 20px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
        }
        
        .form-control, .form-select {
            background-color: white;
            color: var(--text-color);
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            height: auto;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background-color: white;
            color: var(--text-color);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(3, 123, 144, 0.2);
        }
        
        .form-control::placeholder {
            color: var(--text-muted-color);
        }

        .footer {
            background-color: #f8f9fa;
            padding: 60px 0 30px;
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 0;
            color: var(--text-muted-color);
        }

        .quick-links a {
            color: var(--text-muted-color);
            text-decoration: none;
            transition: color 0.3s ease;
            margin: 0 10px;
        }
        
        .quick-links a:hover {
            color: var(--primary-color);
        }
        
        .quick-links a:not(:last-child)::after {
            content: '|';
            margin-left: 10px;
            color: var(--text-muted-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .hero-section p {
                font-size: 1.1rem;
                padding: 0 20px;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('ouk-logo-small.png') }}" alt="OUK Logo" height="40" class="d-inline-block align-text-top me-2" style="filter: brightness(0) invert(1);">
                <span class="d-none d-sm-inline">Timetable</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="text-white">Welcome to Open University of Kenya</h1>
            <p>
                Access your personalized timetable and manage your academic schedule with our intuitive platform designed for students and faculty.
            </p>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary-custom px-4 py-2">Go to Dashboard</a>
            @else
                <a href="#cta" class="btn btn-primary-custom px-4 py-2 me-2">View Timetable</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 py-2">Login</a>
            @endauth
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="stats" class="stats-section">
        <div class="container">
            <div class="row g-4">
                <!-- Stat Card 1: Programmes -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
                        <span class="stat-number" data-target="{{ $programmes->count() }}">0</span>
                        <div class="stat-label">Programmes</div>
                    </div>
                </div>
                <!-- Stat Card 2: Courses -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-book-half"></i></div>
                        <span class="stat-number" data-target="{{ $courseUnits->count() }}">0</span>
                        <div class="stat-label">Course Units</div>
                    </div>
                </div>
                <!-- Stat Card 3: Instructors -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-person-video3"></i></div>
                        <span class="stat-number" data-target="{{ $instructors->count() }}">0</span>
                        <div class="stat-label">Instructors</div>
                    </div>
                </div>
                <!-- Stat Card 4: Students -->
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                        <span class="stat-number" data-target="15000">0</span>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Call to Action Section -->
    <section id="cta" class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-3">View Your Timetable</h2>
                    <p class="mb-4">Select your programme and academic year to instantly view the course schedule.</p>
                    
                    @php
                        $groupedProgrammes = $programmes->sortBy('programme_code')->groupBy('school_id');
                    @endphp
                    
                    <form method="GET" action="{{ route('timetable.index') }}" class="filter-form" target="_blank">
                        <div class="row g-4">
                            <!-- School Dropdown -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control form-control-lg" id="school_id" name="school_id" required>
                                        <option value="">Select School</option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}">
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Programme Dropdown -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control form-control-lg" id="programme_id" name="programme_id" required>
                                        <option value="">Select Programme</option>
                                        @foreach ($groupedProgrammes as $schoolId => $schoolProgrammes)
                                            @foreach ($schoolProgrammes as $programme)
                                                <option value="{{ $programme->id }}" data-school="{{ $schoolId }}">
                                                    {{ $programme->programme_code }} - {{ $programme->name }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Level of Study -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control form-control-lg" id="level" name="level" required>
                                        <option value="">Select Level</option>
                                        @foreach($levels as $level)
                                            <option value="{{ $level->id }}">
                                                {{ $level->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- View Button -->
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary-custom w-100 py-2 d-flex align-items-center justify-content-center" style="font-size: 1.1rem;">
                                    <i class="bi bi-calendar3 me-2"></i> View Timetable
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    @guest
                        <p class="text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-primary">Sign up</a> for full access.</p>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="quick-links mb-3">
                <a href="https://ouk.ac.ke" target="_blank">University Website</a>
                <a href="https://elearning.ouk.ac.ke" target="_blank">eLearning Portal</a>
                <a href="https://student.ouk.ac.ke" target="_blank">Student Portal</a>
            </div>
            <p>&copy; {{ date('Y') }} Open University of Kenya. All Rights Reserved.</p>
            <p class="mt-2">Open University of Kenya - Excellence in Open and Distance e-Learning</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolSelect = document.getElementById('school_id');
            const programmeSelect = document.getElementById('programme_id');
            const programmeOptions = programmeSelect.querySelectorAll('option[data-school]');
            
            // Function to filter programmes based on selected school
            function filterProgrammes() {
                const selectedSchool = schoolSelect.value;
                
                // Show/hide programme options based on selected school
                programmeOptions.forEach(option => {
                    const schoolId = option.getAttribute('data-school');
                    if (selectedSchool === '' || schoolId === selectedSchool) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                
                // Reset programme selection if it's no longer valid
                if (programmeSelect.value) {
                    const selectedOption = programmeSelect.options[programmeSelect.selectedIndex];
                    if (selectedOption.style.display === 'none') {
                        programmeSelect.value = '';
                    }
                }
            }
            
            // Add event listener for school selection change
            if (schoolSelect) {
                schoolSelect.addEventListener('change', filterProgrammes);
                
                // Initial filter in case a school is pre-selected
                filterProgrammes();
            }
        });
    </script>
    
    <script>
        // Navbar scroll effect
        const navbar = document.querySelector('.navbar');
        window.onscroll = () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };

        // Animated number counter
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            const speed = 200; // Lower number = faster animation

            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText.replace(/,/g, '');
                    const increment = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment).toLocaleString();
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target.toLocaleString();
                    }
                };
                
                // Start the animation when the element is in viewport
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCount();
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                
                observer.observe(counter);
            });
        }

        // Initialize counters when the page loads
        document.addEventListener('DOMContentLoaded', animateCounters);
    </script>
</body>
</html>