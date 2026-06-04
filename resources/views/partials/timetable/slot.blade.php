@php
    $duration = $lesson->duration ?? ($lesson->session_duration ?? 0);
    $rowspan = max(1, ceil($duration / 30));

    $titleAbbr = '';
    if (!empty($lesson->lecturer?->title?->abbreviation)) {
        $titleAbbr = $lesson->lecturer->title->abbreviation . ' ';
    }
    $instructorName = $titleAbbr . ($lesson->lecturer?->name ?? 'Not Assigned');

    $fallbackImage = 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png';
    $imagePath = $fallbackImage;

    if (!empty($lesson->lecturer?->image_path)) {
        $imgPath = $lesson->lecturer->image_path;
        if (filter_var($imgPath, FILTER_VALIDATE_URL)) {
            $imagePath = $imgPath;
        } else {
            $imgPath = ltrim($imgPath, '/');
            $imgPath = str_replace(['storage/', 'public/'], '', $imgPath);
            $imagePath = asset('storage/' . $imgPath);
        }
    }

    $session = 'Not Specified';
    $timeToCheck = $lesson->session_start_time ?? ($lesson->start_time ?? null);
    
    if (!empty($timeToCheck)) {
        try {
            $startTime = \Carbon\Carbon::parse($timeToCheck);
            $hour = (int)$startTime->format('H');
            if ($hour < 13) {
                $session = 'Morning';
            } else {
                $session = 'Evening';
            }
        } catch (\Exception $e) {
            if (!empty($lesson->session)) {
                $session = $lesson->session;
            } elseif (!empty($lesson->active_slot_type)) {
                $session = $lesson->active_slot_type;
            }
        }
    } elseif (!empty($lesson->session)) {
        $session = $lesson->session;
    } elseif (!empty($lesson->active_slot_type)) {
        $session = $lesson->active_slot_type;
    }
    
    $mode = $lesson->mode ?? 'Synchronous online';
    $courseColor = $lesson->courseUnit?->color ?? '#3b82f6';
@endphp

<td rowspan="{{ $rowspan }}" style="padding: 4px; vertical-align: top; border: 1px solid #dee2e6; background-color: transparent;">
    <div style="background-color: {{ $courseColor }}; border-radius: 4px; padding: 12px; height: 100%; display: flex; flex-direction: column; color: white; transition: filter 0.2s; cursor: pointer;" onmouseover="this.style.filter='brightness(0.95)'" onmouseout="this.style.filter='brightness(1)'">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
            <div style="flex-grow: 1; overflow: hidden;">
                <p style="font-size: 0.95rem; font-weight: 700; margin: 0 0 4px 0; line-height: 1.2;">{{ $instructorName }}</p>
                <div style="font-size: 0.85rem; font-weight: 600; opacity: 0.95; margin-bottom: 2px;">{{ $lesson->courseUnit->code }}</div>
                <div style="font-size: 0.8rem; opacity: 0.9; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ $lesson->courseUnit->name }}
                </div>
            </div>
            
            <div style="width: 45px; height: 45px; flex-shrink: 0; display: {{ $mobile ? 'none' : 'block' }};">
                <img src="{{ $imagePath }}" alt="{{ $instructorName }}" onerror="this.onerror=null; this.src='{{ $fallbackImage }}'" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.5);">
            </div>
        </div>
        
        <div style="margin-top: auto; padding-top: 10px;">
            <div style="height: 1px; background: rgba(255,255,255,0.2); margin-bottom: 8px;"></div>
            <div style="font-size: 0.75rem; opacity: 0.9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 4px;">
                <span style="display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-laptop" style="font-size: 0.7rem;"></i>
                    {{ !empty($mode) ? ucfirst($mode) : 'Online' }}
                </span>
                <span style="display: flex; align-items: center; gap: 4px;">
                    @if(strtolower($session) === 'morning')
                        <i class="fas fa-sun" style="font-size: 0.7rem;"></i> Morning
                    @elseif(strtolower($session) === 'evening')
                        <i class="fas fa-moon" style="font-size: 0.7rem;"></i> Evening
                    @else
                        <i class="fas fa-clock" style="font-size: 0.7rem;"></i> {{ ucfirst($session) }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</td>
