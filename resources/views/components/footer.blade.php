<footer class="mt-auto py-4 text-white bg-dark">
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
                    <li><a href="{{ route('dashboard') }}" class="text-white">Dashboard</a></li>
                    <li><a href="{{ route('timetable.index') }}" class="text-white">Timetables</a></li>
                    <li><a href="#" class="text-white">Academic Calendar</a></li>
                    <li><a href="#" class="text-white">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <address>
                    <p><i class="bi bi-geo-alt"></i> P.O. Box 3000-30100, Eldoret, Kenya</p>
                    <p><i class="bi bi-telephone"></i> +254 700 000000</p>
                    <p><i class="bi bi-envelope"></i> info@ouk.ac.ke</p>
                </address>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Open University of Kenya. All rights reserved.</p>
        </div>
    </div>
</footer>
