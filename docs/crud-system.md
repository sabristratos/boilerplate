# Modular CRUD System

The boilerplate includes a powerful, configuration-based system that allows for the rapid creation of administrative CRUD (Create, Read, Update, Delete) interfaces for any Eloquent model. The system is designed to be highly modular and extensible.

The core idea is to define the behavior and appearance of a CRUD interface in a single configuration class, and the system handles the rest—routing, data binding, table display, form generation, and validation.

## How It Works

The system is powered by two generic Livewire components:
- `App\Livewire\Admin\Crud\Index.php`: Handles the listing, searching, filtering, and deletion of records.
- `App\Livewire\Admin\Crud\Form.php`: Handles the creation and editing of records.

These components are dynamically configured by a "CRUD Config" class specific to the model you want to manage. The `CrudServiceProvider` automatically discovers these configuration classes and sets up the necessary routing.

## Creating a New CRUD Interface

To create a new CRUD interface for a model (e.g., `Product`), follow these two steps:

### 1. Create the Model and Migration

First, create your Eloquent model and its corresponding migration file as you normally would in Laravel.

```bash
php artisan make:model Product -m
```

### 2. Create a CRUD Config Class

Next, create a new configuration class in the `app/Crud/Configurations/` directory. For a `Product` model, you would create `app/Crud/Configurations/ProductCrudConfig.php`.

This class must implement the `App\Crud\CrudConfigInterface`.

```php
<?php

namespace App\Crud\Configurations;

use App\Crud\CrudConfigInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductCrudConfig implements CrudConfigInterface
{
    public function getModelClass(): string
    {
        return Product::class;
    }

    public function getEntityName(): string
    {
        return 'Product';
    }

    public function getEntityPluralName(): string
    {
        return 'Products';
    }
    
    public function getPermissionPrefix(): string
    {
        return 'products';
    }

    public function getTableColumns(): array
    {
        return [
            ['label' => 'Name', 'key' => 'name', 'sortable' => true],
            ['label' => 'Price', 'key' => 'price'],
            ['label' => 'Status', 'key' => 'status', 'type' => 'badge'],
        ];
    }

    public function getFormFields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
            ['name' => 'price', 'label' => 'Price', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
                'available' => 'Available',
                'out-of-stock' => 'Out of Stock',
            ]],
            ['name' => 'product_image', 'label' => 'Product Image', 'type' => 'file'],
        ];
    }
    
    // ... implement other required methods from the interface
}
```

### 3. Automatic Registration and Routing

That's it! The `CrudServiceProvider` will automatically detect your new `ProductCrudConfig.php` class. It will generate an alias `products` from the class name.

You can now access your new CRUD interface at `/admin/crud/products`.

- **List View:** `/admin/crud/products`
- **Create View:** `/admin/crud/products/create`
- **Edit View:** `/admin/crud/products/{id}/edit`

The system automatically handles authorization based on the `getPermissionPrefix()` method. It will check for permissions like `view-any-products`, `create-products`, and `edit-products`. 