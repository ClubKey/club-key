# Inertia.js Patterns Guide

This guide covers Inertia.js integration patterns and best practices for the Laravel Club Rewards project.

## Overview

Inertia.js allows us to build modern single-page applications using classic server-side routing and controllers, without
the complexity of building APIs. It bridges the gap between Laravel and Vue.js seamlessly.

## Controller Responses

Controllers should return Inertia responses for all page requests.

### Basic Controller Pattern

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetClubsAction;
use Inertia\Inertia;
use Inertia\Response;

final class ClubController extends Controller
{
    public function __construct(
        private readonly GetClubsAction $getClubsAction
    ) {}

    public function index(): Response
    {
        $clubs = $this->getClubsAction->handle();

        return Inertia::render('clubs/Index', [
            'clubs' => $clubs,
            'filters' => request()->only(['search', 'status']),
        ]);
    }

    public function show(Club $club): Response
    {
        return Inertia::render('clubs/Show', [
            'club' => $club->load(['owner', 'members']),
        ]);
    }
}
```

### Response with Shared Data

```php
public function create(): Response
{
    return Inertia::render('clubs/Create', [
        'clubs' => Club::all(),
        'users' => User::all(),
    ])->with([
        'flash' => [
            'message' => 'Club created successfully!',
        ],
    ]);
}
```

## Frontend Setup

The Inertia setup is configured in `resources/js/app.ts`:

```typescript
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)
    app.mount(el)
  },
})
```

### Ziggy Integration

Ziggy provides Laravel routes to JavaScript:

```typescript
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// Use Laravel routes in JavaScript
router.post(route('clubs.store'), formData)
```

## Page Components

Page components should be in `resources/js/pages/` and follow Vue 3 + TypeScript patterns.

### Basic Page Component

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import type { Club, User } from '@/types'

interface Props {
  clubs: Club[]
  users: User[]
  filters: {
    search?: string
    status?: string
  }
}

defineProps<Props>()
</script>

<template>
  <Head title="Clubs" />

  <div>
    <h1>Clubs</h1>

    <!-- Filters -->
    <div class="filters">
      <input v-model="filters.search" placeholder="Search clubs..." />
      <select v-model="filters.status">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>

    <!-- Clubs List -->
    <div class="clubs-grid">
      <ClubCard
        v-for="club in clubs"
        :key="club.id"
        :club="club"
      />
    </div>
  </div>
</template>
```

### Page with Forms

```vue
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'

interface Props {
  club?: Club
  users: User[]
}

const props = defineProps<Props>()

const form = useForm({
  name: props.club?.name ?? '',
  description: props.club?.description ?? '',
  owner_id: props.club?.owner_id ?? '',
  logo: null as File | null,
})

const submit = () => {
  if (props.club) {
    form.put(route('clubs.update', props.club.id))
  } else {
    form.post(route('clubs.store'))
  }
}
</script>

<template>
  <Head :title="club ? 'Edit Club' : 'Create Club'" />

  <form @submit.prevent="submit">
    <Input
      v-model="form.name"
      :error="form.errors.name"
      placeholder="Club name"
    />

    <Textarea
      v-model="form.description"
      :error="form.errors.description"
      placeholder="Club description"
    />

    <select v-model="form.owner_id" :error="form.errors.owner_id">
      <option value="">Select Owner</option>
      <option
        v-for="user in users"
        :key="user.id"
        :value="user.id"
      >
        {{ user.name }}
      </option>
    </select>

    <input
      type="file"
      @change="form.logo = $event.target.files[0]"
      accept="image/*"
    />

    <Button
      type="submit"
      :disabled="form.processing"
    >
      {{ club ? 'Update Club' : 'Create Club' }}
    </Button>
  </form>
</template>
```

## Shared Data

Use Inertia's shared data for global state that's available on every page.

### Middleware Configuration

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ] : null,
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'app' => [
                'name' => config('app.name'),
                'version' => config('app.version'),
            ],
        ]);
    }
}
```

### Using Shared Data

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Access shared data
const user = page.props.auth.user
const flashMessage = page.props.flash.message
const appName = page.props.app.name
</script>
```

## Form Handling

Forms should use Inertia for submissions with proper error handling.

### Form with Validation

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      // Handle success
      form.reset()
    },
    onError: (errors) => {
      // Handle validation errors
      console.log('Validation errors:', errors)
    },
    onFinish: () => {
      // Always called after request completes
      form.processing = false
    },
  })
}
</script>

<template>
  <form @submit.prevent="submit">
    <div>
      <label>Name</label>
      <Input
        v-model="form.name"
        :error="form.errors.name"
      />
      <span v-if="form.errors.name" class="error">
        {{ form.errors.name }}
      </span>
    </div>

    <div>
      <label>Email</label>
      <Input
        v-model="form.email"
        type="email"
        :error="form.errors.email"
      />
      <span v-if="form.errors.email" class="error">
        {{ form.errors.email }}
      </span>
    </div>

    <Button
      type="submit"
      :disabled="form.processing"
    >
      {{ form.processing ? 'Creating...' : 'Create User' }}
    </Button>
  </form>
