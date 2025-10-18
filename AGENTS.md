# AGENTS.md

## Overview

This document summarizes the best practices, coding standards, and workflow rules for the Club Rewards project. All
contributors and agents should follow these guidelines to ensure code quality, maintainability, and consistency.

---

### 1. Code Formatting

- **PHP**: Use Laravel Pint as configured in `pint.json`.
- **TypeScript/Vue**: Use Prettier as configured in `package.json`.
- **ESLint**: Use for TypeScript/JavaScript linting.

---

### 2. Project Structure

- **Backend**:
    - `app/Actions/`: Business logic (Action pattern, final readonly classes, single `handle()` method).
    - `app/Http/Controllers/`: HTTP controllers, return Inertia responses, use dependency injection.
    - `app/Models/`: Eloquent models, relationships, `$fillable`, `$casts`, enums for status/roles.
    - `database/migrations/`: Schema changes, follow Laravel naming conventions.
    - `routes/`: Application routing.
    - `tests/`: Pest PHP tests.
- **Frontend**:
    - `resources/js/`: Vue 3 components, TypeScript code, Composition API.
    - `tests/components/`: Vitest component tests.

---

### 3. Coding Standards

- **PHP**:
    - All files must start with `declare(strict_types=1);`.
    - PascalCase for classes and file names, camelCase for methods/variables, snake_case for DB columns.
    - Use enums for status fields and roles.
    - Use Form Requests for validation.
    - Use Pest PHP for testing.
    - Use Laravel's exception handling and error pages.
    - Use Inertia's error handling on the frontend.
    - Log errors appropriately.
    - Use proper module resolution and path aliases.
    - Use proper query optimization and eager loading.
    - Implement caching where appropriate.
    - Optimize queries and indexes.
    - Use proper query optimization and eager loading.
    - Implement caching where appropriate.
- **Vue/TypeScript**:
    - Use `<script setup lang="ts">` and Composition API.
    - Define props and emits with TypeScript interfaces.
    - PascalCase for component names and files.
    - Use Tailwind CSS and shadcn/ui vue for styling.

---

### 4. Inertia.js Patterns

- Controllers return Inertia responses.
- Use Inertia plugin for Vue 3, Ziggy for routes.
- Page components in `resources/js/pages/`, use TypeScript and define props.
- Use Inertia's shared data for global state (auth, flash, etc.).
- Forms use Inertia helpers, handle validation errors, loading states, and CSRF.

---

### 5. Testing

- **Backend**: Pest PHP for expressive, isolated tests. Use factories for test data.
- **Frontend**: Vitest with Vue Test Utils. Mock Inertia dependencies.
- Write feature tests for all user flows.
- Test Actions, Controllers, Models, and Form Requests in isolation.

---

### 6. Database

- Use migrations for schema changes.
- Use current date/time for migration filenames.
- Use enums for status fields.
- Implement model factories for testing.

---

### 7. Security

- Validate all input with Form Requests.
- Use Laravel's CSRF protection.
- Use Policies for authorization.
- Sanitize data before DB operations.

---

### 8. Performance

- Use query optimization and eager loading.
- Implement caching where appropriate.
- Optimize queries and indexes.

---

### 9. Build & Tooling

- Use Vite for frontend builds, Laravel Vite plugin for integration.
- Use code splitting and TypeScript compilation.
- Use proper module resolution and path aliases.

---

### 10. Error Handling

- Use Laravel's exception handling and error pages.
- Use Inertia's error handling on the frontend.
- Log errors appropriately.

