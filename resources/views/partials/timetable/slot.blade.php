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

    // Set default fallback image URL
    $fallbackImage = 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png';
    $imagePath = $fallbackImage;

    // Process image path if available
    if (!empty($lesson->lecturer?->image_path)) {
        $imgPath = $lesson->lecturer->image_path;
        
        // If it's already a full URL, use it directly
        if (filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $imagePath = $imgPath;
        } 
        // If it's a local path, construct the full URL
        else {
            $imgPath = ltrim($imgPath, '/');
            $imgPath = str_replace(['storage/', 'public/'], '', $imgPath);
            $imagePath = asset('storage/' . $imgPath);
        }
    }

    // Determine session based on start time (morning if before 1PM, otherwise evening)
    $session = 'Not Specified'; // Default value
    // This logic uses the specific start time of the rendered slot.
    $timeToCheck = $lesson->session_start_time ?? ($lesson->start_time ?? null);
    
    // Debug information
    echo "<!-- Image Path: " . $imagePath . " -->\n";

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
   
    <div class="lesson-container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div class="lesson-details" style="flex-grow: 1; min-width: 0; order: 1; margin:10px;">
            <!-- Text content will go here -->
        
        <div class="instructor-avatar" style="width: 80px; height: 80px; flex-shrink: 0; order: 2;">
            <img 
                src="{{ $imagePath }}" 
                alt="{{ $instructorName }}"
                onerror="this.onerror=null; this.src='{{ $fallbackImage }}'"
                style="
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    object-fit: cover;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
        </div>
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