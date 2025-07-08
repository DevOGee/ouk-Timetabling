<?php

namespace App\Imports;

use App\Models\Programme;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProgrammesImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError
{
    protected $schoolId;
    protected $rowCount = 0;
    protected $errors = [];
    protected $importedCount = 0;
    protected $skippedCount = 0;

    /**
     * @var array
     */
    public $headingRow = 1;

    /**
     * @var array
     */
    protected $requiredHeaders = ['programme_code', 'name'];

    public function __construct($schoolId)
    {
        $this->schoolId = $schoolId;
    }

    /**
     * @param Collection $rows
     * @throws ValidationException
     */
    public function collection(Collection $rows)
    {
        $this->validateHeaders($rows->first()->keys()->toArray());

        foreach ($rows as $index => $row) {
            // Skip empty rows
            if ($row->filter()->isEmpty()) {
                $this->skippedCount++;
                continue;
            }

            try {
                // Normalize the row keys to match the expected format
                $normalizedRow = $this->normalizeRow($row->toArray());

                // Validate the row data
                $validator = Validator::make($normalizedRow, [
                    'programme_code' => [
                        'required',
                        'string',
                        'max:50',
                        Rule::unique('programmes', 'programme_code')
                    ],
                    'name' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    $this->errors[] = [
                        'row' => $index + $this->headingRow + 1,
                        'errors' => $validator->errors()->all()
                    ];
                    $this->skippedCount++;
                    continue;
                }

                // Create the programme
                Programme::create([
                    'programme_code' => $normalizedRow['programme_code'],
                    'name' => $normalizedRow['name'],
                    'school_id' => $this->schoolId,
                ]);

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + $this->headingRow + 1,
                    'errors' => [$e->getMessage()]
                ];
                $this->skippedCount++;
                continue;
            }
        }

        if (!empty($this->errors)) {
            $errorMessages = collect($this->errors)->map(function ($error) {
                return "Row {$error['row']}: " . implode(', ', $error['errors']);
            })->implode("\n");
            
            throw new \Exception("Some rows failed validation:\n" . $errorMessages);
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.programme_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('programmes', 'programme_code')
            ],
            '*.name' => 'required|string|max:255',
        ];
    }

    /**
     * @param array $headers
     * @throws ValidationException
     */
    protected function validateHeaders(array $headers)
    {
        $missingHeaders = array_diff($this->requiredHeaders, $headers);
        
        if (!empty($missingHeaders)) {
            throw ValidationException::withMessages([
                'headers' => [
                    'Missing required headers: ' . implode(', ', $missingHeaders) . 
                    '. Please download the template for the correct format.'
                ]
            ]);
        }
    }

    /**
     * Normalize row keys to match database columns
     *
     * @param array $row
     * @return array
     */
    protected function normalizeRow(array $row): array
    {
        $normalized = [];
        
        // Convert all keys to snake_case and trim values
        foreach ($row as $key => $value) {
            $normalized[strtolower(str_replace(' ', '_', $key))] = is_string($value) ? trim($value) : $value;
        }
        
        return $normalized;
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        // Log the error if needed
        logger()->error('Error importing programmes: ' . $e->getMessage());
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * @return int
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    
    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
