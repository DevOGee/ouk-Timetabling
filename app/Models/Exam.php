<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_schedule_id',
        'course_unit_programme_mapping_id',
        'course_unit_id',
        'user_id',
        'exam_date',
        'start_time',
        'duration_minutes',
        'room_id',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime', // Laravel typically casts time to Carbon instance if format is H:i:s, but 'datetime' works well too
        'duration_minutes' => 'integer',
        'room_id' => 'integer',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id');
    }

    public function mapping(): BelongsTo
    {
        return $this->belongsTo(CourseUnitProgrammeMapping::class, 'course_unit_programme_mapping_id');
    }

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function invigilator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
