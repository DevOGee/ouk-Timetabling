<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonSlot extends Model
{
    use HasFactory;

    protected $fillable = ['course_unit_id', 'programme_id', 'day_id', 'start_time', 'duration'];

    public function courseUnit()
    {
        return $this->belongsTo(CourseUnit::class);
    }

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }
}
