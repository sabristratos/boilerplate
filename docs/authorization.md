# Authorization System Standardization

## Overview

This project uses Laravel Policies as the single source of truth for all authorization logic. All permission checks are centralized in Policy classes, and all route and component authorization is enforced using Laravel's `can:` middleware and `$this->authorize()`/`can()` methods. This ensures a maintainable, consistent, and secure approach to permissions across the application.

## Key Principles

- **Centralization:** All authorization logic for models (User, Role, Taxonomy, Term, Setting, Attachment, ActivityLog, Notification, LegalPage) is handled in their respective Policy classes in `app/Policies`.
- **No Gate Definitions:** All legacy Gate definitions and the `Gate::before` hook have been removed. No custom Gate logic is used.
- **No Permission Middleware:** The custom `permission` and `role` middleware have been removed. Use only `can:` middleware for route protection.
- **Super Admin Logic:** If super admin overrides are needed, implement them directly in the relevant Policy methods.
- **No Direct Permission Checks in Views:** Use Blade's `@can` and `@cannot` directives, which now rely on policies.

## Best Practices for Policies

- **One Policy per Model:** Each model that requires authorization should have a corresponding Policy class.
- **Method Naming:** Use standard method names (`viewAny`, `view`, `create`, `update`, `delete`, etc.) for consistency and compatibility with Laravel's helpers.
- **Dependency Injection:** Inject the User and relevant model into each policy method.
- **Super Admins:** If super admins should bypass checks, add this logic at the top of each policy method.
- **Testing:** Write feature tests to ensure policies enforce the correct permissions.

## Usage Examples

**In Controllers/Livewire Components:**
```php
$this->authorize('update', $user);
```

**In Routes:**
```php
Route::middleware('can:update,user')->group(function () {
    // ...
});
```

**In Blade Views:**
```blade
@can('delete', $role)
    <button>Delete</button>
@endcan
```

## Adding New Authorization Logic

1. **Create a Policy:**
   - Run `php artisan make:policy ModelPolicy --model=Model`.
   - Implement the required methods.
2. **Register the Policy:**
   - Add the model and policy to the `$policies` array in `AuthServiceProvider`.
3. **Use `can:` Middleware:**
   - Protect routes using `can:action,Model`.
4. **Use `authorize()` and `@can`:**
   - In controllers/components, use `$this->authorize()`.
   - In Blade, use `@can`/`@cannot`.

## Maintenance

- **Do not use direct permission checks** (e.g., `$user->hasPermission('edit-users')`) outside of policies.
- **Do not use Gate definitions** or custom middleware for permissions.
- **Review policies** when adding new features or permissions.

---

For more details, see the Laravel [authorization documentation](https://laravel.com/docs/authorization). 