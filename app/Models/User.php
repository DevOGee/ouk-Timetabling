<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the school that owns the user.
     */
    public function school(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(School::class);
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
        'password',
        'school_id',
        'image_path',
        'status',
        'phone',
        'last_login_at'
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
}
