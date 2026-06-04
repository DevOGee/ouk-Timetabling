<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class InstructorSchedulesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithEvents
{
    protected $schedules;
    protected $instructor;

    public function __construct($schedules)
    {
        $this->schedules = $schedules;
    }

    public function collection()
    {
        // Flatten the schedules to include both morning and evening slots
        $flattened = collect();
        
        foreach ($this->schedules as $schedule) {
            if ($schedule->morning_time) {
                $flattened->push((object)[
                    'day' => $schedule->day,
                    'time' => $schedule->morning_time,
                    'course_unit' => $schedule->course_unit,
                    'programme' => $schedule->programme,
                    'type' => 'Morning'
                ]);
            }
            
            if ($schedule->evening_time) {
                $flattened->push((object)[
                    'day' => $schedule->day,
                    'time' => $schedule->evening_time,
                    'course_unit' => $schedule->course_unit,
                    'programme' => $schedule->programme,
                    'type' => 'Evening'
                ]);
            }
        }
        
        return $flattened;
    }

    public function headings(): array
    {
        return [
            'Day',
            'Time Slot',
            'Course (Code: Name)',
            'Programmes',
            'Session Type',
            'Status',
            'Semester'
        ];
    }

    public function map($schedule): array
    {
        // Join programme names with line breaks for better readability in Excel
        $programmes = collect($schedule->programmes)
            ->pluck('name')
            ->map(function($name) {
                return '• ' . $name; // Add bullet points
            })
            ->implode("\n");
            
        return [
            $schedule->day ?? 'N/A',
            $schedule->time ?? 'N/A',
            ($schedule->course_unit->code ?? 'N/A') . ': ' . ($schedule->course_unit->name ?? 'N/A'),
            $programmes ?: 'N/A',
            $schedule->session_type ?? 'N/A',
            'Scheduled',
            $schedule->session ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Instructor Schedule';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:G1')->getFont()->setBold(true);
                
                // Enable text wrapping for the programmes column (column D)
                $event->sheet->getDelegate()->getStyle('D2:D' . $event->sheet->getHighestRow())
                    ->getAlignment()
                    ->setWrapText(true);
                    
                // Set column widths
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(15); // Day
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20); // Time
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(40); // Course
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(40); // Programmes
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15); // Session Type
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(12); // Status
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15); // Semester
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Styling a specific cell by coordinate
            'A1:G1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9EAD3'],
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }
}
