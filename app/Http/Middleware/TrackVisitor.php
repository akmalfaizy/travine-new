<?php

namespace App\Http\Middleware;

use App\Services\VisitorTrackerService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            VisitorTrackerService::record($request);
        } catch (\Throwable $e) {
            // Silently ignore tracking errors so site response is never affected
        }

        return $response;
    }
}
