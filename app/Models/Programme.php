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
        return $this->hasMany(\App\Models\CourseUnitProgrammeMapping::class);
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
}
