<?php

namespace App\Http\Requests;

use App\Models\ReviewRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validation for Add / Update Academic Review Request. */
class ReviewRequestForm extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role is checked by the route middleware
    }

    /** Normalise subject code before validating: " des 3083" -> "DES3083". */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'subject_code' => strtoupper(preg_replace('/\s+/', '', (string) $this->input('subject_code'))),
            'justification' => trim((string) $this->input('justification')),
        ]);
    }

    public function rules(): array
    {
        $ignoreId = $this->route('id');

        return [
            'subject_code' => [
                'required',
                'regex:/^[A-Z]{3}[0-9]{4}$/',
                // No second pending request for the same subject
                Rule::unique('review_requests', 'subject_code')
                    ->where('student_id', $this->user()->id)
                    ->where('status', ReviewRequest::PENDING)
                    ->ignore($ignoreId),
            ],
            'justification' => ['required', 'string', 'min:20', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject_code.required' => 'Subject code is required.',
            'subject_code.regex' => 'Use 3 letters followed by 4 digits, e.g. DES3083.',
            'subject_code.unique' => 'You already have a pending request for this subject.',
            'justification.required' => 'Justification is required.',
            'justification.min' => 'Please explain a little more (at least 20 characters).',
            'justification.max' => 'Justification must be 1000 characters or fewer.',
        ];
    }
}
