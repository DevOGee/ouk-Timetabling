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

class TimeConflictsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    protected $conflicts;

    public function __construct($conflicts)
    {
        $this->conflicts = $conflicts;
    }

    public function collection()
    {
        return $this->conflicts;
    }

    public function title(): string
    {
        return 'Time Conflicts';
    }

    public function headings(): array
    {
        return [
            'Day',
            'Time Slot',
            'Course Code',
            'Course Name',
            'Programme',
            'Year/Semester',
            'Instructor',
            'Session Type'
        ];
    }

    public function map($conflict): array
    {
        $rows = [];
        $first = true;
        
        foreach ($conflict['conflicts'] as $mapping) {
            $rows[] = [
                $first ? $conflict['day'] : '',
                $first ? $conflict['time'] : '',
                $mapping->course_unit->code ?? 'N/A',
                $mapping->course_unit->name ?? 'N/A',
                $mapping->programme->programme_code ?? 'N/A',
                ($mapping->year_of_study ? $mapping->year_of_study->name : 'N/A') . ' / ' . 
                    ($mapping->semester ? $mapping->semester->name : 'N/A'),
                $mapping->instructor ? $mapping->instructor->name : 'N/A',
                $mapping->session_type
            ];
            $first = false;
        }
        
        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Style the header row
                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Set alignment for all cells
                $sheet->getStyle('A1:H' . $sheet->getHighestRow())->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD'],
                        ],
                    ],
                ]);

                // Set row height for better readability
                $sheet->getDefaultRowDimension()->setRowHeight(20);
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(15); // Day
                $sheet->getColumnDimension('B')->setWidth(20); // Time Slot
                $sheet->getColumnDimension('C')->setWidth(15); // Course Code
                $sheet->getColumnDimension('D')->setWidth(30); // Course Name
                $sheet->getColumnDimension('E')->setWidth(15); // Programme
                $sheet->getColumnDimension('F')->setWidth(20); // Year/Semester
                $sheet->getColumnDimension('G')->setWidth(25); // Instructor
                $sheet->getColumnDimension('H')->setWidth(15); // Session Type
                
                // Freeze the first row
                $sheet->freezePane('A2');
                
                // Set auto filter
                $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
            },
        ];
    }
}
