# Eloquent Models

This document provides details about the Eloquent ORM models used in the application.

## `App\Models\User`

The `User` model represents users in the application. It extends `Illuminate\Foundation\Auth\User` and implements `Illuminate\Contracts\Auth\MustVerifyEmail` and `App\Interfaces\HasRoles`.

### Traits Used

*   `Illuminate\Database\Eloquent\Factories\HasFactory`: For model factories.
*   `Illuminate\Notifications\Notifiable`: For handling notifications.
*   `App\Models\Traits\HasAttachments`: Provides methods for managing file attachments (see [HasAttachments Trait](#hasattachments-trait)).
*   `App\Models\Traits\HasTaxonomies`: Provides methods for managing taxonomies (e.g., categories, tags) (see [HasTaxonomies Trait](#hastaxonomies-trait)).

### Properties

*   `id` (int): Primary key.
*   `name` (string): User's full name.
*   `email` (string): User's email address (unique).
*   `email_verified_at` (Carbon|null): Timestamp of email verification.
*   `password` (string): Hashed password.
*   `remember_token` (string|null): Token for "remember me" functionality.
*   `created_at` (Carbon|null): Timestamp of creation.
*   `updated_at` (Carbon|null): Timestamp of last update.
*   `two_factor_secret` (string|null): Encrypted secret key for two-factor authentication.
*   `two_factor_recovery_codes` (array|null): Encrypted recovery codes for two-factor authentication.
*   `two_factor_confirmed_at` (Carbon|null): Timestamp when two-factor authentication was confirmed.

### Fillable Attributes

*   `name`
*   `email`
*   `password`

### Hidden Attributes (for serialization)

*   `password`
*   `remember_token`
*   `two_factor_secret`
*   `two_factor_recovery_codes`

### Casts

*   `email_verified_at`: `'datetime'`
*   `password`: `'hashed'`
*   `two_factor_confirmed_at`: `'datetime'`
*   `two_factor_recovery_codes`: `'array'`

### Relationships

*   `roles()`: `BelongsToMany` relationship with `App\Models\Role`.
    *   Returns a collection of roles associated with the user.
*   `notifications()`: Provided by `Notifiable` trait.
    *   Access user's database notifications.
*   `unreadNotifications()`: Provided by `Notifiable` trait.
    *   Access user's unread database notifications.
*   `attachments()`: `MorphMany` relationship with `App\Models\Attachment` (from `HasAttachments` trait).
    *   Returns a collection of attachments associated with the user.
*   `terms()`: `MorphToMany` relationship with `App\Models\Term` (from `HasTaxonomies` trait).
    *   Returns a collection of terms (e.g., tags, categories) associated with the user.

### Key Methods

#### Two-Factor Authentication

*   `hasTwoFactorEnabled(): bool`: Checks if 2FA is enabled and confirmed.
*   `hasConfirmedTwoFactor(): bool`: Checks if 2FA has been confirmed.
*   `twoFactorQrCodeSvg(string $companyName = 'TALL Boilerplate'): ?string`: Generates the SVG for the 2FA QR code.
*   `enableTwoFactorAuthentication(): void`: Enables 2FA by generating and storing a secret key and recovery codes.
*   `confirmTwoFactorAuthentication(): void`: Confirms 2FA setup.
*   `disableTwoFactorAuthentication(): void`: Disables 2FA for the user.
*   `validateTwoFactorCode(string $code): bool`: Validates a given 2FA code.
*   `validateTwoFactorRecoveryCode(string $code): bool`: Validates and consumes a 2FA recovery code.

#### Roles and Permissions

*   `hasRole(string $roleSlug): bool`: Checks if the user has a specific role (by slug).
*   `hasAnyRole(array $roleSlugs): bool`: Checks if the user has any of the specified roles.
*   `hasAllRoles(array $roleSlugs): bool`: Checks if the user has all of the specified roles.
*   `hasPermission(string $permissionSlug): bool`: Checks if the user has a specific permission (by slug) through their roles.
*   `hasAnyPermission(array $permissionSlugs): bool`: Checks if the user has any of the specified permissions through their roles.
*   `hasAllPermissions(array $permissionSlugs): bool`: Checks if the user has all of the specified permissions through their roles.

#### Attachment Management (from `HasAttachments` trait)

*   `getAttachments(?string $collection = null)`: Get attachments, optionally filtered by collection.
*   `addAttachment(UploadedFile $file, ?string $collection = null, array $meta = [], array $options = [])`: Add an attachment.
*   `removeAttachment(Attachment $attachment)`: Remove a specific attachment.
*   `removeAllAttachments(?string $collection = null)`: Remove all attachments, optionally filtered by collection.

#### Taxonomy Management (from `HasTaxonomies` trait)

*   `getTerms(string $taxonomySlug)`: Get terms for a specific taxonomy slug.
*   `addTerm(Term $term)`: Add a term to the model.
*   `removeTerm(Term $term): int`: Remove a term from the model.
*   `syncTerms(array $termIds): array`: Sync terms for the model.
*   `hasTerm(Term $term): bool`: Check if the model has a specific term.
*   `hasTermsFromTaxonomy(string $taxonomySlug): bool`: Check if the model has terms from a specific taxonomy.

### Usage Examples

```php
// Finding a user
$user = User::find(1);

// Checking roles
if ($user->hasRole(config('roles.super_admin'))) {
    // User is an admin
}

// Adding an attachment
// $uploadedFile = new Illuminate\Http\UploadedFile(...);
// $user->addAttachment($uploadedFile, 'avatar');

// Adding a term (assuming $term is an instance of App\Models\Term)
// $user->addTerm($term);

// Example:
//
// ```php
// Role::create([
//     'name' => 'Administrator',
//     'slug' => config('roles.super_admin'),
//     'description' => 'Full access to all system features',
// ]);
// ```

---
## Model Traits

### `App\Models\Traits\HasAttachments` {#hasattachments-trait}

This trait provides a polymorphic relationship to the `Attachment` model and methods to manage file attachments for any model that uses it.

**Key Features:**
*   Defines a `attachments()` `MorphMany` relationship.
*   Allows adding, retrieving, and removing attachments.
*   Supports organizing attachments into collections.
*   Uses `App\Services\AttachmentService` for handling file operations.

**Methods:**
*   `attachments(): MorphMany`: Returns the polymorphic relationship instance.
*   `getAttachments(?string $collection = null)`: Fetches attachments, optionally filtered by a collection name.
*   `addAttachment(UploadedFile $file, ?string $collection = null, array $meta = [], array $options = [])`: Uploads and associates a file.
    *   `$file`: The `UploadedFile` instance.
    *   `$collection`: Optional string to group attachments (e.g., 'images', 'documents').
    *   `$meta`: Optional array of metadata to store with the attachment.
    *   `$options`: Optional array of options for the `AttachmentService`.
*   `removeAttachment(Attachment $attachment)`: Deletes a specific attachment record and its associated file.
*   `removeAllAttachments(?string $collection = null)`: Deletes all attachments for the model, or those in a specific collection.

**Example Usage (within a model that uses `HasAttachments`):**
```php
use App\Models\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Post extends Model
{
    use HasAttachments;

    // ... other model code
}

// $post = Post::find(1);
// $file = new UploadedFile(storage_path('app/public/some_file.jpg'), 'some_file.jpg');
// $post->addAttachment($file, 'featured_images');
// $images = $post->getAttachments('featured_images');
```

### `App\Models\Traits\HasTaxonomies` {#hastaxonomies-trait}

This trait provides functionality to associate models with `Term` records, which belong to different `Taxonomy` types (e.g., categories, tags).

**Key Features:**
*   Defines a `terms()` `MorphToMany` relationship to the `Term` model.
*   Allows adding, removing, syncing, and checking for terms.
*   Supports filtering terms by taxonomy.

**Methods:**
*   `terms(): MorphToMany`: Returns the polymorphic relationship instance.
*   `getTerms(string $taxonomySlug)`: Retrieves all terms associated with the model that belong to the specified taxonomy (by slug).
*   `addTerm(Term $term)`: Attaches an existing `Term` to the model.
*   `removeTerm(Term $term): int`: Detaches a `Term` from the model. Returns the number of detached records.
*   `syncTerms(array $termIds): array`: Synchronizes the model's associated terms with the provided array of term IDs. Detaches terms not in the array and attaches new ones.
*   `hasTerm(Term $term): bool`: Checks if the model is associated with a specific `Term` instance.
*   `hasTermsFromTaxonomy(string $taxonomySlug): bool`: Checks if the model has any terms belonging to the specified taxonomy.

**Example Usage (within a model that uses `HasTaxonomies`):**
```php
use App\Models\Traits\HasTaxonomies;
use App\Models\Term;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasTaxonomies;

    // ... other model code
}

// $article = Article::find(1);
// $categoryTerm = Term::where('slug', 'news')->first();
// $tagTerm = Term::where('slug', 'php')->first();

// if ($categoryTerm) {
//     $article->addTerm($categoryTerm);
// }
// if ($tagTerm) {
//     $article->addTerm($tagTerm);
// }

// $articleCategories = $article->getTerms('category'); // Assuming 'category' is a Taxonomy slug
// $article->syncTerms([$categoryTerm->id, $tagTerm->id]);
```

---
## `App\Models\ActivityLog`

The `ActivityLog` model is used to record activities performed within the application. This is crucial for auditing, tracking changes, and understanding user interactions.

### Table

*   `activity_logs`

### Fillable Attributes

*   `log_name` (string, nullable): A name for the log, can be used to group activities (e.g., 'default', 'auth').
*   `description` (string): A human-readable description of the activity.
*   `subject_type` (string, nullable): The class name of the model that was the subject of the activity.
*   `subject_id` (int, nullable): The ID of the model that was the subject of the activity.
*   `causer_type` (string, nullable): The class name of the model (usually `User`) that caused the activity.
*   `causer_id` (int, nullable): The ID of the model (usually `User`) that caused the activity.
*   `properties` (array, nullable): A JSON column to store any additional context or data related to the activity (e.g., changed attributes).
*   `event` (string, nullable): An optional event name (e.g., 'created', 'updated', 'deleted', 'login').
*   `ip_address` (string, nullable): The IP address from which the activity originated.
*   `user_agent` (string, nullable): The user agent of the client that performed the activity.

### Casts

*   `properties`: `'array'`

### Relationships

*   `subject()`: `MorphTo` relationship.
    *   Fetches the model that was the subject of the activity (e.g., the `Post` that was updated).
*   `causer()`: `MorphTo` relationship.
    *   Fetches the model (typically `User`) that performed the activity.

### Query Scopes

*   `scopeInLog(string $logName)`: Filters activities by a specific `log_name`.
*   `scopeForSubject(Model $subject)`: Filters activities for a specific subject model instance.
*   `scopeCausedBy(Model $causer)`: Filters activities caused by a specific model instance (typically a `User`).
*   `scopeWithEvent(string $event)`: Filters activities by a specific event name.

### Usage Examples

```php
// Recording an activity (typically handled by an activity logging package/service)
// ActivityLog::create([
//     'log_name' => 'content',
//     'description' => 'User created a new article',
//     'subject_type' => App\Models\Article::class,
//     'subject_id' => $article->id,
//     'causer_type' => App\Models\User::class,
//     'causer_id' => auth()->id(),
//     'properties' => ['title' => $article->title],
//     'event' => 'created',
//     'ip_address' => request()->ip(),
//     'user_agent' => request()->header('User-Agent'),
// ]);

// Retrieving activities
$userActivities = ActivityLog::causedBy(auth()->user())->latest()->take(10)->get();

$articleActivities = ActivityLog::forSubject($article)->get();

$loginActivities = ActivityLog::inLog('auth')->withEvent('login')->get();
```

---
## `App\Models\Attachment`

The `Attachment` model represents a file that has been uploaded to the system. It stores metadata about the file and provides a polymorphic relationship to associate it with other models.

### Fillable Attributes

*   `filename` (string): The original name of the uploaded file.
*   `path` (string): The path to the file on the specified `disk`.
*   `disk` (string): The filesystem disk where the file is stored (from `config/filesystems.php`).
*   `mime_type` (string): The MIME type of the file (e.g., `image/jpeg`, `application/pdf`).
*   `size` (int): The size of the file in bytes.
*   `collection` (string, nullable): An optional name to group attachments (e.g., 'avatars', 'documents').
*   `meta` (array, nullable): A JSON column to store any additional custom metadata about the attachment.

### Casts

*   `meta`: `'array'`
*   `size`: `'integer'`

### Relationships

*   `attachable()`: `MorphTo` relationship.
    *   Fetches the parent model to which this attachment belongs (e.g., a `User` for an avatar, a `Post` for a featured image).

### Accessors

*   `getUrlAttribute()`: Returns the public URL to access the file, using the configured disk and path.

### Methods

*   `isImage(): bool`: Determines if the attachment is an image by checking if the `mime_type` starts with `'image/'`.
*   `deleteFile(): void`: Deletes the physical file from the storage disk. This method is automatically called when an `Attachment` model instance is being deleted, thanks to the `deleting` event listener in the `booted` method.

### Model Events

*   `deleting`: When an `Attachment` record is deleted, the `deleteFile()` method is called to remove the associated file from the storage disk. This ensures that orphaned files are not left behind.

### Usage Examples

```php
// Example: An attachment is typically created via a service or a trait like HasAttachments
// $user = User::find(1);
// $file = $request->file('avatar'); // Instance of UploadedFile

// // Assuming HasAttachments trait is used on User model
// $attachment = $user->addAttachment($file, 'avatars', ['is_profile_picture' => true]);

// Retrieving an attachment
$attachment = Attachment::find(1);

if ($attachment) {
    echo "File URL: " . $attachment->url; // Accessor
    echo "File Size: " . $attachment->size . " bytes";

    if ($attachment->isImage()) {
        echo "<img src=\"{$attachment->url}\" alt=\"{$attachment->filename}\">";
    }

    // Get the model this attachment belongs to
    // $parentModel = $attachment->attachable;
}

// Deleting an attachment (this will also delete the file from storage)
// $attachment->delete();
```

---
## `App\Models\PageView`

The `PageView` model is responsible for logging individual page views within the application, forming the basis for analytics.

### Features

*   Tracks views by both authenticated users and guest sessions.
*   Captures common analytics data like path, referrer, UTM parameters, IP address, user agent, and device information.
*   Uses a dedicated `visited_at` timestamp.
*   Standard Eloquent timestamps (`created_at`, `updated_at`) are disabled (`public $timestamps = false;`).

### Fillable Attributes

*   `user_id` (int, nullable): The ID of the authenticated user who viewed the page. Null for guest users.
*   `session_id` (string): The session identifier for the visitor.
*   `path` (string): The path of the page that was viewed (e.g., `/products/cool-widget`).
*   `ip_address` (string, nullable): The IP address of the visitor.
*   `user_agent` (string, nullable): The user agent string of the visitor's browser.
*   `referrer` (string, nullable): The referring URL, if available.
*   `utm_source` (string, nullable): UTM source parameter.
*   `utm_medium` (string, nullable): UTM medium parameter.
*   `utm_campaign` (string, nullable): UTM campaign parameter.
*   `utm_term` (string, nullable): UTM term parameter.
*   `utm_content` (string, nullable): UTM content parameter.
*   `device_type` (string, nullable): Type of device (e.g., 'desktop', 'mobile', 'tablet').
*   `browser_name` (string, nullable): Name of the browser (e.g., 'Chrome', 'Firefox').
*   `platform_name` (string, nullable): Name of the operating system/platform (e.g., 'Windows', 'macOS', 'Linux').
*   `visited_at` (Carbon): Timestamp indicating when the page was viewed.

### Casts

*   `visited_at`: `'datetime'`
*   `user_id`: `'integer'`

### Relationships

*   `user()`: `BelongsTo` relationship with `App\Models\User`.
    *   Fetches the authenticated user associated with the page view, if any.

### Usage Examples

```php
// Recording a page view (typically done in middleware or a service)
// PageView::create([
//     'user_id' => auth()->id(), // Can be null
//     'session_id' => session()->getId(),
//     'path' => request()->path(),
//     'ip_address' => request()->ip(),
//     'user_agent' => request()->userAgent(),
//     'referrer' => request()->headers->get('referer'),
//     'utm_source' => request()->query('utm_source'),
//     // ... other UTM and device parameters
//     'visited_at' => now(),
// ]);

// Retrieving page views for a specific path
// $viewsForPath = PageView::where('path', '/pricing')->count();

// Getting recent page views by a user
// $user = User::find(1);
// $recentUserViews = $user->pageViews()->orderBy('visited_at', 'desc')->take(10)->get();
```

**Note:** The PHPDoc block for the `PageView` class in the source code lists additional properties like `@property string $date` and `@property int $views`. These are not actual database columns or direct model attributes/relationships but are likely used when results are grouped or aggregated in analytics queries (e.g., when fetching daily view counts). 

---
## `App\Models\Permission`

The `Permission` model defines specific actions or access rights that can be granted within the system. Permissions are typically associated with Roles to implement a Role-Based Access Control (RBAC) system.

### Fillable Attributes

*   `name` (string): A human-readable name for the permission (e.g., "Edit Posts", "Manage Users").
*   `slug` (string): A unique, URL-friendly identifier for the permission (e.g., `edit-posts`, `manage-users`). This is often used in code to check for permissions.
*   `description` (string, nullable): A more detailed explanation of what the permission allows.

### Relationships

*   `roles()`: `BelongsToMany` relationship with `App\Models\Role`.
    *   Returns a collection of roles that have this permission.

### Usage Examples

```php
// Creating a new permission
// $permission = Permission::create([
//     'name' => 'Create Articles',
//     'slug' => 'create-articles',
//     'description' => 'Allows a user to create new articles.'
// ]);

// Finding a permission by slug
// $editArticlesPermission = Permission::where('slug', 'edit-articles')->first();

// Getting roles associated with a permission
// if ($editArticlesPermission) {
//     $rolesWithEditAccess = $editArticlesPermission->roles;
//     foreach ($rolesWithEditAccess as $role) {
//         // echo $role->name;
//     }
// }

// (Permissions are usually assigned to roles, and then users are assigned roles)
// See Role model documentation for assigning permissions to roles.
```

---
## `App\Models\Role`

The `Role` model represents a user role within the application (e.g., Administrator, Editor, Subscriber). Roles are used to group permissions and assign them to users, forming a key part of the Role-Based Access Control (RBAC) system. This model implements the `App\Interfaces\HasPermissions` interface.

### Fillable Attributes

*   `name` (string): A human-readable name for the role (e.g., "Site Administrator").
*   `slug` (string): A unique, URL-friendly identifier for the role (e.g., `admin`, `editor`). This is often used in code.
*   `description` (string, nullable): A more detailed explanation of the role's purpose and responsibilities.

### Relationships

*   `users()`: `BelongsToMany` relationship with `App\Models\User`.
    *   Returns a collection of users who have this role.
*   `permissions()`: `BelongsToMany` relationship with `App\Models\Permission`.
    *   Returns a collection of permissions granted to this role.

### Methods

*   `hasPermission(string $permissionSlug): bool`:
    *   Checks if the role has a specific permission (identified by its slug).
    *   Example: `$adminRole->hasPermission('edit-settings');`

### Interface Implementation

*   Implements `App\Interfaces\HasPermissions`:
    *   `permissions()`: As described in Relationships.
    *   `hasPermission(string $permission): bool`: As described in Methods.

### Usage Examples

```php
// Creating a new role
// $adminRole = Role::create([
//     'name' => 'Administrator',
//     'slug' => config('roles.super_admin'),
//     'description' => 'Full access to all system features',
// ]);

// Finding a role
// $editorRole = Role::where('slug', 'editor')->first();

// Assigning a permission to a role
// $editArticlesPermission = Permission::where('slug', 'edit-articles')->first();
// if ($editorRole && $editArticlesPermission) {
//     $editorRole->permissions()->attach($editArticlesPermission);
// }

// Checking if a role has a permission
// if ($editorRole && $editorRole->hasPermission('edit-articles')) {
//     // Editor can edit articles
// }

// Assigning a role to a user (see User model documentation for user-side operations)
// $user = User::find(1);
// if ($user && $editorRole) {
//     $user->roles()->attach($editorRole);
// }

// Getting all users with a specific role
// if ($editorRole) {
//     $allEditors = $editorRole->users;
// }
``` 

---
## `App\Models\Setting`

The `Setting` model represents an individual configurable setting within the application. Settings are often used to control application behavior, store API keys, or manage content snippets without requiring code changes.

### Fillable Attributes

*   `setting_group_id` (int, nullable): The ID of the `SettingGroup` this setting belongs to. Can be null if the setting is not part of a group.
*   `key` (string): A unique key to identify the setting (e.g., `site_name`, `google_analytics_id`).
*   `display_name` (string): A human-readable name for the setting, often used in admin interfaces (e.g., "Site Name").
*   `description` (string, nullable): A more detailed explanation of the setting and its purpose.
*   `value` (string, nullable): The actual value of the setting. The interpretation of this value depends on the `type`.
*   `type` (`App\Enums\SettingType`): The data type of the setting, which dictates how its value is stored, validated, and rendered. See [SettingType Enum](#settingtype-enum).
*   `options` (array, nullable): A JSON column to store additional options, typically used for `select`, `radio`, or `checkbox` types (e.g., `["key1" => "Value 1", "key2" => "Value 2"]`).
*   `is_public` (boolean): Indicates if the setting can be exposed publicly (e.g., via an API or directly in frontend views). Defaults to `false`.
*   `is_required` (boolean): Indicates if the setting must have a value. Defaults to `false`.
*   `order` (int, nullable): An integer to define the display order of the setting within its group or in a list.

### Casts

*   `options`: `'array'`
*   `is_public`: `'boolean'`
*   `is_required`: `'boolean'`
*   `type`: `App\Enums\SettingType::class` (Casts the `type` string to a `SettingType` enum instance).

### Relationships

*   `group()`: `BelongsTo` relationship with `App\Models\SettingGroup` (via `setting_group_id`).
    *   Fetches the group this setting belongs to, if any.

### Accessors

*   `getFormattedValueAttribute()`: Returns the setting's `value` cast to its appropriate PHP type based on the `type` enum.
    *   `SettingType::BOOLEAN`, `SettingType::CHECKBOX`: casts to `bool`.
    *   `SettingType::NUMBER`: casts to `int`.
    *   `SettingType::JSON`: decodes the JSON string into a PHP array.
    *   Other types: returns the value as is (string).

### Usage Examples

```php
// Creating a new setting (often done via an admin panel or seeder)
// $newSetting = Setting::create([
//     'setting_group_id' => 1, // Assuming a SettingGroup with ID 1 exists
//     'key' => 'maintenance_mode',
//     'display_name' => 'Maintenance Mode',
//     'value' => '0',
//     'type' => App\Enums\SettingType::BOOLEAN,
//     'is_required' => true,
// ]);

// Retrieving a setting by key
// $siteNameSetting = Setting::where('key', 'site_name')->first();
// if ($siteNameSetting) {
//     $siteName = $siteNameSetting->formatted_value; // Accessor gives the typed value
// }

// Retrieving a setting via a helper/facade (common pattern)
// $analyticsId = App\Facades\Settings::get('google_analytics_id');

// Updating a setting
// if ($siteNameSetting) {
//     $siteNameSetting->update(['value' => 'My Awesome New Site Name']);
// }
```

---
### `App\Enums\SettingType` {#settingtype-enum}

This enum defines the possible data types for a `Setting` model's `value` attribute. It helps in validating, storing, and rendering settings appropriately.

**Enum Cases (Value: string):**

*   `TEXT`: `'text'`
*   `TEXTAREA`: `'textarea'`
*   `SELECT`: `'select'`
*   `CHECKBOX`: `'checkbox'`
*   `RADIO`: `'radio'`
*   `COLOR`: `'color'`
*   `FILE`: `'file'`
*   `NUMBER`: `'number'`
*   `EMAIL`: `'email'`
*   `URL`: `'url'`
*   `PASSWORD`: `'password'`
*   `DATE`: `'date'`
*   `DATETIME`: `'datetime'`
*   `TIME`: `'time'`
*   `BOOLEAN`: `'boolean'`
*   `JSON`: `'json'`

**Static Methods:**

*   `toArray(): array<string, string>`: Returns an associative array of all setting types where keys and values are the enum case values (e.g., `['text' => 'text', ...]`)

**Instance Methods:**

*   `label(): string`: Returns a human-readable label for the enum case (e.g., `SettingType::TEXTAREA->label()` returns `'Text Area'`).

**Example Usage:**

```php
// $setting = Setting::find(1);
// if ($setting->type === App\Enums\SettingType::BOOLEAN) {
//     // Handle boolean setting
// }

// $typeLabel = App\Enums\SettingType::JSON->label(); // "JSON"

// $allTypes = App\Enums\SettingType::toArray();
// // Used for populating dropdowns in UI, for example.
``` 

---
## `App\Models\SettingGroup`

The `SettingGroup` model is used to organize individual `Setting` records into logical groups. This is primarily for improving the user experience in administrative interfaces where settings are managed.

### Fillable Attributes

*   `name` (string): A human-readable name for the group (e.g., "General Settings", "Mail Configuration").
*   `slug` (string): A unique, URL-friendly identifier for the group (e.g., `general`, `mail`).
*   `description` (string, nullable): A brief description of what settings this group contains.
*   `icon` (string, nullable): An optional identifier for an icon to be displayed next to the group name (e.g., a CSS class for an icon font like Font Awesome, or an SVG icon name).
*   `order` (int, nullable): An integer to define the display order of this group among other setting groups.

### Relationships

*   `settings()`: `HasMany` relationship with `App\Models\Setting`.
    *   Returns a collection of all settings that belong to this group.
    *   The related settings are automatically ordered by their own `order` attribute.

### Usage Examples

```php
// Creating a new setting group
// $generalGroup = SettingGroup::create([
//     'name' => 'General',
//     'slug' => 'general',
//     'description' => 'Basic site-wide settings.',
//     'icon' => 'cog', // Example icon identifier
//     'order' => 1,
// ]);

// Finding a setting group and its settings
// $mailGroup = SettingGroup::where('slug', 'mail')->first();
// if ($mailGroup) {
//     $mailSettings = $mailGroup->settings; // Returns ordered collection of Setting models
//     foreach ($mailSettings as $setting) {
//         // echo $setting->display_name . ": " . $setting->value;
//     }
// }

// A setting is associated with a group via its `setting_group_id` attribute.
// Setting::create([
//     'setting_group_id' => $generalGroup->id,
//     'key' => 'site_tagline',
//     // ... other setting attributes
// ]);
``` 

---
## `App\Models\Taxonomy`

The `Taxonomy` model represents a classification system that can be used to categorize or group other models (e.g., posts, products). Common examples include "Category" and "Tag". Taxonomies can be hierarchical (like categories with subcategories) or flat (like tags).

### Fillable Attributes

*   `name` (string): A human-readable name for the taxonomy (e.g., "Product Categories", "Article Tags").
*   `slug` (string): A unique, URL-friendly identifier for the taxonomy (e.g., `product-category`, `article-tag`). This is used to fetch the taxonomy programmatically.
*   `description` (string, nullable): A brief explanation of what this taxonomy is used for.
*   `hierarchical` (boolean): Indicates whether the terms within this taxonomy can have parent-child relationships (e.g., categories can be hierarchical, tags are usually not). Defaults to `false`.

### Casts

*   `hierarchical`: `'boolean'`

### Relationships

*   `terms()`: `HasMany` relationship with `App\Models\Term`.
    *   Returns a collection of all terms belonging to this taxonomy.
*   `rootTerms()`: A specific query on the `terms` relationship.
    *   Returns a collection of terms belonging to this taxonomy that do not have a `parent_id` (i.e., they are top-level terms). This is particularly useful for hierarchical taxonomies.

### Query Scopes

*   `scopeSlug($slug)`: Filters taxonomies by a specific `slug`.
    *   Example: `Taxonomy::slug('post-category')->first();`

### Usage Examples

```php
// Creating a new taxonomy
// $categoryTaxonomy = Taxonomy::create([
//     'name' => 'Categories',
//     'slug' => 'category',
//     'description' => 'Used to categorize articles and posts.',
//     'hierarchical' => true,
// ]);

// $tagTaxonomy = Taxonomy::create([
//     'name' => 'Tags',
//     'slug' => 'tag',
//     'hierarchical' => false,
// ]);

// Finding a taxonomy by slug
// $postCategories = Taxonomy::slug('category')->first();

// Getting all terms for a taxonomy
// if ($postCategories) {
//     $allCategoryTerms = $postCategories->terms;
//     foreach ($allCategoryTerms as $term) {
//         // echo $term->name;
//     }
// }

// Getting root terms for a hierarchical taxonomy
// if ($postCategories && $postCategories->hierarchical) {
//     $rootCategoryTerms = $postCategories->rootTerms;
//     // Display these, then recursively fetch children for each if needed
// }
``` 

---
## `App\Models\Term`

The `Term` model represents an individual item within a `Taxonomy` (e.g., a specific category like "PHP News" or a tag like "Laravel"). Terms are used to classify content.

### Fillable Attributes

*   `taxonomy_id` (int): The ID of the `Taxonomy` this term belongs to.
*   `name` (string): The name of the term (e.g., "Tutorials", "Product Updates").
*   `slug` (string): A unique, URL-friendly identifier for the term, typically unique within its taxonomy (e.g., `tutorials`, `product-updates`).
*   `description` (string, nullable): A brief description of the term.
*   `parent_id` (int, nullable): The ID of the parent `Term` if this term is a child in a hierarchical taxonomy (e.g., a subcategory). Null for top-level terms.
*   `order` (int): An integer to define the display order of the term, usually within its parent or taxonomy. Defaults to `0`.
*   `meta` (array, nullable): A JSON column to store any additional custom metadata about the term.

### Casts

*   `meta`: `'array'`
*   `order`: `'integer'`

### Relationships

*   `taxonomy()`: `BelongsTo` relationship with `App\Models\Taxonomy`.
    *   Fetches the taxonomy this term is part of.
*   `parent()`: `BelongsTo` relationship with `App\Models\Term` (self-referencing, via `parent_id`).
    *   Fetches the parent term if this term is a child.
*   `children()`: `HasMany` relationship with `App\Models\Term` (self-referencing, via `parent_id`).
    *   Fetches all direct child terms of this term.
*   `taxonomables()`: `MorphToMany` relationship (via the `taxonomables` table, `taxonomable_type`, `taxonomable_id`).
    *   This is the inverse of the `terms()` relationship found in models using the `HasTaxonomies` trait.
    *   Returns a collection of all models (e.g., Posts, Products) that have been assigned this term.

### Query Scopes

*   `scopeTaxonomySlug($slug)`: Filters terms belonging to a taxonomy with the given slug.
    *   Example: `Term::taxonomySlug('category')->get();`
*   `scopeOrdered()`: Orders terms by their `order` attribute in ascending order.

### Methods

*   `getPath(): array`: Returns an array of `Term` objects representing the hierarchical path from the root term to the current term. The current term is the last element in the array.
*   `descendants(): \Illuminate\Database\Eloquent\Collection`: Recursively fetches all descendant terms (children, grandchildren, etc.) of the current term.

### Usage Examples

```php
// Creating a new term (usually associated with a Taxonomy)
// $categoryTaxonomy = Taxonomy::slug('category')->first();
// if ($categoryTaxonomy) {
//     $phpTerm = Term::create([
//         'taxonomy_id' => $categoryTaxonomy->id,
//         'name' => 'PHP',
//         'slug' => 'php',
//     ]);

//     // Creating a sub-term (child)
//     $laravelTerm = Term::create([
//         'taxonomy_id' => $categoryTaxonomy->id,
//         'name' => 'Laravel',
//         'slug' => 'laravel',
//         'parent_id' => $phpTerm->id,
//     ]);
// }

// Finding a term by slug within a taxonomy
// $newsTerm = Term::taxonomySlug('category')->where('slug', 'news')->first();

// Getting parent and children
// if ($laravelTerm && $laravelTerm->parent) {
//     // echo "Parent of Laravel: " . $laravelTerm->parent->name; // PHP
// }
// if ($phpTerm) {
//     $childrenOfPhp = $phpTerm->children; // Collection containing Laravel term
// }

// Getting all posts tagged with 'laravel'
// if ($laravelTerm) {
//    // Assuming Post model uses HasTaxonomies trait and is configured for 'taxonomable'
//    // $laravelPosts = $laravelTerm->taxonomables()->where('taxonomable_type', App\Models\Post::class)->get();
// }

// Get path for a term
// if ($laravelTerm) {
//     $path = $laravelTerm->getPath(); // [PHP Term, Laravel Term]
// }

// Get all descendants
// if ($phpTerm) {
//    $allDescendants = $phpTerm->descendants();
// }
``` 