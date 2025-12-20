<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVolunteerHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be an organizer and own the event
        $event = $this->route('event');

        return $this->user()
            && $this->user()->hasRole('organizer')
            && $this->user()->organization
            && $event->Organizer_ID === $this->user()->organization->Organization_ID;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:Registered,Attended,No-Show,Cancelled'],
            'total_hours' => [
                'required',
                'numeric',
                'min:0',
                'max:24',
            ],
            'role_id' => [
                'nullable',
                'exists:event_role,Role_ID',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Please select a status for the volunteer.',
            'status.in' => 'Invalid status selected.',
            'total_hours.required' => 'Total hours is required.',
            'total_hours.numeric' => 'Total hours must be a valid number.',
            'total_hours.min' => 'Total hours cannot be negative.',
            'total_hours.max' => 'Total hours cannot exceed 24 hours in a single day.',
            'role_id.exists' => 'The selected role does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'total_hours' => 'volunteer hours',
            'role_id' => 'event role',
        ];
    }
}
