<?php

namespace App\Http\Resources;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Experience
 */
class ExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'role' => $this->role,
            'company' => $this->company,
            'companyUrl' => $this->company_url,
            'type' => $this->type,
            'location' => $this->location,
            'start' => $this->start,
            'end' => $this->end,
            'summary' => $this->summary,
            'highlights' => $this->highlights,
            'tech' => $this->tech,
        ];
    }
}
