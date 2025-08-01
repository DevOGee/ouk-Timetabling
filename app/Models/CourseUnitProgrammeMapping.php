<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'user_id', // Instructor (user with role 'instructor')
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
        'morning_duration' => 'integer',
        'evening_duration' => 'integer',
    ];
    
    protected $appends = ['display_name'];

    /**
     * The "booting" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }

    // Relationships

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class)->withDefault();
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class)->withDefault();
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class)->withDefault();
    }

    public function yearOfStudy(): BelongsTo
    {
        return $this->belongsTo(YearOfStudy::class)->withDefault();
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class)->withDefault();
    }
    
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class)->withDefault();
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }
    
    /**
     * Get the lecturer (instructor) for this mapping.
     */
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }
    
    public function morningSlot(): HasOne
    {
        return $this->hasOne(LessonSlot::class, 'mapping_id')
            ->where('time_slot_type', 'morning');
    }
    
    public function eveningSlot(): HasOne
    {
        return $this->hasOne(LessonSlot::class, 'mapping_id')
            ->where('time_slot_type', 'evening');
    }

    // Scopes

    public function scopeForCurriculum(Builder $query, $curriculumId): Builder
    {
        return $query->whereHas('programme', function($q) use ($curriculumId) {
            $q->where('curriculum_id', $curriculumId);
        });
    }

    public function scopeForProgramme(Builder $query, $programmeId): Builder
    {
        return $query->where('programme_id', $programmeId);
    }

    public function scopeForYearOfStudy(Builder $query, $yearId): Builder
    {
        return $query->where('year_of_study_id', $yearId);
    }

    public function scopeForSemester(Builder $query, $semesterId): Builder
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopeForCourseUnit(Builder $query, $courseUnitId): Builder
    {
        return $query->where('course_unit_id', $courseUnitId);
    }
    
    public function scopeWithInstructors(Builder $query): Builder
    {
        return $query->whereNotNull('user_id');
    }
    
    public function scopeForInstructor(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Methods

    public function getDisplayNameAttribute(): string
    {
        $parts = [];
        
        if ($this->courseUnit) {
            $parts[] = $this->courseUnit->code . ' - ' . $this->courseUnit->name;
        }
        
        if ($this->yearOfStudy && $this->semester) {
            $parts[] = '(' . $this->yearOfStudy->name . ' - ' . $this->semester->name . ')';
        }
        
        if ($this->instructor) {
            $parts[] = 'Instructor: ' . $this->instructor->name;
        }
        
        return implode(' | ', $parts);
    }

    public function hasScheduling(): bool
    {
        return !is_null($this->day_id) || 
               !is_null($this->morning_start_time) || 
               !is_null($this->evening_start_time);
    }

    public function duplicateForCurriculum($newCurriculumId, bool $clearScheduling = true): self
    {
        $newMapping = $this->replicate();
        $newMapping->programme_id = $newCurriculumId;
        
        if ($clearScheduling) {
            $newMapping->day_id = null;
            $newMapping->morning_start_time = null;
            $newMapping->morning_duration = null;
            $newMapping->evening_start_time = null;
            $newMapping->evening_duration = null;
        }
        
        $newMapping->save();
        
        return $newMapping;
    }

    // Static Methods

    public static function getByProgrammeAndCurriculum($programmeId, $curriculumId = null)
    {
        $query = self::with(['courseUnit', 'instructor', 'yearOfStudy', 'semester', 'day'])
            ->where('programme_id', $programmeId);
            
        if ($curriculumId) {
            $query->whereHas('programme', function($q) use ($curriculumId) {
                $q->where('curriculum_id', $curriculumId);
            });
        }
        
        return $query->get()
            ->groupBy(['year_of_study_id', 'semester_id']);
    }

    public static function countForProgramme($programmeId, $curriculumId = null): int
    {
        $query = self::where('programme_id', $programmeId);
        
        if ($curriculumId) {
            $query->whereHas('programme', function($q) use ($curriculumId) {
                $q->where('curriculum_id', $curriculumId);
            });
        }
        
        return $query->count();
    }
}
