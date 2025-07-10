<?php

namespace App\Exports;

use App\Models\CourseUnitProgrammeMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProgrammeScheduleExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
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

    public function collection()
    {
        return CourseUnitProgrammeMapping::with(['courseUnit', 'day', 'instructor', 'yearOfStudy', 'semester'])
            ->where('programme_id', $this->programmeId)
            ->where('academic_session_id', $this->academicSessionId)
            ->orderBy('year_of_study_id')
            ->orderBy('semester_id')
            ->orderBy('day_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Level',
            'Semester',
            'Course Code',
            'Course Name',
            'Day',
            'Instructor',
            'Morning Time',
            'Morning Duration (min)',
            'Evening Time',
            'Evening Duration (min)',
        ];
    }

    public function map($mapping): array
    {
        return [
            $mapping->yearOfStudy ? $mapping->yearOfStudy->name : 'N/A',
            $mapping->semester ? $mapping->semester->name : 'N/A',
            $mapping->courseUnit->code,
            $mapping->courseUnit->name,
            $mapping->day->name ?? 'Not Set',
            $mapping->instructor ? $mapping->instructor->name : 'Not Assigned',
            $mapping->morning_start_time ? \Carbon\Carbon::parse($mapping->morning_start_time)->format('h:i A') : 'N/A',
            $mapping->morning_duration ?? 'N/A',
            $mapping->evening_start_time ? \Carbon\Carbon::parse($mapping->evening_start_time)->format('h:i A') : 'N/A',
            $mapping->evening_duration ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return $this->programme->name . ' Schedule';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            // Set background color for header
            'A1:J1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9EAD3'], // Light green
                ],
            ],
        ];
    }
}
