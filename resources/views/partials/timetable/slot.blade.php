@if (isset($mobile) && $mobile)
    <td rowspan="{{ ceil(($lesson->duration ?? 0) / 30) }}"
        style="background-color: {{ $lesson->courseUnit?->color ?? '#ff7f50' }}; color: white; vertical-align: top; padding: 8px;">
        <!-- Instructor Image -->
        <div class="mb-2" style="text-align: left;">
            <img class="instructor-image"
                src="{{ $lesson->lecturer?->image_path ? asset('storage/' . $lesson->lecturer->image_path) : 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png' }}"
                alt="Instructor"
                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid white;">
        </div>

        <!-- Course and Instructor Info -->
        <div style="text-align: left;">
            <!-- Course Code and Name -->
            <div class="mb-1">
                <div class="fw-bold"
                    style="font-size: 0.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $lesson->courseUnit->code }}
                </div>
                <div style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $lesson->courseUnit->name }}
                </div>
            </div>

            <!-- Instructor Name -->
            <div class="mb-1"
                style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                {{ $lesson->lecturer?->title->name ?? '' }} {{ $lesson->lecturer?->name ?? '' }}
            </div>

            <!-- Mode and Session -->
            <div style="font-size: 0.65rem; opacity: 0.9;">
                <div>Mode: {{ $lesson->mode ?? 'Synchronous online' }}</div>
                <div>Session: {{ $lesson->session ?? 'Not specified' }}</div>
            </div>
        </div>
    </td>
@else
    <td rowspan="{{ ceil(($lesson->duration ?? 0) / 30) }}"
        style="background-color: {{ $lesson->courseUnit?->color ?? '#ff7f50' }}; color: white; vertical-align: middle; padding: 10px;">
        <div class="lesson-container">
            <div class="instructor-img-container">
                <img class="instructor-image"
                    src="{{ $lesson->lecturer?->image_path
                        ? asset('storage/' . $lesson->lecturer->image_path)
                        : 'https://ouk.ac.ke/sites/default/files/Facilitators/alt.png' }}"
                    alt="Instructor Image">
            </div>
            <div class="lesson-details">
                <p class="instructor-name">
                    {{ $lesson->lecturer?->title->name ?? '' }}
                    {{ $lesson->lecturer?->name ?? '' }}
                </p>
                <div class="course-code">
                    {{ $lesson->courseUnit->code }}:
                    <span class="course-title">
                        {{ $lesson->courseUnit->name }}
                    </span>
                </div>
                <p class="mode">Mode: Synchronous online</p>
                <div class="session">
                    Session: {{ ucfirst($lesson->session ?? ($lesson->active_slot_type ?? '-')) }}
                </div>
            </div>
        </div>
    </td>
@endif
