<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\Title;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturersImport implements ToModel, WithHeadingRow
{
    public $importedCount = 0;

    public $skippedCount = 0;

    public function model(array $row)
    {
        // Debugging: Inspect the $row before checking the database
        // dd($row);  // This will show you the contents of the row before checking the database

        // Check if the lecturer already exists based on the email
        $existingLecturer = Lecturer::where('email', operator: $row['email'])->exists();

        // If the lecturer doesn't exist, create a new one
        if (! $existingLecturer) {
            // Find the title by name or create it if it doesn't exist
            $title = Title::firstOrCreate(['name' => $row['title']]);

            // Create and return the Lecturer model
            $lecturer = new Lecturer([
                'title_id' => $title->id,
                'name' => $row['name'],
                'email' => $row['email'],
                'image_path' => $row['image_path'] ?? null, // Bulk upload does not handle images
            ]);

            // Increment the count of newly imported lecturers
            $this->importedCount++;

            return $lecturer; // Return the newly created lecturer
        } else {
            // Increment the count of skipped lecturers (those already exist)
            $this->skippedCount++;

            return null; // Skip this row if the lecturer exists
        }
    }

    // This method is required to return the column headings
    public function headings(): array
    {
        return [
            'email', 'title', 'name', 'image_path',
        ];
    }
}
