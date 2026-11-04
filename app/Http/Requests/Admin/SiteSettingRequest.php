<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingRequest extends FormRequest
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
            'about_reasons' => $this->cleanList('about_reasons'),
            'hero_words' => $this->cleanList('hero_words'),
            'person_skills' => $this->cleanList('person_skills'),
            'person_languages' => $this->cleanList('person_languages'),
            'seo_keywords' => collect(explode(',', (string) $this->input('seo_keywords')))
                ->map(fn (string $item): string => trim($item))
                ->filter()
                ->unique(fn (string $item): string => mb_strtolower($item))
                ->values()
                ->all(),
            'whatsapp_number' => preg_replace('/\D+/', '', (string) $this->input('whatsapp_number')) ?: null,
            'remove_logo' => $this->boolean('remove_logo'),
            'remove_favicon' => $this->boolean('remove_favicon'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'file', 'mimes:svg,png,jpg,jpeg,webp', 'max:1024'],
            'remove_logo' => ['boolean'],
            'favicon' => ['nullable', 'image', 'mimes:png', 'max:512', 'dimensions:min_width=48,ratio=1'],
            'remove_favicon' => ['boolean'],
            'linkedin_url' => ['nullable', 'url:https', 'max:255'],
            'github_url' => ['nullable', 'url:https', 'max:255'],
            'facebook_url' => ['nullable', 'url:https', 'max:255'],
            'instagram_url' => ['nullable', 'url:https', 'max:255'],
            'x_url' => ['nullable', 'url:https', 'max:255'],
            'whatsapp_number' => ['nullable', 'digits_between:8,15'],
            'hero_prefix' => ['required', 'string', 'max:40'],
            'hero_words' => ['required', 'array', 'min:1', 'max:10'],
            'hero_words.*' => ['string', 'max:40'],
            'hero_headline' => ['required', 'string', 'max:200'],
            'hero_highlight' => ['nullable', 'string', 'max:80'],
            'hero_supporting' => ['required', 'string', 'max:400'],
            'seo_title' => ['required', 'string', 'max:70'],
            'seo_description' => ['required', 'string', 'max:320'],
            'seo_keywords' => ['array', 'max:20'],
            'seo_keywords.*' => ['string', 'max:60'],
            'person_name' => ['required', 'string', 'max:100'],
            'person_job_title' => ['required', 'string', 'max:120'],
            'person_description' => ['nullable', 'string', 'max:300'],
            'person_email' => ['required', 'email', 'max:200'],
            'person_phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9 ()-]{6,30}$/'],
            'person_city' => ['nullable', 'string', 'max:80'],
            'person_country' => ['nullable', 'string', 'max:80'],
            'person_skills' => ['array', 'max:30'],
            'person_skills.*' => ['string', 'max:60'],
            'person_languages' => ['array', 'max:10'],
            'person_languages.*' => ['string', 'max:40'],
            'about_title' => ['required', 'string', 'max:120'],
            'about_story' => ['required', 'string', 'max:4000'],
            'about_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=300,min_height=300'],
            'about_photo_alt' => ['required', 'string', 'max:200'],
            'about_reasons_title' => ['required', 'string', 'max:120'],
            'about_reasons' => ['array', 'max:8'],
            'about_reasons.*' => ['string', 'max:200'],
        ];
    }

    /**
     * The coloured words must appear in the headline exactly.
     *
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [function ($validator): void {
            $highlight = (string) $this->input('hero_highlight');
            if ($highlight !== '' && ! str_contains((string) $this->input('hero_headline'), $highlight)) {
                $validator->errors()->add('hero_highlight', __('The coloured words must appear exactly in the headline.'));
            }
        }];
    }

    /**
     * Rows from an "Add more" list, trimmed, without empty rows.
     *
     * @return list<string>
     */
    private function cleanList(string $key): array
    {
        return collect((array) $this->input($key, []))
            ->filter(fn ($item): bool => is_string($item))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'favicon.dimensions' => __('The favicon must be square and at least 48 × 48 pixels.'),
            'favicon.mimes' => __('The favicon must be a PNG file.'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'linkedin_url' => 'LinkedIn link',
            'github_url' => 'GitHub link',
            'facebook_url' => 'Facebook link',
            'instagram_url' => 'Instagram link',
            'x_url' => 'X link',
            'whatsapp_number' => 'WhatsApp number',
            'hero_prefix' => 'typing line start',
            'hero_words' => 'typed words',
            'hero_headline' => 'headline',
            'hero_highlight' => 'coloured words',
            'hero_supporting' => 'text under the headline',
            'seo_title' => 'meta title',
            'seo_description' => 'meta description',
            'seo_keywords' => 'meta keywords',
            'person_name' => 'name',
            'person_job_title' => 'job title',
            'person_description' => 'short bio',
            'person_email' => 'email',
            'person_phone' => 'phone',
            'person_city' => 'city',
            'person_country' => 'country',
            'person_skills' => 'skills',
            'person_languages' => 'languages',
            'about_title' => 'heading',
            'about_story' => 'about text',
            'about_photo' => 'photo',
            'about_photo_alt' => 'photo description',
            'about_reasons_title' => 'list heading',
            'about_reasons' => 'reasons',
        ];
    }
}
