<?php

namespace App\Http\Middleware;

use App\Services\PageViewTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function __construct(
        protected PageViewTracker $tracker
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($response->isSuccessful() || $response->isRedirection()) {
                $this->tracker->record($request);
            }
        } catch (\Throwable) {
            // Ne jamais bloquer la navigation visiteur pour un problème de stats.
        }

        return $response;
    }
}
