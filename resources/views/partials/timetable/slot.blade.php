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
