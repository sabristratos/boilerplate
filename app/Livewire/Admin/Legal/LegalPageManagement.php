<?php

namespace App\Livewire\Admin\Legal;

use App\Livewire\Traits\WithFiltering;
use App\Models\LegalPage;
use App\Services\LegalPageService;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

#[Layout('components.layouts.admin')]
class LegalPageManagement extends Component
{
    use WithPagination;
    use WithFiltering;

    #[Url(keep: true)]
    public ?string $localeFilter = null;

    public bool $confirmingDelete = false;
    public ?LegalPage $deletingPage = null;

    public function mount(): void
    {
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
    }

    public function hasFilters(): bool
    {
        return !empty($this->search) || !is_null($this->localeFilter);
    }

    #[On('legal-page-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the page list.
    }

    #[On('confirm-delete-page')]
    public function confirmDelete(LegalPage $legalPage): void
    {
        $this->authorize('delete', $legalPage);
        $this->deletingPage = $legalPage;
        $this->confirmingDelete = true;
    }

    public function delete(LegalPageService $legalPageService): void
    {
        if (!$this->deletingPage) {
            return;
        }

        $this->authorize('delete', $this->deletingPage);

        try {
            $legalPageService->deleteLegalPage($this->deletingPage);
            Flux::toast(
                text: __('Page deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->dispatch('legal-page-saved');
        } catch (\Exception $e) {
            Log::error('Failed to delete page: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete page. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }

        $this->confirmingDelete = false;
        $this->deletingPage = null;
    }

    protected function getSortColumn(): string
    {
        $translatableAttributes = (new LegalPage())->getTranslatableAttributes();
        if (in_array($this->sortBy, $translatableAttributes)) {
            $locale = $this->localeFilter ?: config('app.fallback_locale');
            return $this->sortBy . '->' . $locale;
        }

        return $this->sortBy;
    }

    public function render()
    {
        $pages = LegalPage::query()
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    foreach (config('app.available_locales', []) as $localeCode => $localeName) {
                        $subQuery->orWhere("title->{$localeCode}", 'like', '%' . $this->search . '%')
                                 ->orWhere("slug->{$localeCode}", 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->when($this->localeFilter, function ($query) {
                $query->whereNotNull("title->{$this->localeFilter}")
                      ->where("title->{$this->localeFilter}", '!=', '');
            }, function ($query) {
                $query->whereJsonLength('title', '>', 0);
            })
            ->orderBy($this->getSortColumn(), $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.legal.legal-page-management', [
            'pages' => $pages,
            'locales' => config('app.available_locales', ['en' => 'English']),
        ]);
    }
}
