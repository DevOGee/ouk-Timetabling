<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specialisation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'programme_id'];

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }
}
