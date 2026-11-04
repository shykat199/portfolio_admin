<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * The site secret is checked by middleware before this runs.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * A missing or malformed visitor IP is dropped rather than rejecting a
     * genuine enquiry.
     */
    protected function prepareForValidation(): void
    {
        if (filter_var($this->input('ip_address'), FILTER_VALIDATE_IP) === false) {
            $this->merge(['ip_address' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200'],
            'business' => ['required', 'string', 'max:150'],
            'website' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'ip_address' => ['nullable', 'ip'],
            'user_agent' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
