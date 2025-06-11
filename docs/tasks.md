# Codebase Improvement Tasks

This document outlines a series of tasks to improve the codebase, focusing on architectural refactoring, code quality, and maintainability.

## 1. Architectural Refactoring

The current implementation uses large, monolithic Livewire components that handle too many responsibilities. The following tasks aim to refactor these components into smaller, more focused components and introduce a service layer for business logic.

- [x] **Refactor `UserManagement` Component:**
    - [x] Create a `UserForm` Livewire component to handle user creation and editing forms, including validation logic.
    - [x] Create a `UserList` Livewire component to display the list of users with search and pagination.
    - [x] Extract the create/edit modal into a `UserModal` Blade component that uses the `UserForm` component.
    - [x] Extract the delete confirmation modal into a reusable `DeleteConfirmationModal` Blade component.
    - [x] Create a `UserService` to handle the business logic of creating, updating, and deleting users, including role assignment, notifications, and activity logging.
    - [x] The `UserManagement` component should be refactored to act as a container, coordinating the `UserList` and modals.

- [x] **Refactor `RoleManagement` Component:**
    - [x] Create a `RoleForm` Livewire component to handle role creation and editing forms, including validation and permission selection.
    - [x] Create a `RoleList` Livewire component to display the list of roles with search and pagination.
    - [x] Extract the create/edit modal into a `RoleModal` Blade component that uses the `RoleForm` component.
    - [x] Create a `RoleService` to handle the business logic of creating, updating, and deleting roles, including permission syncing and activity logging.

- [x] **Refactor `TaxonomyManagement` Component:**
    - [x] Split the component into `TaxonomyManagement` and `TermManagement`.
    - [x] The `TaxonomyManagement` component will handle CRUD for taxonomies.
        - [x] Create a `TaxonomyForm` Livewire component.
        - [x] Create a `TaxonomyList` Livewire component.
        - [x] Create a `TaxonomyService`.
    - [x] The `TermManagement` component will handle CRUD for terms of a selected taxonomy.
        - [x] The component should accept a `taxonomy_id` prop.
        - [x] Create a `TermForm` Livewire component.
        - [x] Create a `TermList` Livewire component that can render hierarchical terms.
        - [x] Create a `TermService`.
    - [x] Implement event-based communication between `TaxonomyManagement` and `TermManagement`.

## 2. Code Quality & Consistency

- [x] **Use Translation Strings:**
    - [x] Replace all hardcoded strings in Livewire components (e.g., toast messages, log messages) with translatable strings using the `__()` helper.

- [ ] **Consistent Validation:**
    - [ ] For complex validation scenarios in controllers, use Form Requests instead of inline validation. The current Livewire validation is good, but this should be a rule for any controller-based validation.

- [ ] **Use Settings Facade:**
    - [ ] Audit the codebase for any hardcoded configuration values and move them to the settings system, accessible via the `Settings` facade.

- [ ] **N+1 Queries:**
    - [ ] Review all data tables and lists and ensure that relationships are eager-loaded using `with()` to prevent N+1 query issues. A tool like Laravel Telescope could help identify these issues.

## 3. Frontend Improvements

- [ ] **Reusable Blade Components:**
    - [ ] Create a library of reusable Blade components for common UI elements like modals, buttons, form inputs, and tables, to be used across the admin panel. The `flux:` components should be the foundation for this.

- [ ] **GSAP Animations:**
    - [ ] Identify areas where complex UI animations could improve user experience and implement them using GSAP, as stated in the project's best practices. For example, animating modal transitions or list filtering.

## 4. Testing

- [ ] **Increase Test Coverage:**
    - [ ] Write feature tests for all the new service classes (`UserService`, `RoleService`, `TaxonomyService`, `TermService`).
    - [ ] Write Livewire tests for the new, smaller components (`UserForm`, `UserList`, `RoleForm`, `RoleList`, etc.) to ensure they function correctly in isolation.
    - [ ] Write tests for the container components to ensure they coordinate the child components correctly. 