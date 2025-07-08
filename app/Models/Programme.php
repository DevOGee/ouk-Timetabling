<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id', 'programme_code'];

    /**
     * Get all course unit mappings for this programme.
     */
    public function courseUnitMappings(): HasMany
    {
        return $this->hasMany(CourseUnitProgrammeMapping::class);
    }

    /**
     * Get all course units for this programme across all academic sessions.
     */
    public function courseUnits(): BelongsToMany
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_programme_mappings')
            ->withPivot(['academic_session_id', 'year_of_study_id', 'semester_id'])
            ->withTimestamps();
    }

    /**
     * Get course units for a specific academic session.
     */
    public function sessionCourseUnits($academicSessionId = null)
    {
        $query = $this->belongsToMany(CourseUnit::class, 'course_unit_programme_mappings', 'programme_id', 'course_unit_id')
            ->withPivot(['academic_session_id', 'year_of_study_id', 'semester_id']);
            
        if ($academicSessionId) {
            $query->wherePivot('academic_session_id', $academicSessionId);
        }
        
        return $query;
    }

    /**
     * Get course unit mappings for a specific academic session.
     */
    public function sessionMappings($academicSessionId = null): HasMany
    {
        $query = $this->hasMany(CourseUnitProgrammeMapping::class);
        
        if ($academicSessionId) {
            $query->where('academic_session_id', $academicSessionId);
        }
        
        return $query;
    }
    
    /**
     * Get all academic sessions this programme is associated with.
     */
    public function academicSessions()
    {
        return $this->belongsToMany(AcademicSession::class, 'academic_session_programme')
            ->withTimestamps();
    }

    /**
     * Get the school that owns the programme.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all instructors teaching in this programme.
     */
    public function instructors(): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            CourseUnitProgrammeMapping::class,
            'programme_id',
            'id',
            'id',
            'user_id'
        )->whereHas('roles', function($q) {
            $q->where('name', 'instructor');
        });
    }
    
    /**
     * @deprecated Use instructors() instead
     */
    public function lecturers(): HasManyThrough
    {
        return $this->instructors();
    }
    
    /**
     * Get instructors teaching in this programme for a specific academic session.
     */
    public function sessionInstructors($academicSessionId): HasManyThrough
    {
        return $this->hasManyThrough(
            User::class,
            CourseUnitProgrammeMapping::class,
            'programme_id',
            'id',
            'id',
            'lecturer_id'
        )->where('course_unit_programme_mappings.academic_session_id', $academicSessionId);
    }

    /**
     * Get instructors teaching a specific course unit in this programme.
     */
    public function lecturersForCourseUnit($courseUnitId, $curriculumId = null)
    {
        $query = $this->hasManyThrough(
            User::class,
            CourseUnitProgrammeMapping::class,
            'programme_id',
            'id',
            'id',
            'user_id'
        )->where('course_unit_programme_mappings.course_unit_id', $courseUnitId)
         ->whereHas('roles', function($q) {
             $q->where('name', 'instructor');
         });
        
        if ($curriculumId) {
            $query->where('course_unit_programme_mappings.curriculum_id', $curriculumId);
        }
        
        return $query;
    }
    
    /**
     * Get the number of course units in a specific curriculum.
     */
    public function countCourseUnitsInCurriculum($curriculumId): int
    {
        return $this->curriculumMappings($curriculumId)->count();
    }
    
    /**
     * Get course units by year and semester for a specific curriculum.
     */
    public function getCurriculumStructure($curriculumId)
    {
        return $this->curriculumMappings($curriculumId)
            ->with(['courseUnit', 'yearOfStudy', 'semester', 'lecturer'])
            ->get()
            ->groupBy(['year_of_study_id', 'semester_id']);
    }
}
