<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class ActivityLogManagement extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $logName = '';
    public $event = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $causerId = '';
    public $subjectType = '';

    // Pagination
    public $perPage = 15;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal states
    public bool $showDetailsModal = false;
    public bool $showClearLogsModal = false;

    // Selected log for details
    public ?ActivityLog $selectedLog = null;

    /**
     * Show confirmation modal for clearing logs.
     */
    public function confirmClearLogs()
    {
        Gate::authorize('delete-activity-logs');
        $this->showClearLogsModal = true;
    }

    /**
     * Cancel clearing logs.
     */
    public function cancelClearLogs()
    {
        $this->showClearLogsModal = false;
    }

    /**
     * Clear all activity logs.
     */
    public function clearLogs()
    {
        Gate::authorize('delete-activity-logs');
        ActivityLog::truncate();
        $this->showClearLogsModal = false;

        Flux::toast(
            text: 'All activity logs have been cleared.',
            heading: 'Logs Cleared',
            variant: 'success'
        );
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->reset(['search', 'logName', 'event', 'dateFrom', 'dateTo', 'causerId', 'subjectType']);
    }

    /**
     * Sort by field
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * View log details
     */
    public function viewDetails(int $logId)
    {
        $this->selectedLog = ActivityLog::find($logId);
        $this->showDetailsModal = true;
    }

    /**
     * Close log details
     */
    public function closeDetails()
    {
        $this->showDetailsModal = false;
        $this->selectedLog = null;
    }

    /**
     * Get unique log names for filter dropdown
     */
    public function getLogNamesProperty()
    {
        return ActivityLog::distinct('log_name')->pluck('log_name')->filter()->toArray();
    }

    /**
     * Get unique events for filter dropdown
     */
    public function getEventsProperty()
    {
        return ActivityLog::distinct('event')->pluck('event')->filter()->toArray();
    }

    /**
     * Get unique subject types for filter dropdown
     */
    public function getSubjectTypesProperty()
    {
        return ActivityLog::distinct('subject_type')->pluck('subject_type')->filter()->toArray();
    }

    /**
     * Render the component
     */
    public function render()
    {
        $query = ActivityLog::query()
            ->when($this->search, fn($query) => $query->where('description', 'like', '%' . $this->search . '%'))
            ->when($this->logName, fn($query) => $query->where('log_name', $this->logName))
            ->when($this->event, fn($query) => $query->where('event', $this->event))
            ->when($this->dateFrom, fn($query) => $query->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($query) => $query->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->causerId, fn($query) => $query->where('causer_id', $this->causerId))
            ->when($this->subjectType, fn($query) => $query->where('subject_type', $this->subjectType))
            ->orderBy($this->sortField, $this->sortDirection);

        $logs = $query->paginate($this->perPage);

        return view('livewire.admin.activity-log-management', [
            'logs' => $logs,
        ]);
    }
}
