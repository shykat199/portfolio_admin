<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExperienceRequest;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExperienceController extends Controller
{
    use RefreshesFrontend;

    /**
     * List every job in display order.
     */
    public function index(): View
    {
        return view('experience.index', [
            'jobs' => Experience::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for a new job.
     */
    public function create(): View
    {
        return view('experience.form', [
            'job' => new Experience(['is_active' => true, 'type' => 'Full-time', 'sort_order' => 0, 'highlights' => [], 'tech' => []]),
        ]);
    }

    /**
     * Save a new job.
     */
    public function store(ExperienceRequest $request): RedirectResponse
    {
        $job = Experience::create($request->safe()->except('is_current'));

        $this->refreshFrontend('experience');

        return redirect()->route('experience.index')->with('toast', [
            'type' => 'success',
            'message' => __(':role at :company added.', ['role' => $job->role, 'company' => $job->company]),
        ]);
    }

    /**
     * Show the form for editing a job.
     */
    public function edit(Experience $experience): View
    {
        return view('experience.form', ['job' => $experience]);
    }

    /**
     * Save changes to a job.
     */
    public function update(ExperienceRequest $request, Experience $experience): RedirectResponse
    {
        $experience->update($request->safe()->except('is_current'));

        $this->refreshFrontend('experience');

        return redirect()->route('experience.index')->with('toast', [
            'type' => 'success',
            'message' => __('Changes saved.'),
        ]);
    }

    /**
     * Delete a job.
     */
    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();

        $this->refreshFrontend('experience');

        return redirect()->route('experience.index')->with('toast', [
            'type' => 'success',
            'message' => __('Job removed.'),
        ]);
    }
}
