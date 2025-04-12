<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\Title;
use Maatwebsite\Excel\Concerns\ToModel;

class LecturersImport implements ToModel
{
    public $importedCount = 0;

    public $skippedCount = 0;

    public function model(array $row)
    {
        // Check if the email exists in the row
        if (! isset($row['email'])) {
            // Log the error or handle missing email
            return null;  // Skip this row if email is missing
        }

        // Check if the lecturer already exists based on the email (or another unique identifier)
        $existingLecturer = Lecturer::where('email', $row['email'])->first();

        // If the lecturer doesn't exist, create a new one
        if (! $existingLecturer) {
            // Find the title by name or create it if it doesn't exist
            $title = Title::firstOrCreate(['name' => $row['title']]);

            // Create and return the Lecturer model
            $lecturer = new Lecturer([
                'title_id' => $title->id,
                'name' => $row['name'],
                'email' => $row['email'],
                'image_path' => null, // Bulk upload does not handle images
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
}
