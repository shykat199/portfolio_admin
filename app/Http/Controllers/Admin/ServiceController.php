<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    use RefreshesFrontend;

    /**
     * List every service in display order.
     */
    public function index(): View
    {
        return view('services.index', [
            'services' => Service::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for a new service.
     */
    public function create(): View
    {
        return view('services.form', [
            'service' => new Service([
                'is_active' => true,
                'time_label' => 'Delivery',
                'sort_order' => (Service::max('sort_order') ?? 0) + 1,
                'includes' => [],
                'ideal_for' => [],
                'faq' => [],
                'related_projects' => [],
            ]),
            'projects' => $this->projectOptions(),
        ]);
    }

    /**
     * Save a new service.
     */
    public function store(ServiceRequest $request): RedirectResponse
    {
        $service = Service::create($request->validated());

        $this->refreshFrontend('services');

        return redirect()->route('services.edit', $service)->with('toast', [
            'type' => 'success',
            'message' => $service->is_active ? __('Service created and shown on the website.') : __('Service saved as hidden.'),
        ]);
    }

    /**
     * Show the form for editing a service.
     */
    public function edit(Service $service): View
    {
        return view('services.form', [
            'service' => $service,
            'projects' => $this->projectOptions(),
        ]);
    }

    /**
     * Save changes to a service.
     */
    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $previousSlug = $service->slug;

        $service->update($request->validated());

        if ($previousSlug !== $service->slug) {
            Post::where('service', $previousSlug)->update(['service' => $service->slug]);
            Project::where('service', $previousSlug)->update(['service' => $service->slug]);
        }

        $this->refreshFrontend('services');

        return redirect()->route('services.edit', $service)->with('toast', [
            'type' => 'success',
            'message' => __('Changes saved.'),
        ]);
    }

    /**
     * @return Collection<string, string>
     */
    private function projectOptions(): Collection
    {
        return Project::query()->orderBy('sort_order')->pluck('title', 'slug');
    }

    /**
     * Delete a service, and unlink any blog posts that pointed to it.
     */
    public function destroy(Service $service): RedirectResponse
    {
        Post::where('service', $service->slug)->update(['service' => null]);
        Project::where('service', $service->slug)->update(['service' => null]);

        $service->delete();

        $this->refreshFrontend('services');
        $this->refreshFrontend('posts');

        return redirect()->route('services.index')->with('toast', [
            'type' => 'success',
            'message' => __('Service deleted.'),
        ]);
    }
}
