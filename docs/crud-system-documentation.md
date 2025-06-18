# Comprehensive CRUD System Documentation

This document provides a complete guide to the dynamic CRUD (Create, Read, Update, Delete) system. It is designed to be powerful and flexible, allowing developers to quickly scaffold interfaces for managing any Eloquent model.

## Core Concept

The system is centered around a `CrudConfigInterface`. For each model you want to manage, you create a corresponding configuration class that implements this interface. This class defines everything about how the CRUD interface will look and behave—from the table columns and form fields to validation rules and user permissions.

---

## Getting Started: Creating a CRUD Configuration

To get started, create a new class in `app/Crud/Configurations`. This class must implement `App\Crud\CrudConfigInterface`.

**Example: `app/Crud/Configurations/UserCrudConfig.php`**
```php
<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Models\User;
// ... other necessary imports

class UserCrudConfig implements CrudConfigInterface
{
    // All configuration methods will go here...
}
```

Then, you must register this configuration in the `app/Providers/CrudServiceProvider.php` to make it accessible via a unique alias.

**Example: `app/Providers/CrudServiceProvider.php`**
```php
// ...
use App\Crud\Configurations\UserCrudConfig;

class CrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CrudManager::class, function ($app) {
            $manager = new CrudManager();
            
            // Register your new CRUD config
            $manager->register('users', UserCrudConfig::class);

            return $manager;
        });
    }
}
```

---

## `CrudConfigInterface` Method Reference

### Basic Configuration

#### `getModelClass(): string`
Return the fully qualified class name of the Eloquent model.
```php
public function getModelClass(): string
{
    return \App\Models\User::class;
}
```

#### `getEntityName(): string`
Return the singular name of the entity for labels (e.g., "Edit User").
```php
public function getEntityName(): string
{
    return 'User';
}
```

#### `getEntityPluralName(): string`
Return the plural name for labels and headings (e.g., "Back to Users").
```php
public function getEntityPluralName(): string
{
    return 'Users';
}
```

#### `getAlias(): string`
Return the unique alias used in routes and for registration. This should match the key used in the `CrudServiceProvider`.
```php
public function getAlias(): string
{
    return 'users';
}
```

### Table Column Configuration

#### `getTableColumns(): array`
Define the columns displayed on the index (listing) page. This is an array of column arrays.

| Key | Description | Example |
|---|---|---|
| `label` | The column header text. | `'Status'` |
| `key` | The model attribute to display. | `'status'` |
| `sortable`| (Optional) Whether the column is sortable. | `true` |
| `type` | (Optional) Renders the cell as a specific component. Currently supports `'badge'`. | `'badge'` |
| `colors` | (Optional) When `type` is `'badge'`, a key-value array mapping attribute values to badge colors. | `['active' => 'green', 'inactive' => 'zinc']` |
| `render` | (Optional) A Blade string for custom cell rendering. Use `$item` to access the model instance. | `'<x-user-profile-cell :user="$item" />'` |

**Example:**
```php
public function getTableColumns(): array
{
    return [
        [
            'label' => 'User',
            'key' => 'name',
            'render' => '<x-user-profile-cell :user="$item" />',
        ],
        [
            'label' => 'Status',
            'key' => 'status',
            'type' => 'badge',
            'colors' => [
                UserStatus::Active->value => 'green',
                UserStatus::Inactive->value => 'zinc',
            ],
        ],
        [
            'label' => 'Created At',
            'key' => 'created_at',
            'sortable' => true,
        ],
    ];
}
```

### Form Field Configuration

#### `getFormFields(): array`
Define the fields displayed on the create and edit forms. This is an array of field arrays. See the **Form Field Property Reference** below for a complete list of options.

**Example:**
```php
public function getFormFields(): array
{
    return [
        [
            'name' => 'name',
            'label' => __('Name'),
            'type' => 'text',
            'required' => true,
            'column_span' => 1,
        ],
        [
            'name' => 'email',
            'label' => __('Email'),
            'type' => 'email',
            'required' => true,
            'column_span' => 1,
        ],
        // ... more fields
    ];
}
```

