<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TechItemResource;
use App\Models\TechItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TechItemController extends Controller
{
    /**
     * The tech stack shown on the website, in display order.
     */
    public function __invoke(): AnonymousResourceCollection
    {
        return TechItemResource::collection(TechItem::query()->active()->get());
    }
}
