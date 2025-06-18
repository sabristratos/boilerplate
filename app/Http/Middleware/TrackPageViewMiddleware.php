<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to track page views.
 */
class TrackPageViewMiddleware
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(protected AnalyticsService $analyticsService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') &&
            $response->isSuccessful() &&
            !$request->hasHeader('X-Livewire') &&
            !$this->isExcludedPath($request) &&
            !$this->isBot($request->userAgent()))
        {
            $this->analyticsService->track($request);
        }

        return $response;
    }

    /**
     * Determine if the given request path should be excluded from tracking.
     */
    protected function isExcludedPath(Request $request): bool
    {
        $excludedPaths = [
            'admin/*',
            'livewire/*',
            '_ignition/*',
            'telescope/*',
        ];

        foreach ($excludedPaths as $excludedPath) {
            if ($request->fullUrlIs($excludedPath) || $request->is($excludedPath)) {
                return true;
            }
        }

        // Exclude common asset file extensions
        $path = $request->path();
        $excludedExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot', 'map'];
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), $excludedExtensions, true);
    }

    /**
     * Determine if the request is likely from a bot.
     *
     * @param string|null $userAgent The user agent string.
     */
    protected function isBot(?string $userAgent): bool
    {
        if ($userAgent === null || $userAgent === '' || $userAgent === '0') {
            return false;
        }

        $botKeywords = [
            'bot', 'spider', 'crawler', 'slurp', 'mediapartners', 'adsbot',
            'googlebot', 'bingbot', 'yahoo', 'duckduckbot', 'baiduspider',
            'yandexbot', 'aolbuild', 'dataprovider', 'pinterest', 'python-requests',
            'httpclient', 'curl', 'wget', 'postman', 'lighthouse'
        ];

        foreach ($botKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
}
