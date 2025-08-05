# Troubleshooting Guide

This guide helps you resolve common issues encountered while developing the Laravel Club Rewards project.

## Common Issues

### Backend Issues

#### 1. Strict Types Error

**Problem**: `Fatal error: Uncaught TypeError: Argument 1 passed to function() must be of type string, null given`

**Solution**: Ensure all PHP files start with `declare(strict_types=1);` and handle null values properly:

```php
// Instead of this:
function processName(string $name) {
    return strtoupper($name);
}

// Do this:
function processName(?string $name): string {
    return strtoupper($name ?? '');
}
```

#### 2. Action Class Not Found

**Problem**: `Class "App\Actions\SomeAction" not found`

**Solution**:

- Check that the file exists in `app/Actions/`
- Ensure the class name matches the file name (PascalCase)
- Verify the namespace is correct
- Run `composer dump-autoload`

#### 3. Database Transaction Issues

**Problem**: Actions within actions not committing properly

**Solution**: Ensure proper transaction handling:

```php
// Good: Nested transactions work correctly
public function handle(array $data): Club
{
    return DB::transaction(function () use ($data): Club {
        $club = Club::create($data);
        $this->otherAction->handle($club); // This will be part of the same transaction
        return $club;
    });
}

// Bad: Separate transactions
public function handle(array $data): Club
{
    $club = DB::transaction(function () use ($data): Club {
        return Club::create($data);
    });

    $this->otherAction->handle($club); // This is a separate transaction
    return $club;
}
```

#### 4. Inertia Response Issues

**Problem**: Page not rendering or data not available

**Solution**: Check controller responses:

```php
// Correct Inertia response
return Inertia::render('clubs/Index', [
    'clubs' => $clubs,
]);

// Wrong: Returning JSON
return response()->json(['clubs' => $clubs]);
```

#### 5. Form Request Validation

**Problem**: Validation not working or errors not showing

**Solution**: Ensure proper Form Request setup:

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Must return true/false
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
```

### Frontend Issues

#### 1. TypeScript Errors

**Problem**: Type errors in Vue components

**Solution**: Ensure proper TypeScript setup:

```vue
<script setup lang="ts">
// Define interfaces for props
interface Props {
  title: string
  count?: number
}

// Use withDefaults for optional props
const props = withDefaults(defineProps<Props>(), {
  count: 0
})

// Define emits interface
interface Emits {
  (e: 'update', value: string): void
}

const emit = defineEmits<Emits>()
</script>
```

#### 2. Inertia Form Issues

**Problem**: Forms not submitting or errors not showing

**Solution**: Use proper Inertia form handling:

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
})

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      // Handle success
    },
    onError: (errors) => {
      // Handle errors
    },
  })
}
</script>

<template>
  <form @submit.prevent="submit">
    <Input
      v-model="form.name"
      :error="form.errors.name"
    />
    <span v-if="form.errors.name" class="error">
      {{ form.errors.name }}
    </span>
  </form>
</template>
```

#### 3. Component Not Rendering

**Problem**: Vue component not showing or throwing errors

**Solution**: Check component structure:

```vue
<!-- Correct: Proper Vue 3 setup -->
<script setup lang="ts">
// Component logic here
</script>

<template>
  <div>
    <!-- Template content -->
  </div>
</template>

<style scoped>
/* Component styles */
</style>
```

#### 4. Route Not Found

**Problem**: `route() function not found` or route not working

**Solution**: Ensure Ziggy is properly configured:

```typescript
// In your app.ts
import { route } from 'ziggy-js'

// Use routes like this:
router.post(route('clubs.store'), formData)
```

### Development Environment Issues

#### 1. Vite Build Issues

**Problem**: Assets not loading or build errors

**Solution**: Check Vite configuration:

```bash
# Clear cache and rebuild
npm run build
rm -rf node_modules/.vite
npm run dev

# Check for TypeScript errors
npm run type-check
```

#### 2. Database Migration Issues

**Problem**: Migrations failing or database not synced

**Solution**: Reset and re-run migrations:

```bash
# Reset database
php artisan migrate:fresh --seed

# Or rollback and migrate
php artisan migrate:rollback
php artisan migrate
```

#### 3. Cache Issues

**Problem**: Changes not reflecting or old data showing

**Solution**: Clear all caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

#### 4. Permission Issues

**Problem**: File uploads or storage not working

**Solution**: Check file permissions:

