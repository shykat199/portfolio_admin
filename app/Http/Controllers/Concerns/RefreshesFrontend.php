<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

trait RefreshesFrontend
{
    /**
     * Ask the portfolio website to refresh one kind of content ("posts" or
     * "services"). A failure is logged but never blocks saving; the website
     * also refreshes on its own every few minutes.
     */
    protected function refreshFrontend(string $tag): void
    {
        $url = config('blog.frontend_url');
        $secret = config('blog.revalidate_secret');

        if (blank($url) || blank($secret)) {
            return;
        }

        try {
            Http::timeout(3)
                ->withHeaders(['x-revalidate-secret' => $secret])
                ->post(rtrim($url, '/').'/api/revalidate', ['tag' => $tag]);
        } catch (Throwable $exception) {
            Log::warning('Could not refresh the portfolio website.', ['tag' => $tag, 'error' => $exception->getMessage()]);
        }
    }
}
