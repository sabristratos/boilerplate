# Translatable Models

This project uses the [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable) package to handle translations for user-facing content. This document explains which models are translatable and how to work with them.

## Translatable Models

The following models support translations:

1. **LegalPage**
   - `title`
   - `slug`
   - `content`
   - `meta_title`
   - `meta_description`

2. **SettingGroup**
   - `name`
   - `description`

3. **Setting**
   - `display_name`
   - `description`
   - `options`

4. **Role**
   - `name`
   - `description`

5. **Taxonomy**
   - `name`
   - `description`

6. **Term**
   - `name`
   - `description`

## Working with Translatable Models

### Setting Translations

```php
// Set a translation for a specific locale
$model->setTranslation('name', 'en', 'English Name');
$model->setTranslation('name', 'fr', 'French Name');

// Set multiple translations at once
$model->setTranslations('name', [
    'en' => 'English Name',
    'fr' => 'French Name',
]);

// Save the model after setting translations
$model->save();
```

### Getting Translations

```php
// Get a translation for the current locale
$name = $model->name;

// Get a translation for a specific locale
$name = $model->getTranslation('name', 'fr');

// Get all translations
$translations = $model->getTranslations('name');

// Check if a translation exists
$hasTranslation = $model->hasTranslation('name', 'fr');
```

### Available Locales

```php
// Get all locales that have a translation
$locales = $model->getTranslatedLocales('name');

// Get available locales as a comma-separated string
$localesString = $model->available_locales_as_string;

// Get the first available locale
$firstLocale = $model->first_available_locale;
```

### In Blade Views

```blade
{{-- Display the translation for the current locale --}}
{{ $model->name }}

{{-- Display a translation for a specific locale --}}
{{ $model->getTranslation('name', 'fr') }}

{{-- Display available locales --}}
{{ $model->available_locales_as_string }}
```

### In Livewire Components

When working with translatable fields in Livewire components:

1. Define the translatable fields as arrays:
```php
public array $name = [];
public array $description = [];
```

2. Initialize translations in the mount method:
```php
public function mount(Model $model): void
{
    $this->locales = config('app.available_locales', ['en' => 'English']);
    
    foreach ($this->locales as $localeCode => $localeName) {
        $this->name[$localeCode] = $model->getTranslation('name', $localeCode);
        $this->description[$localeCode] = $model->getTranslation('description', $localeCode);
    }
}
```

3. Save translations in the save method:
```php
public function save(): void
{
    foreach ($this->locales as $localeCode => $localeName) {
        $this->model->setTranslation('name', $localeCode, $this->name[$localeCode]);
        $this->model->setTranslation('description', $localeCode, $this->description[$localeCode]);
    }
    
    $this->model->save();
}
```

## Best Practices

1. **Always provide translations for all available locales** when creating or updating models.
2. **Use the current locale** when displaying content to users.
3. **Validate translations** for required fields in all available locales.
4. **Keep translations in sync** across related models (e.g., if a term's name changes, update any references to it).
5. **Use the `available_locales_as_string` attribute** to show which translations are available.
6. **Consider fallback locales** when a translation is missing.

## Adding New Translatable Models

To make a model translatable:

1. Add the `HasTranslations` trait:
```php
use Spatie\Translatable\HasTranslations;

class Model extends Model
{
    use HasTranslations;
}
```

2. Define translatable attributes:
```php
public array $translatable = [
    'name',
    'description',
];
```

3. Create a migration to change the fields to JSON:
```php
Schema::table('table', function (Blueprint $table) {
    $table->json('name')->change();
    $table->json('description')->nullable()->change();
});
```

4. Add helper methods for locale information:
```php
public function getAvailableLocalesAsStringAttribute(): string
{
    return collect($this->getTranslatedLocales('name'))
        ->filter()
        ->implode(', ');
}

public function getFirstAvailableLocaleAttribute(): ?string
{
    $locales = $this->getTranslatedLocales('name');
    return array_shift($locales);
}
``` 