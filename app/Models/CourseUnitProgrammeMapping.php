<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUnitProgrammeMapping extends Model
{
    use HasFactory;

    protected $table = 'course_unit_programme_mappings';

    protected $fillable = [
        'course_unit_id',
        'programme_id',
        'year_of_study_id',
        'semester_id',
        'lecturer_id',
        'day_id',
        'morning_start_time',
        'morning_duration',
        'evening_start_time',
        'evening_duration',
    ];

    // Relationships

    public function courseUnit()
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function yearOfStudy()
    {
        return $this->belongsTo(YearOfStudy::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function morningSlot()
    {
        return $this->belongsTo(LessonSlot::class, 'morning_slot_id');
    }

    public function eveningSlot()
    {
        return $this->belongsTo(LessonSlot::class, 'evening_slot_id');
    }
}
