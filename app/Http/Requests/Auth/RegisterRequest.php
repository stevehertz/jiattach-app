<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = date('Y');
        $maxYear = $currentYear + 10;
        return [
             // Personal Info
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15|unique:users',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|string|in:male,female,other,prefer_not_to_say',
            'national_id' => 'required|string|max:20|unique:users',

            // Disability Logic
            'disability_status' => 'required|in:none,mobility,visual,hearing,cognitive,other,prefer_not_to_say',
            'disability_details' => 'nullable|required_unless:disability_status,none,prefer_not_to_say|string|max:500',

            // Academic Info
            'student_reg_number' => 'required|string|max:50|unique:student_profiles',
            'institution_name' => 'required|string|max:255',
            'institution_type' => 'required|string|in:university,college,polytechnic,technical',
            'course_name' => 'required|string|max:255',
            'course_level' => 'required|string|in:certificate,diploma,bachelor,masters,phd',
            'year_of_study' => 'required|integer|min:1|max:6',
            'expected_graduation_year' => 'required|integer|min:' . $currentYear . '|max:' . $maxYear,
            'cgpa' => 'nullable|numeric|min:0|max:4.0',

            // Skills & Location
            'county' => 'required|string|max:100',
            'preferred_location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:255',
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:50',

            // Security
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => 'required|accepted',
        ];
    }
}
