<?php

namespace App\Livewire\Admin\Crud;

use App\Crud\CrudConfigInterface;
use App\Facades\ActivityLogger;
use App\Facades\Settings;
use App\Services\AttachmentService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public string $alias;
    public string $configClass;
    private CrudConfigInterface $config;

    public ?Model $model = null;
    public array $data = [];

    public array $availableLocales = [];
    public ?string $currentLocale = null;

    public function mount(string $alias, string $crud_config_class, $id = null): void
    {
        $this->alias = $alias;
        $this->configClass = $crud_config_class;
        $this->initConfig();

        $this->availableLocales = $this->config->getAvailableLocales();
        $this->currentLocale = app()->getLocale();

        if ($id) {
            $this->model = $this->config->getModelClass()::findOrFail($id);
            $this->authorize($this->config->getPermissionPrefix() . '.update', $this->model);
        } else {
            $this->authorize($this->config->getPermissionPrefix() . '.create');
            $this->model = new ($this->config->getModelClass())();
        }

        $this->data = $this->model?->toArray() ?? [];
    }

    private function initConfig(): void
    {
        if (empty($this->config)) {
            $this->config = new $this->configClass();
        }
    }

    public function updated($propertyName): void
    {
        $this->initConfig();
        $this->validateOnly($propertyName);
    }

    protected function initializeData(): void
    {
        $this->data = [];
        foreach ($this->config->getFormFields() as $field) {
            if ($field['translatable'] ?? false) {
                foreach ($this->getLocales() as $locale => $name) {
                    $this->data[$field['name']][$locale] = $this->model->getTranslation($field['name'], $locale, false) ?? '';
                }
            } else {
                $this->data[$field['name']] = $this->model->getAttribute($field['name']) ?? $field['default'] ?? null;
            }
        }
    }

    public function getLocales(): array
    {
        $availableLocales = json_decode(Settings::get('available_languages', '[]'), true) ?: [];
        $allLocales = config('app.available_locales', []);

        return array_intersect_key($allLocales, array_flip($availableLocales));
    }

    public function save(AttachmentService $attachmentService): void
    {
        $this->initConfig();
        $this->validate();

        $this->authorize($this->model->exists ? 'update' : 'create', $this->model);

        $this->model = $this->config->beforeSave($this->model, $this->data);

        $attachableFields = $this->config->getAttachableFields();
        $attachmentData = array_intersect_key($this->data, array_flip(array_keys($attachableFields)));
        $modelData = array_diff_key($this->data, $attachmentData);


        // Separate translatable and non-translatable data
        $translatableData = [];
        $nonTranslatableData = [];
        foreach ($this->config->getFormFields() as $field) {
            // Skip attachment fields
            if (in_array($field['name'], array_keys($attachableFields))) {
                continue;
            }

            // Skip fields that should not be persisted
            if (isset($field['persist']) && $field['persist'] === false) {
                continue;
            }

            if ($field['translatable'] ?? false) {
                if (isset($modelData[$field['name']])) {
                    $translatableData[$field['name']] = $modelData[$field['name']];
                }
            } else {
                if (isset($modelData[$field['name']])) {
                    $nonTranslatableData[$field['name']] = $modelData[$field['name']];
                }
            }
        }

        $this->model->fill($nonTranslatableData);

        foreach ($translatableData as $field => $translations) {
            $this->model->setTranslations($field, $translations);
        }

        $wasRecentlyCreated = !$this->model->exists;
        $this->model->save();

        // Handle attachments after model is saved
        foreach ($attachmentData as $collection => $file) {
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                // Remove existing attachment if there is one
                $this->model->attachments()->where('collection', $collection)->get()->each->delete();
                // Upload new one
                $attachmentService->upload($file, $this->model, $collection);
            }
        }

        if ($wasRecentlyCreated) {
            ActivityLogger::logCreated($this->model);
        } else {
            ActivityLogger::logUpdated($this->model);
        }

        Flux::toast(
            heading: __(':entity_name :action', ['entity_name' => $this->config->getEntityName(), 'action' => $wasRecentlyCreated ? 'created' : 'updated']),
            text: __('The :entity_name has been saved successfully.', ['entity_name' => strtolower($this->config->getEntityName())]),
            variant: 'success'
        );

        $this->redirect(route('admin.crud.index', ['alias' => $this->alias]));
    }

    public function render(): View
    {
        $this->initConfig();
        return view('livewire.admin.crud.form', [
            'config' => $this->config,
        ])->layout('components.layouts.admin');
    }

    public function rules(): array
    {
        $this->initConfig();
        return collect($this->config->getValidationRules($this->model))
            ->mapWithKeys(fn ($value, $key) => ['data.' . $key => $value])
            ->all();
    }
} 