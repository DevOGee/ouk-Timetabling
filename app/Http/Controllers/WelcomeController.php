<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\User;

class WelcomeController extends Controller
{
    public function index()
    {
        $programmes = Programme::count();
        $courseUnits = CourseUnit::count();
        $instructors = User::role('instructor')->count();
        
        // Let's pass the raw numbers since the view will need them
        return view('welcome', compact('programmes', 'courseUnits', 'instructors'));
    }
}
