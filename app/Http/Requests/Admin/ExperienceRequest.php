<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Turn the "one per line" and comma fields into lists. Ticking "I work
     * here now" clears the end date.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'highlights' => collect(preg_split('/\R/', (string) $this->input('highlights')))
                ->map(fn (string $line): string => trim(ltrim(trim($line), '-•*')))
                ->filter()
                ->values()
                ->all(),
            'tech' => collect(explode(',', (string) $this->input('tech')))
                ->map(fn (string $item): string => trim($item))
                ->filter()
                ->unique(fn (string $item): string => mb_strtolower($item))
                ->values()
                ->all(),
            'end' => $this->boolean('is_current') ? null : $this->input('end'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', 'max:120'],
            'company' => ['required', 'string', 'max:120'],
            'company_url' => ['nullable', 'url:http,https', 'max:255'],
            'type' => ['required', 'string', 'max:40'],
            'location' => ['nullable', 'string', 'max:120'],
            'start' => ['required', 'string', 'max:40'],
            'end' => ['nullable', 'string', 'max:40'],
            'is_current' => ['boolean'],
            'summary' => ['required', 'string', 'max:1000'],
            'highlights' => ['array', 'max:10'],
            'highlights.*' => ['string', 'max:250'],
            'tech' => ['array', 'max:20'],
            'tech.*' => ['string', 'max:40'],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_url' => 'company website',
            'type' => 'job type',
            'start' => 'start date',
            'end' => 'end date',
            'sort_order' => 'display order',
        ];
    }
}
