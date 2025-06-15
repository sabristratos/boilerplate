<?php

namespace App\Livewire\Admin\Legal;

use App\Models\LegalPage;
use App\Services\LegalPageService;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Url;

#[Layout('components.layouts.admin')]
class EditLegalPage extends Component
{
    public LegalPage $legalPage;

    #[Rule('array')]
    public array $title = [];

    #[Rule('array')]
    public array $slug = [];

    #[Rule('array')]
    public array $content = [];

    #[Rule('boolean')]
    public bool $is_published = false;
    
    public array $locales;

    #[Url(as: 'tab', keep: true)]
    public ?string $currentLocale = null;

    public function mount(LegalPage $legalPage): void
    {
        $this->legalPage = $legalPage;
        $this->locales = config('app.available_locales', ['en' => 'English']);

        if (is_null($this->currentLocale) || ! array_key_exists($this->currentLocale, $this->locales)) {
            $this->currentLocale = array_key_first($this->locales);
        }

        if ($this->legalPage->exists) {
            $this->authorize('update', $this->legalPage);
            foreach ($this->locales as $localeCode => $localeName) {
                $this->title[$localeCode] = $this->legalPage->getTranslation('title', $localeCode, false);
                $this->slug[$localeCode] = $this->legalPage->getTranslation('slug', $localeCode, false);
                $this->content[$localeCode] = $this->legalPage->getTranslation('content', $localeCode, false);
            }
            $this->is_published = $this->legalPage->is_published;
        } else {
            $this->authorize('create', LegalPage::class);
            foreach ($this->locales as $localeCode => $localeName) {
                $this->title[$localeCode] = '';
                $this->slug[$localeCode] = '';
                $this->content[$localeCode] = '';
            }
        }
    }

    public function updatedTitle($value, $key): void
    {
        if (!$this->legalPage->exists) {
            $locale = $key;
            $this->slug[$locale] = Str::slug($value);
        }
    }

    public function save(LegalPageService $legalPageService): void
    {
        $this->authorize($this->legalPage->exists ? 'update' : 'create', $this->legalPage->exists ? $this->legalPage : LegalPage::class);
        
        $fallbackLocale = config('app.fallback_locale');
        $validationRules = [
            "title.{$fallbackLocale}" => 'required|string|max:255',
            "slug.{$fallbackLocale}" => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'slug.*' => 'nullable|string|max:255',
            'content.*' => 'nullable|string',
            'is_published' => 'boolean',
        ];

        $validationMessages = [
            "title.{$fallbackLocale}.required" => __('The title is required in the default language.'),
            "slug.{$fallbackLocale}.required" => __('The slug is required in the default language.'),
            'title.*.max' => __('The title may not be greater than :max characters.'),
            'slug.*.max' => __('The slug may not be greater than :max characters.'),
        ];

        $validatedData = $this->validate($validationRules, $validationMessages);

        try {
            $legalPageService->save($validatedData, $this->legalPage->id);

            Flux::toast(
                text: __('Page saved successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            
            $this->dispatch('legal-page-saved');
            $this->redirect(LegalPageManagement::class, navigate: true);

        } catch (\Exception $e) {
            Log::error('Failed to save legal page: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to save page. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.legal.edit-legal-page');
    }
}
