<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
        });

        static::updating(function ($model) {
            if ($model->isDirty('is_current') && $model->is_current) {
                // Set all other sessions as not current when this one is set as current
                static::where('id', '!=', $model->id)->update(['is_current' => false]);
            }
        });
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
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
