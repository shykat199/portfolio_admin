<?php

namespace App\Http\Requests\Admin;

use App\Models\TechItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TechItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'is_active' => $this->boolean('is_active'),
            'remove_logo' => $this->boolean('remove_logo'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var TechItem|null $item */
        $item = $this->route('tech');

        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('tech_items', 'name')->ignore($item)],
            'logo' => ['nullable', 'file', 'mimes:svg,png,jpg,jpeg,webp', 'max:1024'],
            'remove_logo' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['sort_order' => 'display order'];
    }
}
