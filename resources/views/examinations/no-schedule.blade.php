<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Timetable - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="text-center p-5 bg-white shadow rounded-3">
            <i class="bi bi-calendar-x text-muted display-1 mb-4"></i>
            <h2 class="mb-3">No Active Exam Schedule</h2>
            <p class="text-muted lead mb-4">There is currently no active examination timetable published.</p>
            <a href="/" class="btn btn-primary">Return Home</a>
        </div>
    </div>
</body>
</html>
