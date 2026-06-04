<?php

namespace App\Http\View\Composers;

use App\Models\AcademicSession;
use Illuminate\View\View;

class ActiveSessionComposer
{
    public function compose(View $view)
    {
        $activeSession = AcademicSession::where('is_current', true)->first();
        $view->with('activeSession', $activeSession);
    }
}
