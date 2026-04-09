<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Timetable - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --uni-teal: #037b90;
            --uni-teal-hover: #025f70;
            --uni-gold: #ff7f50;
            --uni-bg: #f5f7f9;
        }
        body {
            background-color: var(--uni-bg);
            font-family: 'Outfit', sans-serif;
        }
        .btn-uni-primary {
            background-color: var(--uni-teal);
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .btn-uni-primary:hover {
            background-color: var(--uni-teal-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .text-uni-teal {
            color: var(--uni-teal) !important;
        }
        .icon-accent {
            color: var(--uni-gold) !important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="text-center p-5 bg-white shadow rounded-3 border-top border-5" style="border-top-color: var(--uni-teal) !important;">
            <i class="bi bi-calendar-x icon-accent display-1 mb-4"></i>
            <h2 class="mb-3 text-uni-teal">No Active Exam Schedule</h2>
            <p class="text-muted lead mb-4">There is currently no active examination timetable published.</p>
            <a href="/" class="btn btn-uni-primary">Return Home</a>
        </div>
    </div>
</body>
</html>
