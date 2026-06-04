<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearOfStudy extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $table = 'years_of_study'; // Ensure it matches the migration
}
