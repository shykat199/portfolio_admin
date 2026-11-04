<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    use RefreshesFrontend;

    /**
     * List every testimonial in display order.
     */
    public function index(): View
    {
        return view('testimonials.index', [
            'testimonials' => Testimonial::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for a new testimonial.
     */
    public function create(): View
    {
        return view('testimonials.form', [
            'testimonial' => new Testimonial(['is_active' => true, 'sort_order' => (Testimonial::max('sort_order') ?? 0) + 1]),
        ]);
    }

    /**
     * Save a new testimonial.
     */
    public function store(TestimonialRequest $request): RedirectResponse
    {
        Testimonial::create($this->attributes($request, new Testimonial));

        $this->refreshFrontend('testimonials');

        return redirect()->route('testimonials.index')->with('toast', [
            'type' => 'success',
            'message' => __('Testimonial added.'),
        ]);
    }

    /**
     * Show the form for editing a testimonial.
     */
    public function edit(Testimonial $testimonial): View
    {
        return view('testimonials.form', ['testimonial' => $testimonial]);
    }

    /**
     * Save changes to a testimonial.
     */
    public function update(TestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update($this->attributes($request, $testimonial));

        $this->refreshFrontend('testimonials');

        return redirect()->route('testimonials.index')->with('toast', [
            'type' => 'success',
            'message' => __('Changes saved.'),
        ]);
    }

    /**
     * Delete a testimonial and its photo.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->photo_path) {
            Storage::disk('public')->delete($testimonial->photo_path);
        }

        $testimonial->delete();

        $this->refreshFrontend('testimonials');

        return redirect()->route('testimonials.index')->with('toast', [
            'type' => 'success',
            'message' => __('Testimonial deleted.'),
        ]);
    }

    /**
     * The fields to save, with the photo stored, replaced or removed.
     *
     * @return array<string, mixed>
     */
    private function attributes(TestimonialRequest $request, Testimonial $testimonial): array
    {
        $attributes = $request->safe()->except(['photo', 'remove_photo']);
        $replacing = $request->hasFile('photo') || $request->boolean('remove_photo');

        if ($replacing && $testimonial->photo_path) {
            Storage::disk('public')->delete($testimonial->photo_path);
            $attributes['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            $attributes['photo_path'] = $request->file('photo')->store('testimonials', 'public');
        }

        return $attributes;
    }
}
