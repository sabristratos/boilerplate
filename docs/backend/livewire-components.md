# Livewire Components

This document provides an overview of the Livewire components used in the application. Livewire is a full-stack framework for Laravel that makes building dynamic interfaces simple, without leaving the comfort of PHP.

Components are typically organized by their functional area within the `app/Livewire/` directory.

## Admin Components (`app/Livewire/Admin/`)

This section covers Livewire components specifically used within the admin panel.

### `App\Livewire\Admin\ActivityLogManagement`

This component is responsible for displaying, filtering, and managing activity logs within the admin panel. It provides a paginated view of logs with options for searching, sorting, and detailed inspection of individual log entries.

**Layout:** `components.admin-layout`

**Traits Used:**

*   `Livewire\WithPagination`: Enables easy pagination of log records.

**Key Public Properties (Filters & Controls):**

*   `search` (string): Search term applied to the log description.
*   `logName` (string): Filters logs by their `log_name`.
*   `event` (string): Filters logs by the `event` name.
*   `dateFrom` (string): Start date for filtering logs by creation date.
*   `dateTo` (string): End date for filtering logs by creation date.
*   `causerId` (string): Filters logs by the ID of the user/model that caused the event.
*   `subjectType` (string): Filters logs by the type of model that was the subject of the event.
*   `perPage` (int): Number of log entries to display per page (default: `15`).
*   `sortField` (string): Field to sort logs by (default: `'created_at'`).
*   `sortDirection` (string): Sorting direction (default: `'desc'`).
*   `selectedLog` (`App\Models\ActivityLog`|null): Holds the currently selected log entry for viewing details.

**Key Methods:**

*   `resetFilters()`: Clears all active filter values.
*   `sortBy(string $field)`: Sets the sort field and toggles the sort direction.
*   `viewDetails(ActivityLog $log)`: Sets the `selectedLog` property to display its details.
*   `closeDetails()`: Clears the `selectedLog`, typically closing the details view.
*   `render()`: Fetches `ActivityLog` records from the database based on the current filter and sorting states. It then passes the paginated logs to the `livewire.admin.activity-log-management` Blade view.

**Computed Properties (for filter dropdowns):**

*   `getLogNamesProperty()`: Returns an array of unique `log_name` values from the `activity_logs` table.
*   `getEventsProperty()`: Returns an array of unique `event` values from the `activity_logs` table.
*   `getSubjectTypesProperty()`: Returns an array of unique `subject_type` values from the `activity_logs` table.

**Usage:**

This component is typically rendered via a route definition (as seen in `routes/web.php` under `/admin/activity-logs`). The corresponding Blade view (`resources/views/livewire/admin/activity-log-management.blade.php`) will contain the HTML structure, filter inputs, the table to display logs, and a section/modal to show details of `selectedLog`.

**Example Interactions:**

*   User types in the search bar, updating the `search` property and re-rendering the component with filtered results.
*   User selects a log name from a dropdown, updating `logName` and re-fetching logs.
*   User clicks on a table header to sort by a specific column, triggering the `sortBy()` method.
*   User clicks a "View Details" button for a log entry, calling `viewDetails()` to populate `selectedLog` and display more information.

### `App\Livewire\Admin\AnalyticsDashboard`

This component provides data for the analytics dashboard within the admin panel. It aggregates and displays various metrics derived from the `PageView` model.

**Layout:** `components.admin-layout`

**Key Public Properties (Data Points):**

*   `totalPageViews` (int): Total number of page views recorded.
*   `uniqueVisitorsToday` (int): Count of unique visitor sessions for the current day.
*   `pageViewsToday` (int): Total page views for the current day.
*   `pageViewsLast7Days` (int): Total page views over the last 7 days.
*   `pageViewsLast30Days` (int): Total page views over the last 30 days.
*   `topPages` (array): List of the top 10 most viewed pages (path and view count).
*   `topReferrers` (array): List of the top 10 referrers (referrer URL and view count).
*   `topBrowsers` (array): List of the top 5 browsers used by visitors.
*   `topPlatforms` (array): List of the top 5 operating systems/platforms used by visitors.
*   `pageViewsOverTimeData` (array): Data points for a chart showing page views per day over the last 30 days (date and view count).