#### **Supported Field Types**

| Type | Description |
|---|---|
| `text` | Standard text input. |
| `email` | Email input with validation. |
| `password` | Password input. |
| `number` | Number input. |
| `textarea` | A multi-line text area. |
| `checkbox` | A toggle switch. |
| `select` | A standard dropdown select. |
| `multiselect` | A searchable multi-select listbox. |
| `file_upload`| The standard file dropzone component. |
| `circular` | A circular variant of the file upload, ideal for avatars. |
| `editor` | A rich text (WYSIWYG) editor. |

#### **Form Field Property Reference**

| Key | Description | Applicable To |
|---|---|---|
| `name` | The field's name, corresponding to a model attribute or a temporary field. | All |
| `label` | The display label for the field. | All |
| `type` | The field type from the list above. | All |
| `column_span` | The width of the field in a 2-column grid. `1` for half-width, `2` for full-width. | All |
| `placeholder` | Placeholder text for the input. | text, email, password, textarea, select, multiselect |
| `required` | Whether the field is required (for frontend display; validation is separate). | text, email, etc. |
| `options` | A key-value array of options for selects. | select, multiselect |
| `relationship`| The name of the Eloquent relationship for multi-selects. | multiselect |
| `persist` | If `false`, the field's value is not loaded from or saved to the model. Ideal for `password_confirmation`. | All |
| `collection` | The collection name for storing attachments. | file_upload, circular |
| `multiple` | If `true`, allows multiple file uploads. | file_upload |


### Data Handling and Logic

#### `getValidationRules(Model $model): array`
Return an array of Laravel validation rules. The `$model` instance is passed to allow for unique rules (e.g., `unique:users,email,` . `$model->id`).
```php
public function getValidationRules(Model $model): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $model->id,
        'password' => 'nullable|string|min:8|confirmed',
    ];
}
```

#### `beforeSave(Model $model, array $data): Model`
A hook to modify the model or data *before* it is saved to the database. It must return the modified `$model` instance. This is the perfect place to hash passwords.
```php
public function beforeSave(Model $model, array $data): Model
{
    if (isset($data['password'])) {
        $model->password = \Illuminate\Support\Facades\Hash::make($data['password']);
    }
    return $model;
}
```

### Searching, Sorting, and Relations

#### `getSearchableFields(): array`
An array of model attributes that should be included in the global search.
```php
public function getSearchableFields(): array
{
    return ['name', 'email'];
}
```

#### `getEagerLoadRelations(): array`
An array of relationships to eager-load with the main model query to prevent N+1 problems.
```php
public function getEagerLoadRelations(): array
{
    return ['roles'];
}
```

#### `getDefaultSortField(): string`
The attribute to use for the default sort order.
```php
public function getDefaultSortField(): string
{
    return 'created_at';
}
```
#### `getDefaultSortDirection(): string`
The default sort direction (`'asc'` or `'desc'`).
```php
public function getDefaultSortDirection(): string
{
    return 'desc';
}
```

### Advanced Configuration

#### `getFilters(): array`
Define filters for the index page.
```php
public function getFilters(): array
{
    return [
        'status' => [
            'label' => 'Status',
            'type' => 'select',
            'options' => UserStatus::asSelectArray(),
        ],
    ];
}
```

#### `getActions(): array`
Define custom row actions for the index page.
```php
public function getActions(): array
{
    return [
        [
            'type' => 'row_action',
            'label' => 'Impersonate',
            'icon' => 'user-circle',
            'method' => 'impersonateUser', // A public method on the Index Livewire component
            'permission' => 'users.impersonate',
        ],
    ];
}
```

#### `getPermissionPrefix(): string`
The prefix for permissions checked via policies (e.g., `users.view`, `users.create`).
```php
public function getPermissionPrefix(): string
{
    return 'users';
}
``` 