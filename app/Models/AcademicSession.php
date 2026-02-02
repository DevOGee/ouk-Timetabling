<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\ProgrammeTimetable;

class AcademicSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'status',
        'description',
        'is_current'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->is_current) {
                // Set all other sessions as not current
                static::where('id', '!=', $model->id)->update(['is_current' => false]);
            }

            if ($model->status === 'active') {
                // Set all other active sessions to completed
                static::where('id', '!=', $model->id)
                    ->where('status', 'active')
                    ->update(['status' => 'completed']);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('is_current') && $model->is_current) {
                // Set all other sessions as not current when this one is set as current
                static::where('id', '!=', $model->id)->update(['is_current' => false]);
            }

            if ($model->isDirty('status') && $model->status === 'active') {
                // Set all other active sessions to completed
                static::where('id', '!=', $model->id)
                    ->where('status', 'active')
                    ->update(['status' => 'completed']);
            }
        });
    }

    /**
     * Get all programme timetables for this academic session
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function timetables()
    {
        return $this->hasManyThrough(
            ProgrammeTimetable::class,
            Programme::class,
            'id', // Foreign key on programmes table
            'programme_id', // Foreign key on programme_timetable table
            'id', // Local key on academic_sessions table
            'id' // Local key on programmes table
        )->where('academic_session_id', $this->id);
    }
    
    /**
     * Get all programme timetables for this academic session
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function programmeTimetables()
    {
        return $this->hasMany(ProgrammeTimetable::class, 'academic_session_id')
            ->with('programme');
    }
    
    /**
     * Get all programmes associated with this academic session.
     */
    public function programmes()
    {
        return $this->belongsToMany(Programme::class, 'academic_session_programme')
            ->withTimestamps();
    }

    /**
     * Get all course unit mappings for this academic session.
     */
    public function courseUnitMappings()
    {
        return $this->hasMany(CourseUnitProgrammeMapping::class, 'academic_session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        return $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
    }
    
    /**
     * Get all course units associated with this academic session through programmes.
     */
    public function courseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_programme_mappings', 'academic_session_id', 'course_unit_id')
            ->withPivot(['programme_id', 'year_of_study_id', 'semester_id'])
            ->withTimestamps()
            ->distinct();
    }
}
