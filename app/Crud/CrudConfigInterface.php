<?php

namespace App\Crud;

use Illuminate\Database\Eloquent\Model;

interface CrudConfigInterface
{
    /**
     * Get the FQCN of the model.
     */
    public function getModelClass(): string;

    /**
     * Get the fields to display in the form.
     */
    public function getFormFields(): array;

    /**
     * Get the columns to display in the table.
     */
    public function getTableColumns(): array;

    /**
     * Get the fields to search in the table.
     */
    public function getSearchableFields(): array;

    /**
     * Get the relations to eager load.
     */
    public function getEagerLoadRelations(): array;

    /**
     * Get the singular name of the entity.
     */
    public function getEntityName(): string;

    /**
     * Get the plural name of the entity.
     */
    public function getEntityPluralName(): string;

    /**
     * Get the permission prefix for the entity.
     */
    public function getPermissionPrefix(): string;

    /**
     * Get the URL alias for the entity.
     */
    public function getAlias(): string;

    /**
     * A hook to modify data before it is saved.
     */
    public function beforeSave(Model $model, array $data): Model;

    /**
     * Get the attachable fields.
     */
    public function getAttachableFields(): array;

    /**
     * Get the default sort field.
     */
    public function getDefaultSortField(): string;

    /**
     * Get the default sort direction.
     */
    public function getDefaultSortDirection(): string;

    /**
     * Get the available locales for translatable fields.
     */
    public function getAvailableLocales(): array;

    /**
     * Get the filters to display in the table.
     */
    public function getFilters(): array;

    /**
     * Get the validation rules for the form.
     */
    public function getValidationRules(Model $model, string $currentLocale): array;

    /**
     * Get the custom actions for the entity.
     */
    public function getActions(): array;
} 