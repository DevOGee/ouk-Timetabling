<?php

namespace App\Imports;

use App\Models\Lecturer;
use App\Models\Title;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Find the title by name or create it if it doesn't exist
        $title = Title::firstOrCreate(['name' => $row['title']]);

        return new Lecturer([
            'title_id' => $title->id,
            'name' => $row['name'],
            'email' => $row['email'],
            'image_path' => null, // Bulk upload does not handle images
        ]);
    }
}