```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
```

### Inertia Testing Issues

**Problem**: Inertia testing not working

**Solution**: Ensure that you are running `npm run build` before running tests. This will ensure the vite manifest is
generated, and up to date.

## Debugging Tools

### Laravel Debugging

#### 1. Enable Debug Mode

```env
# .env
APP_DEBUG=true
APP_ENV=local
```

#### 2. Use Laravel Telescope

```bash
# Install Telescope
composer require laravel/telescope --dev

# Publish and migrate
php artisan telescope:install
php artisan migrate
```

#### 3. Use Laravel Pail

```bash
# View logs in real-time
php artisan pail
```

### Frontend Debugging

#### 1. Vue DevTools

Install Vue DevTools browser extension for debugging Vue components.

#### 2. Browser Console

Check browser console for JavaScript errors and network requests.

#### 3. TypeScript Checking

```bash
# Check TypeScript errors
npm run type-check

# Watch for TypeScript errors
npm run type-check -- --watch
```

## Performance Issues

### 1. Slow Page Loads

**Problem**: Pages taking too long to load

**Solutions**:

- Use eager loading in controllers: `Club::with(['owner', 'members'])->get()`
- Implement pagination for large datasets
- Use database indexes for frequently queried columns
- Optimize images and assets

### 2. Memory Issues

**Problem**: High memory usage or out of memory errors

**Solutions**:

- Use chunking for large operations: `Club::chunk(100, function($clubs) { ... })`
- Implement proper garbage collection
- Use database transactions for large operations
- Monitor memory usage with tools like Laravel Telescope

### 3. N+1 Query Problems

**Problem**: Multiple database queries for related data

**Solutions**:

```php
// Bad: N+1 queries
$clubs = Club::all();
foreach ($clubs as $club) {
    echo $club->owner->name; // Additional query for each club
}

// Good: Eager loading
$clubs = Club::with('owner')->get();
foreach ($clubs as $club) {
    echo $club->owner->name; // No additional queries
}
```

## Common Error Messages

### PHP Errors

| Error                        | Cause                     | Solution                                                 |
|------------------------------|---------------------------|----------------------------------------------------------|
| `Class not found`            | Missing file or namespace | Check file exists and namespace is correct               |
| `Undefined variable`         | Variable not declared     | Use `declare(strict_types=1);` and declare all variables |
| `Call to undefined method`   | Method doesn't exist      | Check class methods and inheritance                      |
| `Database connection failed` | Database config issue     | Check `.env` database settings                           |

### TypeScript Errors

| Error                     | Cause                      | Solution                                      |
|---------------------------|----------------------------|-----------------------------------------------|
| `Property does not exist` | Missing interface property | Add property to interface or use optional `?` |
| `Type is not assignable`  | Type mismatch              | Check types and use proper type assertions    |
| `Cannot find module`      | Missing import or file     | Check import paths and file existence         |
| `Object is possibly null` | Null safety issue          | Use optional chaining `?.` or null checks     |

### Inertia Errors

| Error                 | Cause                   | Solution                                             |
|-----------------------|-------------------------|------------------------------------------------------|
| `Page not found`      | Missing Vue component   | Check component exists in `resources/js/pages/`      |
| `Props not available` | Missing controller data | Ensure controller returns data in Inertia response   |
| `Form not submitting` | Incorrect form setup    | Use `useForm` from Inertia and proper event handling |

## Getting Help

### 1. Check Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Vite logs
npm run dev

# Browser console
F12 -> Console tab
```

### 2. Use Laravel's Built-in Helpers

```php
// Debug variables
dd($variable);
dump($variable);

// Debug queries
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### 3. Community Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TypeScript Documentation](https://www.typescriptlang.org/docs/)

### 4. Project-Specific Help

- Check the [Common Patterns Guide](common-patterns.md) for examples
- Review the [PHP Development Guide](php-development.md) for backend patterns
- Consult the [Frontend Development Guide](frontend-development.md) for Vue/TypeScript patterns
- Look at existing code in the project for reference

## Prevention Tips

1. **Always use strict types** in PHP files
2. **Write tests** for new features
3. **Use TypeScript** for all frontend code
4. **Follow naming conventions** consistently
5. **Use the Action pattern** for business logic
6. **Implement proper error handling**
7. **Monitor performance** regularly
8. **Keep dependencies updated**

This troubleshooting guide should help you resolve most common issues in the Laravel Club Rewards project.
