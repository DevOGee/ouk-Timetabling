<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProgrammeTimetable extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'programme_timetable';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'programme_id',
        'timetable_id',
        'academic_session_id',
        'status',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
