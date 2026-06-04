<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProgrammeTimetable;
use App\Models\Programme;
use App\Models\Semester;
use App\Models\YearOfStudy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Timetable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'academic_session_id',
        'start_date',
        'end_date',
        'status',
        'is_published'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];
    
    protected $dates = [
        'published_at'
    ];

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
    
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }
    
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    
    public function yearOfStudy()
    {
        return $this->belongsTo(YearOfStudy::class);
    }
    
    /**
     * Get all programmes associated with this timetable.
     */
    public function programmes(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class, 'programme_timetable')
            ->using(ProgrammeTimetable::class)
            ->withPivot(['status', 'published_at', 'academic_session_id'])
            ->withTimestamps();
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('academic_session_id', $sessionId);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