</template>
```

### File Uploads

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  logo: null as File | null,
})

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.logo = target.files[0]
  }
}
</script>

<template>
  <form @submit.prevent="form.post(route('clubs.store'))">
    <Input v-model="form.name" />

    <input
      type="file"
      @change="handleFileChange"
      accept="image/*"
    />

    <Button type="submit" :disabled="form.processing">
      Create Club
    </Button>
  </form>
</template>
```

## Navigation

Use Inertia's router for client-side navigation.

### Programmatic Navigation

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const navigateToClub = (clubId: number) => {
  router.get(route('clubs.show', clubId))
}

const deleteClub = (clubId: number) => {
  router.delete(route('clubs.destroy', clubId), {
    onSuccess: () => {
      // Handle success
    },
  })
}
</script>
```

### Link Navigation

```vue
<template>
  <Link :href="route('clubs.show', club.id)">
    {{ club.name }}
  </Link>

  <Link
    :href="route('clubs.edit', club.id)"
    method="get"
    as="button"
  >
    Edit Club
  </Link>
</template>
```

## Error Handling

Implement proper error handling for both client and server errors.

### Global Error Handling

```typescript
// resources/js/app.ts
createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)

    // Global error handler
    app.config.errorHandler = (err, instance, info) => {
      console.error('Global error:', err, info)
    }

    app.mount(el)
  },
})
```

### Component Error Boundaries

```vue
<script setup lang="ts">
import { onErrorCaptured, ref } from 'vue'

const error = ref<Error | null>(null)

onErrorCaptured((err) => {
  error.value = err
  return false // Prevent error from propagating
})
</script>

<template>
  <div v-if="error">
    <p>Something went wrong: {{ error.message }}</p>
    <Button @click="error = null">Try Again</Button>
  </div>
  <slot v-else />
</template>
```

### Server Error Handling

```php
// In your controller
public function store(ClubRequest $request): Response
{
    try {
        $club = $this->createClubAction->handle($request->validated());

        return redirect()->route('clubs.show', $club)
            ->with('message', 'Club created successfully!');
    } catch (Exception $e) {
        return back()->withErrors(['error' => 'Failed to create club.']);
    }
}
```

## Progress Indicators

Inertia provides built-in progress indicators for better UX.

### Progress Bar

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { Progress } from '@/components/ui/progress'

const page = usePage()
</script>

<template>
  <div v-if="page.props.progress" class="progress-bar">
    <Progress :value="page.props.progress" />
  </div>
</template>
```

### Loading States

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
})

const submit = () => {
  form.post(route('users.store'))
}
</script>

<template>
  <form @submit.prevent="submit">
    <Input
      v-model="form.name"
      :disabled="form.processing"
    />

    <Button
      type="submit"
      :disabled="form.processing"
    >
      <span v-if="form.processing">Creating...</span>
      <span v-else>Create User</span>
    </Button>
  </form>
</template>
```

## Best Practices

### 1. Keep Controllers Thin

```php
// Good: Controller delegates to Action
public function store(ClubRequest $request): Response
{
    $club = $this->createClubAction->handle($request->validated());

    return redirect()->route('clubs.show', $club);
}

// Bad: Business logic in controller
public function store(ClubRequest $request): Response
{
    $data = $request->validated();
    $club = Club::create($data);
    // ... more business logic
}
```

### 2. Use TypeScript Interfaces

```typescript
// Define interfaces for page props
interface Props {
  clubs: Club[]
  filters: {
    search?: string
    status?: string
  }
}

defineProps<Props>()
```

### 3. Handle Loading States

```vue
<template>
  <div v-if="form.processing" class="loading-overlay">
    <Spinner />
  </div>
</template>
```

### 4. Implement Proper Error Handling

```vue
<template>
  <div v-if="form.errors.general" class="error-message">
    {{ form.errors.general }}
  </div>
</template>
```

### 5. Use Flash Messages

```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Show flash message
if (page.props.flash.message) {
  showToast(page.props.flash.message)
}
</script>
```

### 6. Optimize Performance

```typescript
// Use eager loading in controllers
$clubs = Club::with(['owner', 'members'])->get();

// Use pagination for large datasets
$clubs = Club::paginate(15);
```

## Common Patterns

### CRUD Operations

```vue
<!-- Index Page -->
<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const deleteItem = (id: number) => {
  router.delete(route('items.destroy', id))
}
</script>

<!-- Create/Edit Page -->
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  description: '',
})

const submit = () => {
  form.post(route('items.store'))
}
</script>
```

### Search and Filtering

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

const search = ref('')
const status = ref('')

const updateFilters = debounce(() => {
  router.get(route('items.index'), {
    search: search.value,
    status: status.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch([search, status], updateFilters)
</script>
```

This guide provides comprehensive patterns for working with Inertia.js in your Laravel Club Rewards project, ensuring a
smooth full-stack development experience.
