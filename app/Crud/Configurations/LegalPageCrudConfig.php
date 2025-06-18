<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Models\LegalPage;
use Illuminate\Database\Eloquent\Model;

class LegalPageCrudConfig implements CrudConfigInterface
{
    public function getModelClass(): string
    {
        return LegalPage::class;
    }

    public function getEagerLoadRelations(): array
    {
        return [];
    }

    public function getEntityName(): string
    {
        return 'Legal Page';
    }

    public function getEntityPluralName(): string
    {
        return 'Legal Pages';
    }

    public function getPermissionPrefix(): string
    {
        return 'legal_pages';
    }

    public function getAlias(): string
    {
        return 'legal-pages';
    }

    public function getSearchableFields(): array
    {
        return ['title'];
    }

    public function getDefaultSortField(): string
    {
        return 'title';
    }

    public function getDefaultSortDirection(): string
    {
        return 'asc';
    }

    public function getTableColumns(): array
    {
        return [
            [
                'label' => 'Title',
                'key' => 'title',
                'sortable' => true,
            ],
            [
                'label' => 'Published',
                'key' => 'is_published',
                'type' => 'badge',
                'colors' => [
                    true => 'green',
                    false => 'zinc',
                ],
            ],
            [
                'label' => 'Languages',
                'key' => 'available_locales_as_string',
            ],
            [
                'label' => 'Last Updated',
                'key' => 'updated_at',
                'sortable' => true,
            ],
        ];
    }

    public function getFormFields(): array
    {
        return [
            [
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'translatable' => true,
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
                'translatable' => true,
            ],
            [
                'name' => 'content',
                'label' => 'Content',
                'type' => 'editor',
                'translatable' => true,
            ],
            [
                'name' => 'is_published',
                'label' => 'Published',
                'type' => 'checkbox',
            ],
        ];
    }

    public function getFilters(): array
    {
        return [
            'is_published' => [
                'label' => 'Status',
                'type' => 'boolean',
            ],
        ];
    }

    public function getActions(): array
    {
        return [
            [
                'type' => 'row_action',
                'label' => 'Copy Link',
                'icon' => 'link',
                'method' => 'copyLink',
            ],
        ];
    }

    public function getAttachableFields(): array
    {
        return [];
    }

    public function getAvailableLocales(): array
    {
        return config('app.available_locales', []);
    }

    public function getValidationRules(Model $model, string $currentLocale): array
    {
        return [
            'title' => 'required|array',
            'title.' . $currentLocale => 'required|string|max:255',
            'slug' => 'required|array',
            'slug.*' => 'required|string|max:255',
            'content' => 'required|array',
            'content.' . $currentLocale => 'required|string',
            'is_published' => 'boolean',
        ];
    }

    public function beforeSave(Model $model, array $data): Model
    {
        return $model;
    }
} 