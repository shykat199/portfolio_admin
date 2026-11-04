<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'isDemo' => $this->is_demo,
            'category' => $this->category,
            'year' => $this->year,
            'relatedService' => $this->service,
            'problem' => $this->problem,
            'solution' => $this->solution,
            'result' => $this->result,
            'overview' => $this->overview,
            'clientProblem' => $this->client_problem,
            'built' => $this->built,
            'tech' => $this->tech,
            'results' => $this->results,
            'cover' => ['src' => $this->cover_url, 'alt' => $this->cover_alt],
            'screenshots' => $this->screenshotUrls(),
            'seo' => $this->seo(),
        ];
    }
}
