<?php

declare(strict_types=1);

if (! function_exists('is_active')) {
    /**
     * Checks if the current route is active.
     */
    function is_active(string|array $routes): bool
    {
        if (is_string($routes)) {
            return request()->routeIs($routes);
        }

        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }

        return false;
    }
} 