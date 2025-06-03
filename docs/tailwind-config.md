# Tailwind CSS v4.0 Configuration

This document explains how Tailwind CSS v4.0 is configured in this project.

## CSS-First Configuration

Tailwind CSS v4.0 uses a CSS-first configuration approach, which means that all configuration is done directly in the CSS file instead of a separate JavaScript configuration file.

In this project, the configuration is in `resources/css/app.css`:

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
}
```

## Key Components

### Import Statement

```css
@import 'tailwindcss';
```

This imports Tailwind CSS into your CSS file. It replaces the old `@tailwind` directives used in previous versions.

### Source Directives

```css
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

These directives specify which files Tailwind CSS should scan for class names. They replace the `content` array in the old `tailwind.config.js` file.

### Theme Configuration

```css
@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
}
```

This block configures the theme for Tailwind CSS. It replaces the `theme` object in the old `tailwind.config.js` file. All variables defined here are available as CSS variables in your application.

## Adding Custom Configuration

To add custom configuration, simply add more variables to the `@theme` block:

```css
@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
    --color-primary: oklch(0.6 0.2 240);
    --spacing-xl: 2.5rem;
}
```

## Vite Integration

This project uses the official Tailwind CSS Vite plugin for integration with Vite:

```js
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

## Further Reading

For more information about Tailwind CSS v4.0, see the [official documentation](https://tailwindcss.com/docs) or the `docs/tailwind 4.md` file in this project.
