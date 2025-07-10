<?php

namespace App\Exports;

use App\Models\CourseUnitProgrammeMapping;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProgrammeSchedulePdfExport implements FromView
{
    protected $programmeId;
    protected $academicSessionId;
    protected $programme;
    protected $academicSession;

    public function __construct($programmeId, $academicSessionId, $programme, $academicSession)
    {
        $this->programmeId = $programmeId;
        $this->academicSessionId = $academicSessionId;
        $this->programme = $programme;
        $this->academicSession = $academicSession;
    }

    public function view(): View
    {
        $mappings = CourseUnitProgrammeMapping::with(['courseUnit', 'day', 'instructor'])
            ->where('programme_id', $this->programmeId)
            ->where('academic_session_id', $this->academicSessionId)
            ->orderBy('level')
            ->orderBy('semester')
            ->orderBy('day_id')
            ->get()
            ->groupBy(['level', 'semester']);

        return view('exports.programme-schedule-pdf', [
            'mappings' => $mappings,
            'programme' => $this->programme,
            'academicSession' => $this->academicSession
        ]);
    }
}
