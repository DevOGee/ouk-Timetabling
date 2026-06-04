<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\CourseUnit;
use App\Models\Programme;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Get the school that owns the user.
     */
    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the course units assigned to this instructor.
     */
    public function assignedCourseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_programme_mappings', 'user_id', 'course_unit_id')
            ->withTimestamps();
    }

    /**
     * Get the programmes this instructor is teaching in.
     */
    public function assignedProgrammes()
    {
        return $this->belongsToMany(Programme::class, 'course_unit_programme_mappings', 'user_id', 'programme_id')
            ->distinct()
            ->withTimestamps();
    }
    
    /**
     * Get the course unit programme mappings for this instructor.
     */
    public function courseUnitMappings()
    {
        return $this->hasMany(CourseUnitProgrammeMapping::class, 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'title_id',
        'email',
        'zoom_email',
        'password',
        'school_id',
        'image_path',
        'status',
        'phone',
        'last_login_at',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $with = ['title'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];
    
    /**
     * Get the user's full name with title.
     */
    public function getFullNameAttribute(): string
    {
        $title = $this->title ? $this->title->abbreviation ?? $this->title->name : '';
        return $title ? "{$title} {$this->name}" : $this->name;
    }

    /**
     * Get the user's title.
     */
    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope a query to filter users based on request parameters.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        });
    }

    /**
     * Update the last login timestamp.
     */
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Get all roles associated with the user.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
    
    /**
     * Scope a query to only include users with the instructor role.
     */
    public function scopeInstructors(Builder $query): Builder
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'instructor');
        });
    }

    /**
     * Check if the user is an instructor.
     */
    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }
    
    /**
     * Get all course unit programme mappings where this user is the instructor.
     */
    public function courseUnitProgrammeMappings()
    {
        return $this->hasMany(CourseUnitProgrammeMapping::class, 'user_id');
    }
}
