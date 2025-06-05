<?php

namespace App\Livewire\Admin;

use App\Models\PageView;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Term;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Livewire component for displaying basic analytics in the admin dashboard.
 */
#[Layout('components.admin-layout')]
class AnalyticsDashboard extends Component
{
    public int $totalPageViews = 0;
    public int $uniqueVisitorsToday = 0;
    public int $pageViewsToday = 0;
    public int $pageViewsLast7Days = 0;
    public int $pageViewsLast30Days = 0;

    public int $totalUsers = 0;
    public int $totalMediaFiles = 0;

    public array $topPages = [];
    public array $topReferrers = [];
    public array $topBrowsers = [];
    public array $topPlatforms = [];
    public array $pageViewsOverTimeData = [];


    /**
     * Mount the component and load initial analytics data.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadAnalyticsData();
    }

    /**
     * Load or refresh analytics data.
     *
     * @return void
     */
    public function loadAnalyticsData(): void
    {
        $this->totalPageViews = PageView::count();

        $this->totalUsers = User::count();
        $this->totalMediaFiles = Attachment::count();

        $today = Carbon::today();
        $this->uniqueVisitorsToday = PageView::whereDate('visited_at', $today)
            ->distinct('session_id')
            ->count('session_id');
        $this->pageViewsToday = PageView::whereDate('visited_at', $today)->count();

        $this->pageViewsLast7Days = PageView::where('visited_at', '>=', Carbon::now()->subDays(7))->count();
        $this->pageViewsLast30Days = PageView::where('visited_at', '>=', Carbon::now()->subDays(30))->count();

        $this->topPages = PageView::select('path', DB::raw('count(*) as views'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->toArray();

        // Consolidate topReferrers by hostname
        $rawReferrers = PageView::select('referrer', DB::raw('count(*) as views'))
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->groupBy('referrer')
            ->orderByDesc('views')
            ->limit(50) // Fetch more to ensure diverse hosts after grouping
            ->get();

        $groupedReferrers = [];
        foreach ($rawReferrers as $item) {
            if (empty($item->referrer)) {
                continue;
            }
            // Attempt to parse the host, ensure it's a string
            $host = parse_url((string) $item->referrer, PHP_URL_HOST);

            // If host is null or empty, use the original referrer (or a placeholder)
            if (empty($host)) {
                // Check if it's a simple domain without scheme (e.g., "google.com")
                // This is a basic check and might need refinement for edge cases
                if (preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', (string)$item->referrer)) {
                    $host = (string)$item->referrer;
                } else {
                     // If it's not a clear host, and not null/empty, keep it, otherwise skip or use 'unknown'
                    $host = !is_null($item->referrer) && $item->referrer !== '' ? trim((string)$item->referrer) : 'unknown';
                    if ($host === 'unknown' && $item->views === 0) continue; // Skip if unknown and no views
                }
            }
             // Ensure host is not excessively long if it's a full path fallback
            $host = mb_substr((string)$host, 0, 255);


            if (!isset($groupedReferrers[$host])) {
                $groupedReferrers[$host] = ['host' => $host, 'views' => 0, 'original_referrers' => []];
            }
            $groupedReferrers[$host]['views'] += $item->views;
            // Store one of the original full referrers for the link, if needed
            if (empty($groupedReferrers[$host]['original_referrers'])) {
                $groupedReferrers[$host]['original_referrers'][] = (string)$item->referrer;
            }
        }
        // Remove 'unknown' if it has no views or is not meaningful
        if (isset($groupedReferrers['unknown']) && $groupedReferrers['unknown']['views'] == 0) {
            unset($groupedReferrers['unknown']);
        }


        usort($groupedReferrers, fn($a, $b) => $b['views'] <=> $a['views']);
        $this->topReferrers = array_slice($groupedReferrers, 0, 10);


        $this->topBrowsers = PageView::select('browser_name', DB::raw('count(*) as views'))
            ->whereNotNull('browser_name')
            ->where('browser_name', '!=', 'unknown')
            ->groupBy('browser_name')
            ->orderByDesc('views')
            ->limit(5)
            ->get()
            ->toArray();

        $this->topPlatforms = PageView::select('platform_name', DB::raw('count(*) as views'))
            ->whereNotNull('platform_name')
            ->where('platform_name', '!=', 'unknown')
            ->groupBy('platform_name')
            ->orderByDesc('views')
            ->limit(5)
            ->get()
            ->toArray();

        // Data for Page Views Over Time Chart (last 30 days)
        $this->pageViewsOverTimeData = PageView::select(DB::raw('DATE(visited_at) as date'), DB::raw('count(*) as views'))
            ->where('visited_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(fn ($item) => ['date' => $item->date, 'views' => $item->views])
            ->toArray();
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.admin.analytics-dashboard');
    }
}
