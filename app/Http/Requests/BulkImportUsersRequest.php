<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkImportUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'exists:roles,name'],
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB max
            'send_welcome_email' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.required' => 'Please select a role for the users.',
            'role.exists' => 'The selected role is invalid.',
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.file' => 'The uploaded file is not valid.',
            'csv_file.mimes' => 'The file must be a CSV file.',
            'csv_file.max' => 'The file may not be greater than 10MB.',
        ];
    }
}
