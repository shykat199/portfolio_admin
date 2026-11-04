<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySiteSecret
{
    /**
     * Only let requests through that carry the secret shared with the
     * portfolio website (FRONTEND_REVALIDATE_SECRET), so the public API can't
     * be used to post messages directly.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('blog.revalidate_secret');
        $given = (string) $request->header('x-site-secret');

        if ($expected === '' || ! hash_equals($expected, $given)) {
            return response()->json(['message' => 'Unauthorised.'], 401);
        }

        return $next($request);
    }
}
