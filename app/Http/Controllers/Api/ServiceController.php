<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends Controller
{
    /**
     * Services shown on the website, in display order.
     */
    public function index(): AnonymousResourceCollection
    {
        return ServiceResource::collection(Service::query()->active()->get());
    }

    /**
     * One service. Hidden services are not found.
     */
    public function show(string $slug): ServiceResource
    {
        return new ServiceResource(
            Service::query()->active()->where('slug', $slug)->firstOrFail(),
        );
    }
}
