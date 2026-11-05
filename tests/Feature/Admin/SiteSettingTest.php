<?php

namespace Tests\Feature\Admin;

use App\Models\Experience;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Http::fake();
        config(['blog.frontend_url' => 'http://frontend.test', 'blog.revalidate_secret' => 'secret']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return [
            'about_title' => 'Hello, I’m Shykat',
            'about_story' => "First paragraph.\n\nSecond paragraph.",
            'about_photo_alt' => 'Portrait',
            'about_reasons_title' => 'Why you can rely on me',
            'about_reasons' => ['One', ' Two ', ''],
            'hero_prefix' => 'I build',
            'hero_words' => ['websites', '', 'apps'],
            'hero_headline' => 'Fast websites that win more customers.',
            'hero_highlight' => 'more customers',
            'hero_supporting' => 'Hello there.',
            'seo_title' => 'Home title',
            'seo_description' => 'Home description.',
            'seo_keywords' => 'one, two, One',
            'person_name' => 'Shykat Roy',
            'person_job_title' => 'Full-Stack Web Developer',
            'person_email' => 'me@example.com',
            'person_phone' => '+880 1712-345678',
            'person_skills' => ['Laravel', '', 'React'],
            'person_languages' => ['English'],
            'linkedin_url' => 'https://www.linkedin.com/in/me',
            'facebook_url' => 'https://www.facebook.com/me',
            'whatsapp_number' => '+880 1712-345678',
            ...$overrides,
        ];
    }

    public function test_guests_cannot_change_settings(): void
    {
        $this->get(route('site-settings.edit'))->assertRedirect(route('login'));
        $this->put(route('site-settings.update'), $this->validData())->assertRedirect(route('login'));
    }

    public function test_settings_can_be_saved_with_images(): void
    {
        $this->actingAs(User::factory()->create())->get(route('site-settings.edit'))->assertOk();

        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData([
                'logo' => UploadedFile::fake()->image('logo.png', 256, 256),
                'favicon' => UploadedFile::fake()->image('icon.png', 512, 512),
                'about_photo' => UploadedFile::fake()->image('me.jpg', 600, 500),
            ]))
            ->assertRedirect(route('site-settings.edit'))
            ->assertSessionHas('toast', ['type' => 'success', 'message' => 'Site settings saved.']);

        $settings = SiteSetting::current();
        $this->assertSame(['First paragraph.', 'Second paragraph.'], $settings->aboutParagraphs());
        $this->assertSame(['One', 'Two'], $settings->about_reasons);
        $this->assertSame(['websites', 'apps'], $settings->hero_words);
        $this->assertSame(['one', 'two'], $settings->seo_keywords);
        $this->assertSame(['Laravel', 'React'], $settings->person_skills);
        $this->assertSame('8801712345678', $settings->whatsapp_number);
        $this->assertSame([600, 500], [$settings->about_photo_width, $settings->about_photo_height]);
        Storage::disk('public')->assertExists([$settings->logo_path, $settings->favicon_path, $settings->about_photo_path]);
        Http::assertSent(fn ($request) => $request['tag'] === 'settings');
    }

    public function test_the_favicon_must_be_a_square_png_and_links_must_be_https(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData([
                'favicon' => UploadedFile::fake()->image('wide.png', 200, 100),
                'facebook_url' => 'javascript:alert(1)',
            ]))
            ->assertSessionHasErrors(['favicon', 'facebook_url']);
    }

    public function test_the_coloured_words_must_be_in_the_headline(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData(['hero_highlight' => 'not in it']))
            ->assertSessionHasErrors('hero_highlight');
    }

    public function test_the_api_includes_the_person_with_their_current_job(): void
    {
        SiteSetting::current()->update(['person_name' => 'Shykat Roy', 'person_job_title' => 'Developer', 'person_skills' => ['Laravel']]);
        Experience::factory()->create(['company' => 'PFEC Global', 'company_url' => 'https://pfecglobal.com', 'end' => null]);
        Experience::factory()->create(['company' => 'Old Co', 'end' => '2024']);

        $this->getJson(route('api.settings.show'))
            ->assertJsonPath('data.person.name', 'Shykat Roy')
            ->assertJsonPath('data.person.skills', ['Laravel'])
            ->assertJsonPath('data.person.worksFor', ['name' => 'PFEC Global', 'url' => 'https://pfecglobal.com']);
    }

    public function test_images_can_be_removed(): void
    {
        Storage::disk('public')->put('brand/logo.png', 'x');
        SiteSetting::current()->update(['logo_path' => 'brand/logo.png']);

        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData(['remove_logo' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull(SiteSetting::current()->logo_path);
        Storage::disk('public')->assertMissing('brand/logo.png');
    }

    public function test_a_resume_can_be_uploaded_and_removed(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData([
                'resume' => UploadedFile::fake()->create('cv.pdf', 300, 'application/pdf'),
            ]))
            ->assertSessionHasNoErrors();

        $path = SiteSetting::current()->resume_path;
        Storage::disk('public')->assertExists($path);
        $this->getJson(route('api.settings.show'))->assertJsonPath('data.resume', Storage::disk('public')->url($path));

        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData(['remove_resume' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull(SiteSetting::current()->resume_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_the_resume_must_be_a_pdf(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData([
                'resume' => UploadedFile::fake()->create('cv.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ]))
            ->assertSessionHasErrors('resume');
    }

    public function test_home_page_sections_can_be_switched_off(): void
    {
        $sections = array_fill_keys(array_keys(SiteSetting::SECTIONS), '1');

        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData(['sections' => [...$sections, 'work' => '0', 'faq' => '0']]))
            ->assertSessionHasNoErrors();

        $settings = SiteSetting::current();
        $this->assertSame(['work', 'faq'], $settings->hidden_sections);
        $this->assertFalse($settings->showsSection('work'));
        $this->assertTrue($settings->showsSection('services'));
        $this->getJson(route('api.settings.show'))->assertJsonPath('data.hiddenSections', ['work', 'faq']);

        $this->actingAs(User::factory()->create())
            ->put(route('site-settings.update'), $this->validData(['sections' => $sections]))
            ->assertSessionHasNoErrors();

        $this->assertSame([], SiteSetting::current()->hidden_sections);
    }

    public function test_the_api_returns_settings_for_the_website(): void
    {
        SiteSetting::current()->update(['about_story' => "A.\n\nB.", 'facebook_url' => 'https://www.facebook.com/me']);

        $this->getJson(route('api.settings.show'))
            ->assertOk()
            ->assertJsonPath('data.about.story', ['A.', 'B.'])
            ->assertJsonPath('data.social.facebook', 'https://www.facebook.com/me')
            ->assertJsonPath('data.logo', null)
            ->assertJsonPath('data.hero', null);
    }
}
