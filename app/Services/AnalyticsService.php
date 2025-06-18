<?php

namespace App\Services;

use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

/**
 * Service class for handling analytics tracking.
 */
class AnalyticsService
{
    protected Agent $agentParser;

    /**
     * Constructor to inject the Agent instance.
     */
    public function __construct()
    {
        $this->agentParser = new Agent();
    }

    /**
     * Tracks a page view.
     *
     * Gathers data from the request and stores it as a PageView record.
     * Uses Jenssegers/Agent for detailed user agent parsing.
     *
     * @param Request $request The current HTTP request.
     */
    public function track(Request $request): void
    {
        try {
            $userAgentString = $request->userAgent();
            $this->agentParser->setUserAgent($userAgentString);

            $deviceType = 'unknown';
            if ($this->agentParser->isDesktop()) {
                $deviceType = 'desktop';
            } elseif ($this->agentParser->isTablet()) {
                $deviceType = 'tablet';
            } elseif ($this->agentParser->isMobile()) {
                $deviceType = 'mobile';
            } elseif ($this->agentParser->isBot()) {
                $deviceType = 'bot';
            }

            $browserName = $this->agentParser->browser();
            if ($browserName === false) {
                $browserName = 'unknown';
            }

            $platformName = $this->agentParser->platform();
            if ($platformName === false) {
                $platformName = 'unknown';
            }


            PageView::create([
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
                'path' => mb_substr($request->path(), 0, 255),
                'ip_address' => $request->ip(),
                'user_agent' => $userAgentString,
                'referrer' => $request->headers->get('referer') ? mb_substr($request->headers->get('referer'), 0, 2048) : null,
                'utm_source' => $request->query('utm_source'),
                'utm_medium' => $request->query('utm_medium'),
                'utm_campaign' => $request->query('utm_campaign'),
                'utm_term' => $request->query('utm_term'),
                'utm_content' => $request->query('utm_content'),
                'device_type' => $deviceType,
                'browser_name' => $browserName ?: __('unknown'),
                'platform_name' => $platformName ?: __('unknown'),
                'visited_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error(__('Error tracking page view: ') . $e->getMessage(), [
                'path' => $request->path(),
                'ip' => $request->ip(),
                'exception' => $e
            ]);
        }
    }
}
