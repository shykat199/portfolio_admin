<?php

namespace App\Http\Requests\Admin;

use App\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Fill in the slug from the title, turn the comma-separated tags into a
     * list, and drop empty FAQ rows before validating.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('slug') ?: $this->input('title', '')),
            'tags' => $this->commaList('tags'),
            'meta_keywords' => $this->commaList('meta_keywords'),
            'faq' => collect($this->input('faq', []))
                ->filter(fn ($item): bool => is_array($item) && filled($item['question'] ?? null) && filled($item['answer'] ?? null))
                ->values()
                ->all(),
        ]);
    }

    /**
     * Split a comma-separated field into a clean list without duplicates.
     *
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash:ascii', Rule::unique('posts', 'slug')->ignore($this->route('post'))],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['array', 'max:15'],
            'meta_keywords.*' => ['string', 'max:60'],
            'description' => ['required', 'string', 'max:300'],
            'category' => ['required', 'string', 'max:60'],
            'tags' => ['array', 'max:10'],
            'tags.*' => ['string', 'max:40'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=600,min_height=300'],
            'cover_alt' => ['nullable', 'required_with:cover', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:100000'],
            'faq' => ['array', 'max:12'],
            'faq.*.question' => ['required', 'string', 'max:200'],
            'faq.*.answer' => ['required', 'string', 'max:1000'],
            'service' => ['nullable', Rule::exists('services', 'slug')],
            'status' => ['required', Rule::enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'faq.*.question' => 'question',
            'faq.*.answer' => 'answer',
            'slug' => 'web address',
            'description' => 'summary',
            'body' => 'article',
            'tags' => 'topics',
            'cover' => 'cover image',
            'cover_alt' => 'image description',
            'seo_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }
}
