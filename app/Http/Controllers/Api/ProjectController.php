<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /**
     * Projects shown on the website, in display order.
     */
    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(Project::query()->active()->get());
    }

    /**
     * One project. Hidden projects are not found.
     */
    public function show(string $slug): ProjectResource
    {
        return new ProjectResource(
            Project::query()->active()->where('slug', $slug)->firstOrFail(),
        );
    }
}
