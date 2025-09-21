<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LecturerConflictsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    protected $conflicts;
    protected $school;
    protected $academicSession;

    public function __construct($conflicts, $school = null, $academicSession = null)
    {
        $this->conflicts = $conflicts;
        $this->school = $school;
        $this->academicSession = $academicSession;
    }

    public function collection()
    {
        return $this->conflicts;
    }

    public function title(): string
    {
        return 'Lecturer Conflicts';
    }

    public function headings(): array
    {
        $headings = [
            'Lecturer',
            'School'
        ];
        
        return array_merge($headings, [
            'Day',
            'Conflict Time',
            'Course',
            'Programme',
            'Time Slot',
            'Session Type'
        ]);
    }

    private $instructorNumber = 1;
    
    public function map($conflict): array
    {
        $rows = [];
        
        // Format time as 5.30PM
        $formatTime = function($timeStr) {
            if (empty($timeStr)) return 'N/A';
            try {
                return \Carbon\Carbon::createFromFormat('H:i:s', $timeStr)->format('g.ia');
            } catch (\Exception $e) {
                try {
                    return \Carbon\Carbon::parse($timeStr)->format('g.ia');
                } catch (\Exception $e) {
                    return $timeStr;
                }
            }
        };
        
        foreach ($conflict['conflicting_slots'] as $slot) {
            $timeParts = explode(' - ', $slot['time'] ?? '');
            $formattedTime = count($timeParts) === 2 
                ? $formatTime(trim($timeParts[0])) . ' - ' . $formatTime(trim($timeParts[1]))
                : 'N/A';
                
            $schoolName = $conflict['instructor']->school
                ? $conflict['instructor']->school->name
                : 'No School';
                
            $row = [
                $this->instructorNumber . '. ' . $conflict['instructor']->name,
                $schoolName
            ];
            
            $rows[] = array_merge($row, [
                $conflict['day'],
                $conflict['time_period'],
                $slot['course'],
                $slot['programme'],
                $conflict['day'] . ' ' . $formattedTime,
                $slot['type']
            ]);
        }
        
        // Increment for the next instructor
        $this->instructorNumber++;
        
        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set title and metadata
                $event->sheet->setCellValue('A1', 'Lecturer Scheduling Conflicts Report');
                $event->sheet->mergeCells('A1:H1');
                
                if ($this->academicSession) {
                    $event->sheet->setCellValue('A2', 'Academic Session: ' . $this->academicSession->name);
                    $event->sheet->mergeCells('A2:H2');
                }
                
                if ($this->school) {
                    $event->sheet->setCellValue('A3', 'School: ' . $this->school->name);
                    $event->sheet->mergeCells('A3:H3');
                }
                
                // Style the header
                $event->sheet->getStyle('A5:H5')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Style the title
                $event->sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Set row height for title rows
                $event->sheet->getRowDimension(1)->setRowHeight(25);
                $event->sheet->getRowDimension(2)->setRowHeight(20);
                $event->sheet->getRowDimension(3)->setRowHeight(20);
                $event->sheet->getRowDimension(5)->setRowHeight(25);
                
                // Add a blank row after the title
                $event->sheet->insertNewRowBefore(5, 1);
                
                // Set the starting row for data
                $startRow = 6;
                $endRow = $startRow + $this->collection()->count() - 1;
                
                // Apply borders to all cells
                $event->sheet->getStyle("A{$startRow}:H{$endRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD'],
                        ],
                    ],
                ]);
                
                // Group by lecturer for better readability
                $currentInstructor = null;
                $instructorStartRow = $startRow;
                
                for ($i = $startRow; $i <= $endRow; $i++) {
                    $instructor = $event->sheet->getCell("A{$i}")->getValue();
                    
                    if ($instructor !== $currentInstructor) {
                        if ($currentInstructor !== null) {
                            // Apply background to previous instructor's group
                            $event->sheet->getStyle("A{$instructorStartRow}:H" . ($i - 1))->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F8F9FA']
                                ]
                            ]);
                        }
                        
                        $currentInstructor = $instructor;
                        $instructorStartRow = $i;
                    }
                }
                
                // Apply to the last group
                if ($currentInstructor !== null) {
                    $event->sheet->getStyle("A{$instructorStartRow}:H{$endRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8F9FA']
                        ]
                    ]);
                }
                
                // Set wrap text for better readability
                $event->sheet->getStyle("A1:H{$endRow}")->getAlignment()->setWrapText(true);
                
                // Set the print area
                $event->sheet->getPageSetup()->setPrintArea("A1:H{$endRow}");
            },
        ];
    }
}
