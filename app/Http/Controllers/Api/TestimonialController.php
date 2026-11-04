<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestimonialController extends Controller
{
    /**
     * Testimonials shown on the website, in display order.
     */
    public function __invoke(): AnonymousResourceCollection
    {
        return TestimonialResource::collection(Testimonial::query()->active()->get());
    }
}
