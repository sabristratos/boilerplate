<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use Illuminate\Http\Response;

class DynamicCssController extends Controller
{
    /**
     * Generate dynamic CSS based on settings
     *
     * @return Response
     */
    public function index(): Response
    {
        $primaryColor = Settings::get('primary_color', 'blue');
        $theme = Settings::get('theme', 'light');
        $enableDarkMode = Settings::get('enable_dark_mode', true);

        $css = "/* Dynamic settings-based styles */\n";
        $css .= "/* Generated at: " . now() . " */\n\n";

        // Light mode
        $css .= ":root {\n";
        $css .= "    --color-accent: var(--color-{$primaryColor}-500);\n";
        $css .= "    --color-accent-content: var(--color-{$primaryColor}-600);\n";
        $css .= "    --color-accent-foreground: var(--color-white);\n";
        $css .= "}\n\n";

        // Dark mode
        $css .= ".dark {\n";
        $css .= "    --color-accent: var(--color-{$primaryColor}-500);\n";
        $css .= "    --color-accent-content: var(--color-{$primaryColor}-400);\n";
        $css .= "    --color-accent-foreground: var(--color-white);\n";
        $css .= "}\n\n";

        // Theme-specific styles
        if ($theme === 'dark' && $enableDarkMode) {
            $css .= "/* Force dark mode based on theme setting */\n";
            $css .= "html:not(.dark) {\n";
            $css .= "    color-scheme: dark;\n";
            $css .= "}\n";
        } elseif ($theme === 'system' && $enableDarkMode) {
            $css .= "/* System-based dark mode */\n";
            $css .= "@media (prefers-color-scheme: dark) {\n";
            $css .= "    html:not(.dark) {\n";
            $css .= "        color-scheme: dark;\n";
            $css .= "    }\n";
            $css .= "}\n";
        }

        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
}
