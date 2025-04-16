<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id', 'programme_code'];

    public function courseUnitMappings()
    {
        return $this->hasMany(CourseUnitProgrammeMapping::class);
    }

    public function courseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_programme_mappings')
            ->withPivot(['year_of_study_id', 'semester_id'])
            ->withTimestamps();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function lecturers()
    {
        return $this->hasManyThrough(
            Lecturer::class, // Related model
            CourseUnitProgrammeMapping::class, // The through model
            'programme_id', // Foreign key on the 'course_unit_programme_mappings' table (points to the related 'programme')
            'id', // Foreign key on the 'lecturers' table (points to 'lecturer_id')
            'id', // Local key on the 'programmes' table
            'lecturer_id' // Local key on the 'course_unit_programme_mappings' table (points to 'lecturer_id')
        );
    }

    // Get lecturers teaching this specific CourseUnit
    public function lecturersForCourseUnit($courseUnitId)
    {
        return $this->hasManyThrough(
            Lecturer::class, // Related model (lecturer)
            CourseUnitProgrammeMapping::class, // The "through" model
            'programme_id', // Foreign key on the `course_unit_programme_mappings` table
            'id', // Foreign key on the `lecturers` table (points to `lecturer_id`)
            'id', // Local key on the `programmes` table
            'lecturer_id' // Local key on the `course_unit_programme_mappings` table (points to `lecturer_id`)
        )
            ->where('course_unit_programme_mappings.course_unit_id', $courseUnitId); // Filter by specific course unit
    }
}
