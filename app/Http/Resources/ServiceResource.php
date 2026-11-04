<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'forWho' => $this->for_who,
            'price' => $this->price,
            'priceSuffix' => $this->price_suffix,
            'billedMonthly' => $this->billed_monthly,
            'timeLabel' => $this->time_label,
            'time' => $this->time,
            'includes' => $this->includes,
            'heading' => $this->heading,
            'intro' => $this->intro,
            'story' => $this->storyParagraphs(),
            'idealFor' => $this->ideal_for,
            'faq' => $this->faq,
            'relatedProjects' => $this->related_projects,
            'seo' => $this->seo(),
        ];
    }
}
