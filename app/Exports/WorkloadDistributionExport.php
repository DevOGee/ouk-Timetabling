<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkloadDistributionExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $academicSessionId;
    protected $schoolId;

    public function __construct($academicSessionId, $schoolId = 'all')
    {
        $this->academicSessionId = $academicSessionId;
        $this->schoolId = $schoolId;
    }

    public function collection()
    {
        $query = User::role('instructor')
            ->with(['courseUnitMappings' => function($q) {
                $q->where('academic_session_id', $this->academicSessionId)
                  ->with('courseUnit');
            }, 'title', 'school'])
            ->orderBy('name');
            
        // Apply school filter if specified
        if ($this->schoolId !== 'all') {
            $query->where('school_id', $this->schoolId);
        }
            
        $filtered = $query->get()
            ->filter(function($instructor) {
                return $instructor->school !== null; // Only include instructors with a school
            });
            
        // Add row numbers to the collection
        return $filtered->map(function($instructor, $index) {
                // Get unique course units
                $uniqueUnits = $instructor->courseUnitMappings
                    ->filter(function($mapping) {
                        return $mapping->courseUnit !== null;
                    })
                    ->unique('course_unit_id')
                    ->map(function($mapping) {
                        return $mapping->courseUnit;
                    });
                
                // Format name as "Title Firstname LASTNAME"
                $name = $instructor->name;
                $nameParts = explode(' ', $name);
                $lastName = array_pop($nameParts);
                $firstName = implode(' ', $nameParts);
                $title = $instructor->title ? ($instructor->title->abbreviation ?? $instructor->title->name) : '';
                $formattedName = trim(($title ? $title . ' ' : '') . $firstName . ' ' . strtoupper($lastName));
                
                return (object)[
                    'row_number' => $index + 1,
                    'name' => $formattedName,
                    'school' => $instructor->school ? $instructor->school->name : 'N/A',
                    'course_units' => $uniqueUnits->pluck('code')->implode(', '),
                    'total_units' => $uniqueUnits->count()
                ];
            });
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Instructor Name',
            'School',
            'Course Units Assigned',
            'Total Units'
        ];
    }

    public function map($instructor): array
    {
        return [
            $instructor->row_number,
            $instructor->name,
            $instructor->school,
            $instructor->course_units ?: 'No units assigned',
            $instructor->total_units
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to all cells
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getAlignment()
            ->setWrapText(true);
            
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3490dc']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ],
            // Set column widths and alignment
            'A' => [
                'width' => 8,
                'alignment' => ['horizontal' => 'center']
            ],
            'B' => ['width' => 35],
            'C' => ['width' => 30],
            'D' => ['width' => 50],
            'E' => [
                'width' => 12,
                'alignment' => ['horizontal' => 'center']
            ],
            // Add borders to all cells
            'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    ]
                ]
            ]
        ];
    }
}
