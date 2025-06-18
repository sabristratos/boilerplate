# Project Overview

This project is a comprehensive Laravel 11 boilerplate designed to accelerate the development of modern, feature-rich web applications. It comes pre-configured with a variety of essential features, following best practices and a clean, maintainable architecture.

The core philosophy is to provide a solid foundation for common application requirements, allowing developers to focus on building unique features rather than reinventing the wheel.

## Core Stack

- **Backend:** Laravel 11, PHP 8.2
- **Admin Frontend:** Livewire 3, Alpine.js, Tailwind CSS, Vite
- **Admin UI Kit:** The project uses the **Flux** UI component library, documented in the `/docs/flux` directory.

## Key Features

- **Modular CRUD System:** A powerful, configuration-based system for rapidly scaffolding CRUD interfaces.
- **Authentication:** Complete, ready-to-use authentication and authorization flows, including registration, email verification, password reset, and two-factor authentication (2FA).
- **Roles & Permissions:** A flexible, custom-built role-based access control (RBAC) system.
- **Settings Management:** A version-controlled system for managing application settings via a config file (`config/settings.php`) and a sync command.
- **Taxonomies:** A generic system for creating and managing taxonomies (categories, tags, etc.) and associating them with other models.
- **File Attachments:** An easy-to-use system for attaching files to any Eloquent model.
- **Notifications:** A robust notification system with support for database and email channels, as well as daily/weekly digests.
- **Activity Logging:** A simple, facade-based system for logging key user actions throughout the application.
- **Impersonation:** The ability for administrators to log in as other users for debugging and support.
- **Admin Panel:** A clean, modern admin panel built entirely with Livewire and the Flux UI Kit.
- **Static & Legal Pages:** A simple system for managing static content pages like "Terms of Service" and "Privacy Policy." 