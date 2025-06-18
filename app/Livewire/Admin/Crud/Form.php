<?php

namespace App\Livewire\Admin\Crud;

use App\Crud\CrudConfigInterface;
use App\Facades\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Attachment;
use Flux\Flux;

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

        $this->initializeData();
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
            $fieldName = $field['name'];
            if (in_array($field['type'], ['file_upload', 'circular'])) {
                // We don't preload file inputs; they are write-only.
                $this->data[$fieldName] = null;
                continue;
            }

            if ($field['translatable'] ?? false) {
                foreach ($this->getLocales() as $locale => $localeName) {
                    $this->data[$fieldName][$locale] = $this->model->getTranslation($fieldName, $locale, false) ?? '';
                }
            } elseif (!isset($field['persist']) || $field['persist'] !== false) {
                $this->data[$fieldName] = $this->model->getAttribute($fieldName) ?? $field['default'] ?? null;
            } else {
                $this->data[$fieldName] = null;
            }
        }
    }

    public function getLocales(): array
    {
        $availableLocales = json_decode((string) Settings::get('available_languages', '[]'), true) ?: [];
        $allLocales = config('app.available_locales', []);

        return array_intersect_key($allLocales, array_flip($availableLocales));
    }

    public function save(): void
    {
        $this->initConfig();
        $this->validate();
        $this->authorize($this->model->exists ? 'update' : 'create', $this->model);

        $allFields = collect($this->config->getFormFields());
        $fileUploadFields = $allFields->whereIn('type', ['file_upload', 'circular']);
        $definedFieldNames = $allFields->pluck('name')->all();

        $formData = collect($this->data)
            ->except($fileUploadFields->pluck('name')->all())
            ->only($definedFieldNames)
            ->all();

        $this->model = $this->config->beforeSave($this->model, $formData);

        DB::transaction(function () use ($formData, $fileUploadFields) {
            $relationshipData = [];
            $persistentData = $formData;

            foreach ($this->config->getFormFields() as $field) {
                if (isset($field['relationship'])) {
                    if (array_key_exists($field['name'], $persistentData)) {
                        $relationshipData[$field['relationship']] = $persistentData[$field['name']];
                        unset($persistentData[$field['name']]);
                    }
                }

                if (isset($field['persist']) && $field['persist'] === false) {
                    if (array_key_exists($field['name'], $persistentData)) {
                        unset($persistentData[$field['name']]);
                    }
                }
            }

            if ($this->model->exists) {
                $this->model->update($persistentData);
            } else {
                $this->model->fill($persistentData);
                $this->model->save();
            }

            foreach ($relationshipData as $relationship => $values) {
                $this->model->{$relationship}()->sync($values ?? []);
            }

            // Handle file uploads
            foreach ($fileUploadFields as $field) {
                $modelName = $field['name'];
                if (isset($this->data[$modelName]) && !empty($this->data[$modelName])) {
                    $files = Arr::wrap($this->data[$modelName]);

                    if (!($field['multiple'] ?? false)) {
                        $this->model->removeAllAttachments($field['collection']);
                    }

                    foreach ($files as $file) {
                        $this->model->addAttachment($file, $field['collection']);
                    }
                }
            }
        });

        Flux::toast(__('Saved successfully!'), variant: 'success');
    }

    public function removeAttachment(int $attachmentId, string $collectionName): void
    {
        $this->initConfig();
        
        if (!$this->model) {
            return;
        }

        $this->authorize($this->config->getPermissionPrefix() . '.update', $this->model);

        /** @var Attachment|null $attachment */
        $attachment = Attachment::find($attachmentId);
        if ($attachment) {
            $this->model->removeAttachment($attachment);
            Flux::toast(__('Attachment removed.'), variant: 'success');
        } else {
            Flux::toast(__('Attachment not found.'), variant: 'danger');
        }
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