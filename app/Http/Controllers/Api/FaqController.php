<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FaqController extends Controller
{
    /**
     * Home page questions shown on the website, in display order.
     */
    public function __invoke(): AnonymousResourceCollection
    {
        return FaqResource::collection(Faq::query()->active()->get());
    }
}
