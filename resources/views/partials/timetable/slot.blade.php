@php
    $duration = $lesson->duration ?? ($lesson->session_duration ?? 0);
    $rowspan = max(1, ceil($duration / 30));
    $isMobile = isset($mobile) && $mobile;
    
    // Set default image path
    $imagePath = 'https://planner.ouk.ac.ke/storage/facilitators/alt.png';
    
    // Get session information
    $session = $lesson->session ?? ($lesson->active_slot_type ?? 'Not specified');
    $mode = $lesson->mode ?? 'Synchronous online';
    $instructorName = trim(($lesson->lecturer?->title->name ?? '') . ' ' . ($lesson->lecturer?->name ?? ''));
@endphp

<td rowspan="{{ $rowspan }}" 
   style="background-color: {{ $lesson->courseUnit?->color ?? '#ff7f50' }}; color: white;"
   class="timetable-slot">
    <div class="lesson-container">
        @if($isMobile)
            <!-- Mobile View -->
            <div class="mb-2" style="text-align: left;">
                <img class="instructor-image"
                    src="{{ $imagePath }}"
                    alt="Instructor"
                    style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
            </div>

            <div style="text-align: left;">
                <div class="mb-1">
                    <div class="fw-bold" style="font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $lesson->courseUnit->code }}
                    </div>
                    <div style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $lesson->courseUnit->name }}
                    </div>
                </div>

                @if($instructorName)
                <div class="mb-1" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $instructorName }}
                </div>
                @endif

                <div style="font-size: 0.65rem; opacity: 0.9;">
                    <div>Mode: {{ $mode }}</div>
                    <div>Session: {{ ucfirst($session) }}</div>
                </div>
            </div>
        @else
            <!-- Desktop View -->
            <div class="lesson-container" style="display: flex; align-items: center; height: 100%;">
                <div class="instructor-img-container" style="margin-right: 10px; flex-shrink: 0;">
                        <img class="instructor-image"
                            src="{{ $imagePath }}"
                            alt="Instructor"
                            onerror="this.onerror=null; this.src='https://planner.ouk.ac.ke/storage/facilitators/alt.png'"
                            style="
                                width: 40px;
                                height: 40px;
                                border-radius: 50%;
                                object-fit: cover;
                                border: 2px solid white;
                                display: block;
                            ">
                </div>
                <div class="lesson-details" style="flex: 1; min-width: 0;">
                    @if($instructorName)
                    <p class="instructor-name" style="
                        margin: 0 0 4px 0;
                        font-size: 0.8rem;
                        font-weight: 500;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    ">
                        {{ $instructorName }}
                    </p>
                    @endif
                    <div class="course-code" style="
                        margin-bottom: 2px;
                        font-weight: 600;
                        font-size: 0.85rem;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    ">
                        {{ $lesson->courseUnit->code }}:
                        <span class="course-title" style="font-weight: 400;">
                            {{ $lesson->courseUnit->name }}
                        </span>
                    </div>
                    <p class="mode" style="
                        margin: 2px 0;
                        font-size: 0.7rem;
                        color: rgba(255,255,255,0.9);
                    ">
                        Mode: {{ !empty($mode) ? ucfirst($mode) : 'Synchronous Online' }}
                    </p>
                    <div class="session" style="
                        font-size: 0.7rem;
                        color: rgba(255,255,255,0.9);
                    ">
                        Session: {{ !empty($session) ? ucfirst($session) : 'Not Specified' }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</td>
