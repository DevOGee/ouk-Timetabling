<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonSlot extends Model
{
    use HasFactory;

    protected $fillable = ['course_unit_id', 'programme_id', 'day_id', 'start_time', 'duration'];

    public function courseUnit(): BelongsTo
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }
    
    public function courseUnitProgrammeMapping(): BelongsTo
    {
        return $this->belongsTo(CourseUnitProgrammeMapping::class, 'course_unit_programme_mapping_id');
    }
}
