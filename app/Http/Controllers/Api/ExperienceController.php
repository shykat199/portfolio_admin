<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExperienceController extends Controller
{
    /**
     * Jobs shown on the website, in display order.
     */
    public function __invoke(): AnonymousResourceCollection
    {
        return ExperienceResource::collection(Experience::query()->active()->get());
    }
}
