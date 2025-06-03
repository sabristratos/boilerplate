<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.admin-layout')]
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

    // Selected log for details
    public $selectedLog = null;

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
    public function viewDetails(ActivityLog $log)
    {
        $this->selectedLog = $log;
    }

    /**
     * Close log details
     */
    public function closeDetails()
    {
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
            ->when($this->search, function ($query) {
                return $query->where('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->logName, function ($query) {
                return $query->where('log_name', $this->logName);
            })
            ->when($this->event, function ($query) {
                return $query->where('event', $this->event);
            })
            ->when($this->dateFrom, function ($query) {
                return $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                return $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->causerId, function ($query) {
                return $query->where('causer_id', $this->causerId);
            })
            ->when($this->subjectType, function ($query) {
                return $query->where('subject_type', $this->subjectType);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $logs = $query->paginate($this->perPage);

        return view('livewire.admin.activity-log-management', [
            'logs' => $logs,
        ]);
    }
}
