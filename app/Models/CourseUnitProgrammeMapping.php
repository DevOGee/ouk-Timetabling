<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CourseUnitProgrammeMapping extends Model
{
    use HasFactory;

    protected $table = 'course_unit_programme_mappings';

    protected $fillable = [
        'course_unit_id',
        'programme_id',
        'academic_session_id',
        'year_of_study_id',
        'semester_id',
        'lecturer_id',
        'day_id',
        'morning_start_time',
        'morning_duration',
        'evening_start_time',
        'evening_duration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'morning_start_time' => 'datetime',
        'evening_start_time' => 'datetime',
    ];

    // Relationships

    /**
     * Get the course unit that owns the mapping.
     */
    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    /**
     * Get the programme that owns the mapping.
     */
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * Get the academic session that owns the mapping.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the year of study for the mapping.
     */
    public function yearOfStudy(): BelongsTo
    {
        return $this->belongsTo(YearOfStudy::class);
    }

    /**
     * Get the semester for the mapping.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the lecturer assigned to teach this course unit.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * Get the user who created this mapping.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this mapping.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes

    /**
     * Scope a query to only include mappings for a specific curriculum.
     */
    public function scopeForCurriculum(Builder $query, $curriculumId): Builder
    {
        return $query->where('curriculum_id', $curriculumId);
    }

    /**
     * Scope a query to only include mappings for a specific programme.
     */
    public function scopeForProgramme(Builder $query, $programmeId): Builder
    {
        return $query->where('programme_id', $programmeId);
    }

    /**
     * Scope a query to only include mappings for a specific year of study.
     */
    public function scopeForYearOfStudy(Builder $query, $yearId): Builder
    {
        return $query->where('year_of_study_id', $yearId);
    }

    /**
     * Scope a query to only include mappings for a specific semester.
     */
    public function scopeForSemester(Builder $query, $semesterId): Builder
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Scope a query to only include mappings for a specific course unit.
     */
    public function scopeForCourseUnit(Builder $query, $courseUnitId): Builder
    {
        return $query->where('course_unit_id', $courseUnitId);
    }

    // Helper Methods

    /**
     * Get the display name for the mapping.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = "{$this->courseUnit->code} - {$this->courseUnit->name}";
        
        if ($this->lecturer) {
            $name .= " (Taught by: {$this->lecturer->name})";
        }
        
        return $name;
    }

    /**
     * Check if this mapping has scheduling information.
     */
    public function hasScheduling(): bool
    {
        return !is_null($this->day_id) || 
               !is_null($this->morning_start_time) || 
               !is_null($this->evening_start_time);
    }

    /**
     * Duplicate this mapping for a new curriculum.
     */
    public function duplicateForCurriculum($newCurriculumId, $clearScheduling = true)
    {
        $newMapping = $this->replicate();
        $newMapping->curriculum_id = $newCurriculumId;
        
        if ($clearScheduling) {
            $newMapping->day_id = null;
            $newMapping->morning_start_time = null;
            $newMapping->morning_duration = null;
            $newMapping->evening_start_time = null;
            $newMapping->evening_duration = null;
        }
        
        $newMapping->created_by = auth()->id();
        $newMapping->save();
        
        return $newMapping;
    }

    // Static Methods

    /**
     * Get all mappings for a programme in a curriculum, grouped by year and semester.
     */
    public static function getByProgrammeAndCurriculum($programmeId, $curriculumId)
    {
        return self::with(['courseUnit', 'yearOfStudy', 'semester', 'lecturer'])
            ->where('programme_id', $programmeId)
            ->where('curriculum_id', $curriculumId)
            ->get()
            ->groupBy(['year_of_study_id', 'semester_id']);
    }

    /**
     * Count the number of course units in a programme's curriculum.
     */
    public static function countForProgramme($programmeId, $curriculumId = null): int
    {
        $query = self::where('programme_id', $programmeId);
        
        if ($curriculumId) {
            $query->where('curriculum_id', $curriculumId);
        }
        
        return $query->count();
    }

    // Events

    protected static function booted()
    {
        static::creating(function ($mapping) {
            if (auth()->check()) {
                $mapping->created_by = auth()->id();
            }
        });

        static::updating(function ($mapping) {
            if (auth()->check()) {
                $mapping->updated_by = auth()->id();
            }
        });
    }

    /**
     * Get the day for the mapping.
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Get the morning lesson slot for the mapping.
     */
    public function morningSlot(): BelongsTo
    {
        return $this->belongsTo(LessonSlot::class, 'morning_slot_id');
    }

    /**
     * Get the evening lesson slot for the mapping.
     */
    public function eveningSlot(): BelongsTo
    {
        return $this->belongsTo(LessonSlot::class, 'evening_slot_id');
    }
}
