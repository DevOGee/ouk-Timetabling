@php
    // Combines duration logic from both snippets for robustness.
    $duration = $lesson->duration ?? ($lesson->session_duration ?? 0);
    $rowspan = max(1, ceil($duration / 30));

    // Get title abbreviation if available, otherwise empty
    $titleAbbr = '';
    if (!empty($lesson->lecturer?->title?->abbreviation)) {
        $titleAbbr = $lesson->lecturer->title->abbreviation . ' ';
    }
    $instructorName = $titleAbbr . ($lesson->lecturer?->name ?? 'Not Assigned');

    // Implements the dynamic image path logic with a reliable fallback
    $imagePath = 'https://planner.ouk.ac.ke/storage/facilitators/alt.png';
    if (!empty($lesson->lecturer?->image_path)) {
        $imgPath = $lesson->lecturer->image_path;
        // Remove any leading slashes or storage/ prefixes
        $imgPath = ltrim($imgPath, '/');
        $imgPath = str_replace('storage/', '', $imgPath);
        $imgPath = str_replace('public/', '', $imgPath);
        $imagePath = 'https://planner.ouk.ac.ke/storage/' . $imgPath;
    }

    // Determine session based on start time (morning if before 1PM, otherwise evening)
    // --- USER'S SESSION LOGIC INTEGRATED HERE ---
    $session = 'Not Specified'; // Default value
    // This logic uses the specific start time of the rendered slot.
    $timeToCheck = $lesson->session_start_time ?? ($lesson->start_time ?? null);

    if (!empty($timeToCheck)) {
        try {
            $startTime = \Carbon\Carbon::parse($timeToCheck);
            $hour = (int)$startTime->format('H'); // Get hour in 24-hour format.
            
            if ($hour < 13) { // Before 1 PM is Morning
                $session = 'Morning';
            } else {
                $session = 'Evening';
            }
        } catch (\Exception $e) {
            // Fallback to session or slot type if time parsing fails.
            if (!empty($lesson->session)) {
                $session = $lesson->session;
            } elseif (!empty($lesson->active_slot_type)) {
                $session = $lesson->active_slot_type;
            }
        }
    } elseif (!empty($lesson->session)) {
        $session = $lesson->session; // Fallback 1
    } elseif (!empty($lesson->active_slot_type)) {
        $session = $lesson->active_slot_type; // Fallback 2
    }
    
    $mode = $lesson->mode ?? 'Synchronous online';
@endphp

<td rowspan="{{ $rowspan }}" 
   style="background-color: {{ $lesson->courseUnit?->color ?? '#6C3428' }}; color: white; vertical-align: middle; padding: 10px;"
   class="timetable-slot">
   
    <div class="lesson-container" style="display: flex; align-items: center; gap: 15px;">

        <div class="instructor-img-container" style="flex-shrink: 0;">
            <img class="instructor-image"
                src="{{ $imagePath }}"
                alt="Instructor Image"
                onerror="this.onerror=null; this.src='https://planner.ouk.ac.ke/storage/facilitators/alt.png';"
                style="
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid white;
                    background-color: #f0f0f0;
                    display: block;
                ">
        </div>
        
        <div class="lesson-details" style="flex-grow: 1; min-width: 0;">
            <p class="instructor-name" style="font-size: 1rem; font-weight: 600; margin: 0 0 5px 0;">
                {{ $instructorName }}
            </p>
            
            <div class="course-info" style="margin: 5px 0; line-height: 1.3;">
                <div style="font-size: 0.9rem; white-space: normal; word-wrap: break-word;">
                    <strong>{{ $lesson->courseUnit->code }}:</strong> {{ $lesson->courseUnit->name }}
                </div>
                <hr style="margin: 8px 0; border: 0; height: 2px; background: #fff; opacity: 0.9;">
            </div>
            
            <div style="font-size: 0.8rem; margin-top: 4px; color: rgba(255,255,255,0.9) !important;">
                <div>Mode: {{ !empty($mode) ? ucfirst($mode) : 'Synchronous Online' }}</div>
                <div>Session: {{ !empty($session) ? ucfirst($session) : 'Not Specified' }}</div>
            </div>
        </div>

    </div>
</td>