**Key Methods:**

*   `mount()`: This lifecycle hook calls `loadAnalyticsData()` when the component is initialized.
*   `loadAnalyticsData()`: Fetches all the analytics metrics from the `PageView` model using various Eloquent queries (counting, grouping, ordering by views, and filtering by date ranges). The results are stored in the component's public properties.
*   `render()`: Renders the `livewire.admin.analytics-dashboard` Blade view. The public properties containing the analytics data are automatically available to this view.

**Usage:**

This component is routed via `/admin/analytics` (as seen in `routes/web.php`). The `resources/views/livewire/admin/analytics-dashboard.blade.php` view is responsible for presenting the analytics data. This typically involves displaying summary statistics (cards, numbers) and charts (e.g., for page views over time, top pages/referrers).

**Functionality:**

The primary role of this component is to gather and prepare data for display. It does not appear to have interactive elements like filters or date range selectors directly within the PHP component itself; such features, if present, would likely be handled by JavaScript in the view or potentially through other Livewire components or page reloads with query parameters if more advanced filtering is needed.

The data is loaded once when the component mounts. The `loadAnalyticsData()` method is public, so it could potentially be called again (e.g., by a button in the view `wire:click="loadAnalyticsData"`) to refresh the displayed analytics.

### `App\Livewire\Admin\AttachmentManagement`

This component provides a comprehensive interface for managing file attachments within the admin panel. It allows users to upload new files, view existing attachments, replace files, and delete them. It integrates with system settings for validation and uses an `AttachmentService` for file operations and an `ActivityLogger` for tracking changes.

**Layout:** `components.admin-layout`

**Traits Used:**

*   `Livewire\WithFileUploads`: Enables Livewire to handle real-time file uploads.
*   `Livewire\WithPagination`: Provides pagination for the list of attachments.

**Key Public Properties:**

*   `file` (mixed): Holds the new file selected for upload.
*   `collection` (string|null): Optional collection name for categorizing the new upload.
*   `search` (string): Search term used to filter attachments by filename, collection, or MIME type.
*   `perPage` (int): Number of attachments to display per page (default: `10`).
*   `attachmentToReplace` (`App\Models\Attachment`|null): Stores the attachment model instance that is currently being targeted for replacement.
*   `replacementFile` (mixed): Holds the new file selected to replace an existing attachment.
*   `showingReplaceModal` (bool): Controls the visibility of the modal dialog used for file replacement.

**Key Methods:**

*   `getFileValidationRules(): array`: Dynamically generates validation rules for new file uploads based on system settings (e.g., `attachments_max_upload_size_kb`, `attachments_allowed_extensions` fetched via `App\Facades\Settings`).
*   `getReplacementFileValidationRules(): array`: Similar to `getFileValidationRules()`, but for the replacement file input.
*   `rules(): array`: Merges the validation rules for both new uploads and replacements.
*   `uploadFile(AttachmentService $attachmentService)`: Handles the upload of a new file. It validates the file, uses `AttachmentService` to store it (associating with the authenticated user and optional collection), logs the creation event using `ActivityLogger`, displays a success/error toast via `Flux::toast`, and refreshes the UI.
*   `showReplaceModal(int $attachmentId)`: Prepares for a file replacement by fetching the specified `Attachment`, storing it in `attachmentToReplace`, and setting `showingReplaceModal` to true. It also clears any previous validation errors and the `replacementFile` input.
*   `processReplace(AttachmentService $attachmentService)`: Processes the file replacement. It validates the `replacementFile`, uses `AttachmentService` to replace the old file with the new one, logs the update event (including old and new file details), closes the modal, shows a toast, and refreshes the UI.
*   `delete(Attachment $attachment, AttachmentService $attachmentService)`: Handles the deletion of an attachment. It logs the deletion event (preserving details of the attachment being deleted), uses `AttachmentService` to perform the deletion (which should also remove the physical file via model events), shows a toast, and refreshes the UI.
*   `closeReplaceModal()`: Hides the replacement modal and resets related properties like `attachmentToReplace`, `replacementFile`, and validation errors.
*   `render()`: Fetches `Attachment` records, applying the `search` filter (on `filename`, `collection`, `mime_type`), orders them by the latest, paginates them, and passes the data to the `livewire.admin.attachment-management` Blade view.

