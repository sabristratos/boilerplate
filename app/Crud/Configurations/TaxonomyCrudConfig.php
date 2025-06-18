<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Facades\Settings;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaxonomyCrudConfig implements CrudConfigInterface
{
    public function getModelClass(): string
    {
        return Taxonomy::class;
    }

    public function getEntityName(): string
    {
        return 'Taxonomy';
    }

    public function getEntityPluralName(): string
    {
        return 'Taxonomies';
    }

    public function getTableColumns(): array
    {
        return [
            [
                'label' => 'Name',
                'key' => 'name',
                'sortable' => true,
            ],
            [
                'label' => 'Slug',
                'key' => 'slug',
                'sortable' => true,
            ],
            [
                'label' => 'Hierarchical',
                'key' => 'is_hierarchical',
                'type' => 'badge',
                'colors' => [
                    1 => 'blue',
                    0 => 'zinc',
                ]
            ],
        ];
    }

    public function getFormFields(): array
    {
        return [
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
                'translatable' => true,
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
                'hint' => 'Leave empty to generate from name.',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => 'textarea',
                'translatable' => true,
            ],
            [
                'name' => 'is_hierarchical',
                'label' => 'Is Hierarchical',
                'type' => 'checkbox',
                'default' => false,
            ],
        ];
    }

    public function getEagerLoadRelations(): array
    {
        return [];
    }

    public function getSearchableFields(): array
    {
        return ['name', 'slug'];
    }

    public function getPermissionPrefix(): string
    {
        // For the pilot, we can reuse existing permissions or create new ones.
        // Reusing 'taxonomies' permissions.
        return 'taxonomies';
    }

    public function beforeSave(Model $model, array $data): Model
    {
        $model->slug = Str::slug($model->name);

        return $model;
    }

    protected function getLocales(): array
    {
        $availableLocales = json_decode(Settings::get('available_languages', '[]'), true) ?: [];
        $allLocales = config('app.available_locales', []);

        return array_intersect_key($allLocales, array_flip($availableLocales));
    }

    public function getAttachableFields(): array
    {
        return [];
    }

    public function getDefaultSortField(): string
    {
        return 'name';
    }

    public function getDefaultSortDirection(): string
    {
        return 'asc';
    }

    public function getAvailableLocales(): array
    {
        // Get available locales from the translation config
        $availableLocales = config('translation.available_locales', []);

        // Get all locales from the main app config
        $allLocales = config('app.available_locales', []);

        // Return only the locales that are both available and configured for translation
        return array_intersect_key($allLocales, array_flip($availableLocales));
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getValidationRules(Model $model): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
} 