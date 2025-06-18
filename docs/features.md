# Key Features

This document provides details on some of the core features of the boilerplate.

## Settings Management

The boilerplate includes a centralized service for managing application-wide settings. Settings are stored in the database but are managed through a configuration file to ensure they are version-controlled and can be deployed consistently across environments.

### Core Concepts
- **Configuration:** All available settings are defined in the `config/settings.php` file. This is the single source of truth for what settings exist, their groups, types, and default values.
- **Database:** The settings and their values are stored in the `settings` and `setting_groups` tables.
- **Synchronization:** To add or update settings, you must first modify the `config/settings.php` file and then run the `php artisan settings:sync` command. This command will update the database to reflect the changes in the configuration file. It will create new settings/groups, update existing ones, and prune any that are no longer in the config.
- **Service:** Logic is handled by `App\Services\SettingsService`, accessed via the `App\Facades\Settings` facade.
- **UI:** A UI for editing the *values* of the settings is available in the admin panel under "Settings".
- **Caching:** All settings queries are cached. The `settings:sync` command automatically clears the relevant caches.
- **Public API:** Settings can be exposed via a public, unauthenticated API by setting `'is_public' => true` in their config array.
    - `GET /api/settings`: Returns all public settings.
    - `GET /api/settings/{key}`: Returns a specific public setting.

### Usage
```php
// Get a setting value
$value = Settings::get('site_name', 'Default Name');

// Set a setting value (in the admin panel or programmatically)
Settings::set('site_name', 'My Awesome App');
```

---

## Taxonomies

The boilerplate provides a generic system for creating and managing taxonomies (like categories, tags, etc.) and associating them with other models.

### Core Concepts
- **Models:** `App\Models\Taxonomy` (e.g., "Categories", "Tags") and `App\Models\Term` (e.g., "Technology", "Health").
- **Hierarchical:** Taxonomies can be defined as hierarchical, allowing terms to be nested (e.g., parent/child categories).
- **Trait:** To make a model "taxonomizable," use the `App\Models\Traits\HasTaxonomies` trait.

### Usage

1.  **Add Trait to Model:**
    ```php
    use App\Models\Traits\HasTaxonomies;

    class Post extends Model
    {
        use HasTaxonomies;
        // ...
    }
    ```

2.  **Associate Terms:**
    ```php
    $post = Post::find(1);
    $taxonomy = Taxonomy::where('slug', 'categories')->first();

    // Attach a term
    $post->addTerm('Technology', $taxonomy->id);

    // Get all terms for a post in a specific taxonomy
    $categories = $post->terms()->where('taxonomy_id', $taxonomy->id)->get();
    ```

---

## File Attachments

The boilerplate includes a simple system for attaching one or more files to any Eloquent model.

### Core Concepts
- **Model:** `App\Models\Attachment`.
- **Trait:** To make a model "attachable," use the `App\Models\Traits\HasAttachments` trait. This trait adds the `attachments` relationship to your model.
- **Storage:** Files are stored in the `storage/app/attachments` directory by default.

### Usage

1.  **Add Trait to Model:**
    ```php
    use App\Models\Traits\HasAttachments;

    class User extends Authenticatable
    {
        use HasAttachments;
        // ...
    }
    ```
2.  **Attaching a File:**
    The boilerplate includes a Livewire component `App\Livewire\Attachments\UploadAttachment` that can be used in forms to handle file uploads. In the backend, you can associate uploaded files with a model like this:
    ```php
    use App\Services\AttachmentService;

    $user = User::find(1);
    $file = $request->file('avatar'); // UploadedFile instance

    (new AttachmentService())->storeAndAttach($file, $user, 'avatars');
    ```
3.  **Retrieving Attachments:**
    ```php
    $user = User::find(1);

    // Get all attachments
    $attachments = $user->attachments;

    // Get the first attachment in a specific collection
    $avatarUrl = $user->attachment('avatars')?->getUrl();
    ```

---

## Activity Logging

The application includes a system for recording user activities. It's recommended to log important events in any new systems you add.

### Core Concepts
- **Service:** All logging is handled by the `App\Services\ActivityLoggerService`, which is available via the `App\Facades\ActivityLogger` facade.
- **Automatic Data:** The service automatically records the user who performed the action (causer), their IP address, and user agent.
- **Standard Events:** The service has helper methods for common CRUD operations: `logCreated`, `logUpdated`, and `logDeleted`.

### Usage

The `ActivityLogger` facade provides a simple and expressive API for logging.

**Logging a Model's Creation:**
```php
use App\Facades\ActivityLogger;

$post = Post::create($data);
ActivityLogger::logCreated($post);
```

**Logging an Update:**
```php
use App\Facades\ActivityLogger;

// You can pass extra properties, like the changed data
$oldValues = $post->getOriginal();
$post->update($newData);
$newValues = $post->getAttributes();

ActivityLogger::logUpdated($post, auth()->user(), [
    'old' => $oldValues,
    'new' => $newValues
]);
```

**Logging a Deletion:**
```php
use App\Facades\ActivityLogger;

ActivityLogger::logDeleted($post);
```

**Logging a Custom Event:**
For actions that don't fit the CRUD model, use the `logCustom` method.
```php
use App\Facades\ActivityLogger;

ActivityLogger::logCustom(
    'user.exported',
    'Exported a list of users',
    $exportFileModel, // optional subject
    auth()->user()   // optional causer
);
``` 