**Event Dispatches:**

*   `lightbox:refresh`: Dispatched after successful upload, replacement, or deletion. This suggests an integration with a JavaScript lightbox library to refresh its state if it displays attachments.
*   `$refresh`: Dispatched after successful replacement to trigger a full component refresh.

**Dependencies:**

*   `App\Services\AttachmentService`: For core file handling logic (upload, replace, delete).
*   `App\Facades\ActivityLogger`: For logging create, update, delete actions.
*   `App\Facades\Settings`: For retrieving dynamic validation settings.
*   `Flux\Flux`: Appears to be a custom helper or facade for displaying toast notifications.

**Usage:**

This component is rendered via the `/admin/attachments` route. The `resources/views/livewire/admin/attachment-management.blade.php` view would contain the upload form, search input, the list/grid of attachments with actions (replace, delete, view), and the modal for replacing files.

### `App\Livewire\Admin\RoleManagement`

This component manages roles and their associated permissions within the admin panel. It provides a complete CRUD (Create, Read, Update, Delete) interface for roles, including assigning permissions, pagination, and search functionality.

**Layout:** `components.admin-layout`

**Traits Used:**

*   `Livewire\WithPagination`: Enables pagination for the list of roles.

**Key Public Properties (Form Data & State):**

*   `role_id` (int|null): The ID of the role currently being edited or confirmed for deletion.
*   `name` (string): The name of the role.
*   `slug` (string): The URL-friendly slug for the role. Automatically generated from `name` during creation or if the name changes during an edit.
*   `description` (string): An optional description for the role.
*   `selectedPermissions` (array): An array of permission IDs selected to be associated with the role.
*   `isCreating` (bool): True if the component is in "create new role" mode.
*   `isEditing` (bool): True if the component is in "edit existing role" mode.
*   `confirmingDelete` (bool): True if the component is in "confirm role deletion" mode.
*   `showModal` (bool): Controls the visibility of the modal used for creating, editing, or confirming deletion of roles.
*   `search` (string): Search term applied to role name, slug, or description.
*   `perPage` (int): Number of roles to display per page (default: `10`).

**Validation Rules (`rules()` & `messages()`):**

*   `name`: Required, string, max 255.
*   `slug`: Required, string, max 255. Dynamically ensures uniqueness (unique on create, unique ignoring self on edit).
*   `description`: Nullable, string.
*   `selectedPermissions`: Must be an array.
*   Custom messages are provided for `name.required`, `slug.required`, and `slug.unique`.

**Key Methods:**

*   `updatedName(string $value)`: Automatically generates `slug` from `name` when `isCreating` is true, or when `isEditing` and the name has actually changed.
*   `create()`: Resets form properties and validation, sets `isCreating` to true, and shows the modal.
*   `store()`: Validates the form data, creates a new `Role`, attaches `selectedPermissions`, logs the creation event with `ActivityLogger`, closes the modal, and displays a success toast using `Flux::toast`.
*   `edit(Role $role)`: Loads the given `Role`'s data (including its permission IDs) into the form properties, sets `isEditing` to true, and shows the modal.
*   `update()`: Validates form data, updates the existing `Role`, syncs its `selectedPermissions`, logs the update event (including old and new values), closes the modal, and shows a success toast.
*   `confirmDelete(Role $role)`: Sets `role_id` and `name` for the role to be deleted, sets `confirmingDelete` to true, and shows the modal for confirmation.
*   `delete()`: If a `role_id` is set, it finds the `Role`, logs its deletion (including associated permission IDs and user count), detaches all associated permissions and users, deletes the role record, closes the modal, and shows a success toast.
*   `closeModal()`: Hides the modal, resets all form and state properties (`isCreating`, `isEditing`, `confirmingDelete`), and clears validation errors.
*   `render()`: Fetches a paginated list of `Role` models. 
    *   Applies search to `name`, `slug`, and `description` fields.
    *   Includes counts of associated `users` and `permissions` for each role (`withCount('users', 'permissions')`).
    *   Orders roles by latest.
    *   Fetches all `Permission` models (for populating the permissions checklist in the form).
    *   Passes `roles` and `permissions` to the `livewire.admin.role-management` Blade view.

