<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseUnit extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'color', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function yearOfStudy()
    {
        return $this->belongsTo(YearOfStudy::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function programmeMappings()
    {
        return $this->hasMany(CourseUnitProgrammeMapping::class);
    }

    public function programmes()
    {
        return $this->belongsToMany(Programme::class, 'course_unit_programme_mappings')
            ->withPivot(['academic_session_id', 'year_of_study_id', 'semester_id'])
            ->withTimestamps();
    }

    public function instructors()
    {
        return $this->hasManyThrough(
            User::class,
            CourseUnitProgrammeMapping::class,
            'course_unit_id', // Foreign key on course_unit_programme_mappings table
            'id', // Foreign key on users table
            'id', // Local key on course_units table
            'user_id' // Local key on course_unit_programme_mappings table
        )->distinct();
    }

    public function lessonSlots()
    {
        return $this->hasMany(LessonSlot::class, 'course_unit_id');
    }
    
    /**
     * Get all academic sessions that include this course unit.
     */
    public function academicSessions()
    {
        return $this->belongsToMany(AcademicSession::class, 'course_unit_programme_mappings', 'course_unit_id', 'academic_session_id')
            ->withPivot(['programme_id', 'year_of_study_id', 'semester_id'])
            ->withTimestamps();
    }
    
    /**
     * Get course unit mappings for a specific academic session.
     */
    public function sessionMappings($academicSessionId = null)
    {
        $query = $this->hasMany(CourseUnitProgrammeMapping::class);
        
        if ($academicSessionId) {
            $query->where('academic_session_id', $academicSessionId);
        }
        
        return $query;
    }
}
