<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Models\Service;
use App\Services\WebpImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use RefreshesFrontend;

    public function __construct(private WebpImageStore $images) {}

    /**
     * List every project in display order.
     */
    public function index(): View
    {
        return view('projects.index', [
            'projects' => Project::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for a new project.
     */
    public function create(): View
    {
        return view('projects.form', [
            'project' => new Project([
                'is_active' => true,
                'is_demo' => false,
                'year' => now()->format('Y'),
                'sort_order' => (Project::max('sort_order') ?? 0) + 1,
                'built' => [],
                'tech' => [],
                'results' => [],
                'screenshots' => [],
            ]),
            'services' => $this->serviceOptions(),
        ]);
    }

    /**
     * Save a new project.
     */
    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = Project::create($this->attributes($request, new Project));

        $this->refreshFrontend('projects');

        return redirect()->route('projects.edit', $project)->with('toast', [
            'type' => 'success',
            'message' => $project->is_active ? __('Project created and shown on the website.') : __('Project saved as hidden.'),
        ]);
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Project $project): View
    {
        return view('projects.form', [
            'project' => $project,
            'services' => $this->serviceOptions(),
        ]);
    }

    /**
     * Save changes to a project.
     */
    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $previousSlug = $project->slug;
        $previousCover = $project->cover_path;

        $project->update($this->attributes($request, $project));

        if ($previousCover !== $project->cover_path) {
            Storage::disk('public')->delete($previousCover);
        }

        if ($previousSlug !== $project->slug) {
            $this->relinkServices($previousSlug, $project->slug);
            $this->refreshFrontend('services');
        }

        $this->refreshFrontend('projects');

        return redirect()->route('projects.edit', $project)->with('toast', [
            'type' => 'success',
            'message' => __('Changes saved.'),
        ]);
    }

    /**
     * Delete a project with its images, and remove it from any services.
     */
    public function destroy(Project $project): RedirectResponse
    {
        Storage::disk('public')->delete([
            $project->cover_path,
            ...collect($project->screenshots)->pluck('path')->all(),
        ]);

        $this->relinkServices($project->slug, null);
        $project->delete();

        $this->refreshFrontend('projects');
        $this->refreshFrontend('services');

        return redirect()->route('projects.index')->with('toast', [
            'type' => 'success',
            'message' => __('Project deleted.'),
        ]);
    }

    /**
     * The validated fields to save, with the cover and screenshots stored.
     *
     * @return array<string, mixed>
     */
    private function attributes(ProjectRequest $request, Project $project): array
    {
        $attributes = $request->safe()->except(['cover', 'screenshots']);

        if ($request->hasFile('cover')) {
            $attributes['cover_path'] = $this->images->store($request->file('cover'), 'work');
        }

        $attributes['screenshots'] = $this->screenshots($request, $project);

        return $attributes;
    }

    /**
     * Build the screenshot list from the form: keep existing images, store
     * new uploads, and delete images that were removed or replaced. Existing
     * paths are only accepted if they already belong to this project.
     *
     * @return list<array{path: string, alt: string, caption: string}>
     */
    private function screenshots(ProjectRequest $request, Project $project): array
    {
        $existing = collect($project->screenshots)->pluck('path');
        $kept = [];

        foreach ($request->validated('screenshots', []) as $index => $row) {
            $path = $row['path'] ?? null;

            if ($row['remove'] ?? false) {
                continue;
            }

            if ($request->hasFile("screenshots.$index.file")) {
                $path = $this->images->store($request->file("screenshots.$index.file"), 'work');
            } elseif (! $existing->contains($path)) {
                continue;
            }

            $kept[] = ['path' => $path, 'alt' => $row['alt'] ?? '', 'caption' => $row['caption'] ?? ''];
        }

        $stillUsed = collect($kept)->pluck('path');
        Storage::disk('public')->delete($existing->diff($stillUsed)->values()->all());

        return $kept;
    }

    /**
     * Point services that listed a project at its new slug, or drop it.
     */
    private function relinkServices(string $from, ?string $to): void
    {
        Service::query()->whereJsonContains('related_projects', $from)->each(function (Service $service) use ($from, $to): void {
            $service->update([
                'related_projects' => collect($service->related_projects)
                    ->map(fn (string $slug): ?string => $slug === $from ? $to : $slug)
                    ->filter()
                    ->values()
                    ->all(),
            ]);
        });
    }

    /**
     * @return Collection<string, string>
     */
    private function serviceOptions(): Collection
    {
        return Service::query()->orderBy('sort_order')->pluck('name', 'slug');
    }
}