**Dependencies:**

*   `App\Models\Role`, `App\Models\Permission`: Eloquent models for data interaction.
*   `App\Facades\ActivityLogger`: For logging CRUD actions.
*   `Flux\Flux`: For displaying toast notifications.
*   `Illuminate\Support\Str`: For slug generation.
*   `Illuminate\Support\Facades\Log`: For error logging in catch blocks.

**Usage:**

This component is rendered via the `/admin/roles` route. The `resources/views/livewire/admin/role-management.blade.php` view contains the UI for listing roles, the search input, action buttons (create, edit, delete), and the modal form for role creation/editing and deletion confirmation. The form would include inputs for role details and a checklist for selecting permissions.

### `App\Livewire\Admin\SettingsManagement`

This component provides the interface for managing application settings in the admin panel. It allows administrators to view and update settings, which are typically grouped for easier navigation (e.g., in a tabbed layout).

**Layout:** `components.admin-layout`

**Key Public Properties:**

*   `tab` (string): Stores the key of the currently active settings group/tab. This property is synchronized with the URL (`Livewire\Attributes\Url`). Defaults to `'general'`.
*   `values` (array): An associative array holding the current values of all settings, keyed by the setting's unique `key`. This array is bound to form inputs in the Blade view.

**Key Methods:**

*   `mount()`: Calls `loadSettings()` to initialize the settings values when the component is first loaded.
*   `loadSettings()`: Fetches all `Setting` models from the database. It populates the `$values` array, ensuring that settings of type `BOOLEAN` or `CHECKBOX` are cast to actual boolean values.
*   `save()`:
    1.  Resets any existing validation errors.
    2.  Retrieves all `Setting` models again, keyed by their `key`.
    3.  Dynamically constructs validation rules for the `$values` array. For each setting:
        *   Adds a `'required'` rule if `is_required` is true.
        *   Adds type-specific rules (e.g., `'email'` for `SettingType::EMAIL`, `'url'` for `SettingType::URL`, `'numeric'` for `SettingType::NUMBER`).
    4.  Sets custom validation attribute names using each setting's `display_name` for more user-friendly error messages.
    5.  Validates the `$this->values` data using the generated rules.
    6.  If validation fails, it populates the error bag and displays a toast notification via `Flux::toast`.
    7.  If validation succeeds, it iterates through `$this->values`. For each key-value pair, it updates the corresponding setting in the database using `App\Facades\Settings::set($key, $value)`.
    8.  Calls `loadSettings()` again to refresh the `$values` array with the persisted (and potentially type-casted) data.
    9.  Displays a success toast.
    10. Redirects the user back to the referring page (typically the settings page itself).
*   `render()`: Fetches all setting groups using `App\Facades\Settings::allGroups()`. It then passes these `groups` (along with the public `$values` and `$tab` properties) to the `livewire.admin.settings-management` Blade view.

**Dependencies:**

*   `App\Models\Setting`, `App\Models\SettingGroup`: For interacting with settings data.
*   `App\Enums\SettingType`: Used for type-specific logic (casting, validation).
*   `App\Facades\Settings`: A facade likely providing helper methods to get/set settings and retrieve groups (e.g., `Settings::set()`, `Settings::allGroups()`).
*   `Flux\Flux`: For displaying toast notifications.
*   `Illuminate\Support\Facades\Validator`: Used for dynamic validation.

**Usage:**

This component is rendered via the `/admin/settings` route. The `resources/views/livewire/admin/settings-management.blade.php` view would typically:
*   Use the `$groups` and `$tab` properties to render a tabbed navigation for different setting groups.
*   Within each tab, iterate over the settings belonging to that group.
*   For each setting, display an appropriate form input (e.g., text input, textarea, checkbox, select) bound to the corresponding entry in the `$values` array (e.g., `wire:model="values.site_name"`).
*   Display validation errors associated with each input.
*   Provide a save button that triggers the `save()` method. 