<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Turn the "one per line" and comma fields into lists, drop empty FAQ
     * rows, and fill in the slug from the name.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('name', '')),
            'includes' => $this->lines('includes'),
            'ideal_for' => $this->lines('ideal_for'),
            'related_projects' => array_values(array_filter((array) $this->input('related_projects', []), 'is_string')),
            'meta_keywords' => $this->commaList('meta_keywords'),
            'billed_monthly' => $this->boolean('billed_monthly'),
            'is_active' => $this->boolean('is_active'),
            'faq' => collect($this->input('faq', []))
                ->filter(fn ($item): bool => is_array($item) && filled($item['question'] ?? null) && filled($item['answer'] ?? null))
                ->values()
                ->all(),
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
            'name' => ['required', 'string', 'max:80'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash:ascii', Rule::unique('services', 'slug')->ignore($this->route('service'))],
            'for_who' => ['required', 'string', 'max:300'],
            'price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'price_suffix' => ['nullable', 'string', 'max:20'],
            'billed_monthly' => ['boolean'],
            'time_label' => ['required', 'string', 'max:40'],
            'time' => ['required', 'string', 'max:80'],
            'includes' => ['required', 'array', 'min:1', 'max:12'],
            'includes.*' => ['string', 'max:150'],
            'heading' => ['required', 'string', 'max:160'],
            'intro' => ['required', 'string', 'max:320'],
            'story' => ['required', 'string', 'max:5000'],
            'ideal_for' => ['array', 'max:12'],
            'ideal_for.*' => ['string', 'max:150'],
            'faq' => ['array', 'max:12'],
            'faq.*.question' => ['required', 'string', 'max:200'],
            'faq.*.answer' => ['required', 'string', 'max:1000'],
            'related_projects' => ['array', 'max:6'],
            'related_projects.*' => ['string', Rule::exists('projects', 'slug')],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['array', 'max:15'],
            'meta_keywords.*' => ['string', 'max:60'],
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
            'slug' => 'web address',
            'for_who' => 'short description',
            'time_label' => 'time label',
            'includes' => "what's included",
            'ideal_for' => 'ideal for',
            'heading' => 'page heading',
            'intro' => 'page intro',
            'faq.*.question' => 'question',
            'faq.*.answer' => 'answer',
            'related_projects' => 'related projects',
            'seo_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
            'sort_order' => 'display order',
        ];
    }

    /**
     * @return list<string>
     */
    private function lines(string $key): array
    {
        return collect(preg_split('/\R/', (string) $this->input($key)))
            ->map(fn (string $line): string => trim(ltrim(trim($line), '-•*')))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    private function commaList(string $key): array
    {
        return collect(explode(',', (string) $this->input($key)))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique(fn (string $item): string => mb_strtolower($item))
            ->values()
            ->all();
    }
}
