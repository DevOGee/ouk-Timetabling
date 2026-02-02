<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all users associated with this school.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all deans associated with this school.
     */
    public function deans()
    {
        return $this->hasMany(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'dean');
        });
    }

    /**
     * Get all timetablers associated with this school.
     */
    public function timetablers()
    {
        return $this->hasMany(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'school_timetabler');
        });
    }

    /**
     * Get all instructors associated with this school.
     */
    public function instructors()
    {
        return $this->hasMany(User::class)->whereHas('roles', function ($query) {
            $query->where('name', 'instructor');
        });
    }

    /**
     * Get all departments associated with this school.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get all programmes associated with this school via departments.
     */
    public function programmes()
    {
        return $this->hasManyThrough(Programme::class, Department::class);
    }
}
