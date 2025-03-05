<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUnit extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'year_of_study_id', 'semester_id', 'color']; // Added 'color'

    public function yearOfStudy()
    {
        return $this->belongsTo(YearOfStudy::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function programmes()
    {
        return $this->belongsToMany(Programme::class, 'course_unit_programme')->withTimestamps();
    }

    public function instructors()
    {
        return $this->belongsToMany(Lecturer::class, 'course_unit_instructor')
            ->withPivot('programme_id')
            ->withTimestamps();
    }

    public function lessonSlots()
    {
        return $this->hasMany(LessonSlot::class, 'course_unit_id');
    }
}
