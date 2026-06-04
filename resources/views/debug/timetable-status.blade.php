<!DOCTYPE html>
<html>
<head>
    <title>Debug Timetable Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Timetable Status Analysis</h1>
        
        <h2 class="mt-4">Programme Status Summary</h2>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Programme ID</th>
                    <th>Programme Name</th>
                    <th>Total Mappings</th>
                    <th>Completed</th>
                    <th>In Progress</th>
                    <th>Not Started</th>
                    <th>Status</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programmes as $programme)
                    @php
                        $mappings = $programme->courseUnitMappings()
                            ->where('academic_session_id', $academicSessionId)
                            ->get();
                            
                        $totalMappings = $mappings->count();
                        $totalPossibleFields = $totalMappings * 2; // user_id and day_id for each mapping
                        $filledFields = 0;
                        $completedMappings = 0;
                        
                        foreach ($mappings as $mapping) {
                            // Count filled fields
                            if ($mapping->user_id !== null) $filledFields++;
                            if ($mapping->day_id !== null) $filledFields++;
                            
                            // Count completed mappings (both fields filled)
                            if ($mapping->user_id !== null && $mapping->day_id !== null) {
                                $completedMappings++;
                            }
                        }
                        
                        // Calculate progress based on filled fields
                        $progress = $totalPossibleFields > 0 
                            ? round(($filledFields / $totalPossibleFields) * 100) 
                            : 0;
                            
                        // For backward compatibility with the view
                        $completed = $completedMappings;
                        $inProgress = $filledFields - ($completedMappings * 2); // Partially filled mappings
                        $notStarted = $totalMappings - $completedMappings - ceil($inProgress / 2);
                        
                        if ($totalMappings === 0) {
                            $status = 'No Mappings';
                            $statusClass = 'secondary';
                        } elseif ($filledFields === $totalPossibleFields) {
                            $status = 'Done';
                            $statusClass = 'success';
                        } elseif ($filledFields > 0) {
                            $status = 'In Progress';
                            $statusClass = 'warning';
                        } else {
                            $status = 'Not Started';
                            $statusClass = 'secondary';
                        }
                    @endphp
                    <tr>
                        <td>{{ $programme->id }}</td>
                        <td>{{ $programme->name }}</td>
                        <td>{{ $totalMappings }}</td>
                        <td class="table-success">{{ $completed }}</td>
                        <td class="table-warning">{{ $inProgress }}</td>
                        <td class="table-secondary">{{ $notStarted }}</td>
                        <td><span class="badge bg-{{ $statusClass }}">{{ $status }}</span></td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-{{ $statusClass }}" role="progressbar" 
                                     style="width: {{ $progress }}%" 
                                     aria-valuenow="{{ $progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $progress }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <h2 class="mt-4">Sample Mappings</h2>
        @foreach($sampleMappings as $mapping)
            <div class="card mb-3">
                <div class="card-header">
                    Mapping ID: {{ $mapping->id }} | 
                    Programme: {{ $mapping->programme->name }} | 
                    Course Unit: {{ $mapping->courseUnit->name ?? 'N/A' }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Morning Session</h5>
                            <p>
                                <strong>Time:</strong> {{ $mapping->morning_start_time }}<br>
                                <strong>Duration:</strong> {{ $mapping->morning_duration }}<br>
                                <strong>Status:</strong> 
                                @if($mapping->morning_start_time && $mapping->morning_duration)
                                    <span class="badge bg-success">Scheduled</span>
                                @elseif($mapping->morning_start_time || $mapping->morning_duration)
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-secondary">Not Set</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Evening Session</h5>
                            <p>
                                <strong>Time:</strong> {{ $mapping->evening_start_time }}<br>
                                <strong>Duration:</strong> {{ $mapping->evening_duration }}<br>
                                <strong>Status:</strong> 
                                @if($mapping->evening_start_time && $mapping->evening_duration)
                                    <span class="badge bg-success">Scheduled</span>
                                @elseif($mapping->evening_start_time || $mapping->evening_duration)
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-secondary">Not Set</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong>Instructor:</strong> {{ $mapping->instructor->name ?? 'Not assigned' }}<br>
                            <strong>Day:</strong> {{ $mapping->day->name ?? 'Not set' }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
