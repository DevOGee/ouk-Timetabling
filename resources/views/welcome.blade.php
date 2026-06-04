<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open University of Kenya - Timetable System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden selection:bg-[rgb(var(--brand-teal))] selection:text-white" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <!-- Navigation Bar -->
    <nav :class="{'glass shadow-md py-3': scrolled, 'bg-transparent py-5': !scrolled}" class="fixed top-0 left-0 w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ route('welcome') }}" class="flex items-center space-x-3 group">
                <div class="p-2 rounded-xl shadow-lg group-hover:scale-105 transition-transform" style="background-color: #037b90;">
                    <img src="{{ asset('ouk-logo-small.png') }}" alt="OUK Logo" class="h-8 w-8 object-contain filter brightness-0 invert">
                </div>
                <span class="font-heading font-bold text-xl tracking-tight" :class="{'text-slate-800': scrolled, 'text-white': !scrolled}">Timetable</span>
            </a>
            
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full font-semibold transition-all duration-300"
                       :class="{'bg-[rgb(var(--brand-teal))] text-white hover:bg-[rgb(var(--brand-dark))] shadow-md hover:shadow-lg': scrolled, 'glass text-white hover:bg-white/20': !scrolled}">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full font-semibold transition-all duration-300"
                       :class="{'bg-[rgb(var(--brand-teal))] text-white hover:bg-[rgb(var(--brand-dark))] shadow-md hover:shadow-lg': scrolled, 'glass text-white hover:bg-white/20': !scrolled}">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 opacity-95 z-10" style="background-color: #037b90;"></div>
            <img src="{{ asset('assets/login/images/home-page.jpg') }}" alt="Background" class="absolute inset-0 w-full h-full object-cover">
            
            <!-- Abstract decorative shapes -->
            <div class="absolute top-1/4 -left-20 w-72 h-72 bg-[rgb(var(--brand-coral))] rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-float z-20"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-[rgb(var(--brand-teal))] rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-float z-20" style="animation-delay: 2s;"></div>
        </div>

        <!-- Content -->
        <div class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
            <div class="glass-dark inline-block px-4 py-1.5 rounded-full mb-6">
                <span class="text-sm font-semibold tracking-wider text-teal-300 uppercase">Academic Year 2025/2026</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-white font-heading tracking-tight mb-6 leading-tight">
                Master Your Time. <br>
                <span style="color: #ff7f50;">Achieve Excellence.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-200 max-w-2xl mx-auto mb-10 font-light leading-relaxed">
                Access your personalized timetable and manage your academic schedule with our intuitive platform designed for students and faculty of the Open University of Kenya.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="group relative px-8 py-4 bg-white text-[rgb(var(--brand-dark))] font-bold rounded-full overflow-hidden shadow-[0_0_40px_rgba(255,255,255,0.3)] transition-all hover:scale-105 hover:shadow-[0_0_60px_rgba(255,255,255,0.5)]">
                        <span class="relative z-10 flex items-center">
                            Go to Dashboard <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </a>
                @else
                    <a href="/timetable" class="group relative px-8 py-4 bg-white text-[rgb(var(--brand-dark))] font-bold rounded-full overflow-hidden shadow-[0_0_40px_rgba(255,255,255,0.3)] transition-all hover:scale-105 hover:shadow-[0_0_60px_rgba(255,255,255,0.5)]">
                        <span class="relative z-10 flex items-center">
                            View Timetable <i class="bi bi-calendar3 ml-2"></i>
                        </span>
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 text-white font-semibold rounded-full border-2 border-white/30 hover:bg-white hover:text-[rgb(var(--brand-dark))] transition-colors">
                        Instructor Login
                    </a>
                @endauth
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-30 animate-bounce">
            <div class="w-8 h-12 rounded-full border-2 border-white/50 flex justify-center p-2">
                <div class="w-1 h-3 bg-white rounded-full"></div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-24 relative z-20 -mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Stat Card 1 -->
                <div class="glass p-8 rounded-3xl text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto bg-teal-100 text-[rgb(var(--brand-teal))] rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-[rgb(var(--brand-teal))] group-hover:text-white transition-all shadow-sm">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h3 class="text-4xl font-bold font-heading text-slate-800 mb-2">{{ $programmes }}</h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Programmes</p>
                </div>
                
                <!-- Stat Card 2 -->
                <div class="glass p-8 rounded-3xl text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto bg-orange-100 text-[rgb(var(--brand-coral))] rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-[rgb(var(--brand-coral))] group-hover:text-white transition-all shadow-sm">
                        <i class="bi bi-book-half"></i>
                    </div>
                    <h3 class="text-4xl font-bold font-heading text-slate-800 mb-2">{{ $courseUnits }}</h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Course Units</p>
                </div>
                
                <!-- Stat Card 3 -->
                <div class="glass p-8 rounded-3xl text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                        <i class="bi bi-person-video3"></i>
                    </div>
                    <h3 class="text-4xl font-bold font-heading text-slate-800 mb-2">{{ $instructors }}</h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Instructors</p>
                </div>
                
                <!-- Stat Card 4 -->
                <div class="glass p-8 rounded-3xl text-center group hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all shadow-sm">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="text-4xl font-bold font-heading text-slate-800 mb-2">15,000+</h3>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-widest">Students</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-24 bg-white relative">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl font-bold font-heading mb-6 text-slate-900">Ready to explore your schedule?</h2>
            <p class="text-lg text-slate-600 mb-10 max-w-2xl mx-auto">Access the most up-to-date class schedules, room allocations, and exam timetables all in one place.</p>
            <a href="{{ route('timetable.index') }}" class="inline-flex items-center px-8 py-4 rounded-full text-white font-semibold text-lg shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all" style="background-color: #037b90;">
                <i class="bi bi-calendar3 mr-3 text-xl"></i> View Timetable
            </a>
            @guest
                <p class="mt-6 text-slate-500 text-sm">For administrative and faculty features, please <a href="{{ route('login') }}" class="text-[rgb(var(--brand-teal))] font-semibold hover:underline">login</a>.</p>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-6 md:mb-0">
                    <img src="{{ asset('ouk-logo-small.png') }}" alt="OUK Logo" class="h-10 opacity-70 filter brightness-0 invert mr-4">
                    <div>
                        <h4 class="text-white font-heading font-semibold text-lg">Open University of Kenya</h4>
                        <p class="text-sm">Excellence in Open and Distance e-Learning</p>
                    </div>
                </div>
                <div class="flex space-x-6 text-sm font-medium">
                    <a href="https://ouk.ac.ke" target="_blank" class="hover:text-white transition-colors">University Website</a>
                    <a href="https://elearning.ouk.ac.ke" target="_blank" class="hover:text-white transition-colors">eLearning Portal</a>
                    <a href="https://student.ouk.ac.ke" target="_blank" class="hover:text-white transition-colors">Student Portal</a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-slate-800 text-center text-sm">
                <p>&copy; {{ date('Y') }} Open University of Kenya. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>