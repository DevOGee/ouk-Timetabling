<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgrammeTimetable extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'programme_timetable';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'programme_id',
        'academic_session_id',
        'name',
        'description',
        'status',
        'published_at',
        'published_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_published',
    ];
    
    /**
     * Get the programme that owns the timetable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * Get the academic session that owns the timetable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the user who published this timetable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
    
    /**
     * Check if the timetable is published.
     *
     * @return bool
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published';
    }
    
    /**
     * Scope a query to only include published timetables.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    /**
     * Scope a query to only include draft timetables.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
    
    /**
     * Scope a query to only include timetables for a specific programme.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $programmeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProgramme($query, $programmeId)
    {
        return $query->where('programme_id', $programmeId);
    }
    
    /**
     * Scope a query to only include timetables for a specific academic session.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $academicSessionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAcademicSession($query, $academicSessionId)
    {
        return $query->where('academic_session_id', $academicSessionId);
    }
}
