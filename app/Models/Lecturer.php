<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = ['title_id', 'name', 'email', 'image_path'];

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function courses()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_instructor')
            ->withPivot('programme_id')
            ->withTimestamps();
    }

    public function courseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_instructor')->withTimestamps();
    }
}
