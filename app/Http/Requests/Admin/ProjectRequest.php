<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Turn the "one per line" and comma fields into lists, and fill in the
     * slug from the title.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title', '')),
            'built' => $this->lines('built'),
            'results' => $this->lines('results'),
            'tech' => $this->commaList('tech'),
            'meta_keywords' => $this->commaList('meta_keywords'),
            'is_demo' => $this->boolean('is_demo'),
            'is_active' => $this->boolean('is_active'),
            'screenshots' => collect($this->input('screenshots', []))->filter(fn ($row): bool => is_array($row))->values()->all(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Project|null $project */
        $project = $this->route('project');
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash:ascii', Rule::unique('projects', 'slug')->ignore($project)],
            'is_demo' => ['boolean'],
            'category' => ['required', 'string', 'max:60'],
            'year' => ['required', 'digits:4'],
            'live_url' => ['nullable', 'url:http,https', 'max:2048'],
            'service' => ['nullable', Rule::exists('services', 'slug')],
            'problem' => ['required', 'string', 'max:300'],
            'solution' => ['required', 'string', 'max:300'],
            'result' => ['required', 'string', 'max:300'],
            'overview' => ['required', 'string', 'max:2000'],
            'client_problem' => ['required', 'string', 'max:2000'],
            'built' => ['required', 'array', 'min:1'],
            'built.*' => ['string', 'max:200'],
            'tech' => ['array', 'max:20'],
            'tech.*' => ['string', 'max:40'],
            'results' => ['required', 'array', 'min:1', 'max:10'],
            'results.*' => ['string', 'max:200'],
            'cover' => [$project?->cover_path ? 'nullable' : 'required', ...$image, 'dimensions:min_width=800,min_height=400'],
            'cover_alt' => ['required', 'string', 'max:200'],
            'screenshots' => ['array', 'max:12'],
            'screenshots.*.path' => ['nullable', 'string', Rule::in(collect($project?->screenshots)->pluck('path')->all())],
            'screenshots.*.file' => ['nullable', ...$image],
            'screenshots.*.alt' => ['nullable', 'string', 'max:200'],
            'screenshots.*.caption' => ['nullable', 'string', 'max:200'],
            'screenshots.*.remove' => ['nullable', 'boolean'],
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
            'client_problem' => "client's problem",
            'built' => 'what you built',
            'results' => 'results',
            'cover' => 'cover image',
            'cover_alt' => 'cover description',
            'screenshots.*.file' => 'screenshot',
            'screenshots.*.caption' => 'caption',
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
