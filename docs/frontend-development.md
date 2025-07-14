# Frontend Development Guide

This guide covers Vue 3 + TypeScript development patterns and standards for the Laravel Club Rewards project.

## Vue 3 Composition API

Use Vue 3 Composition API with `<script setup>` syntax for all components.

### Basic Component Structure

```vue
<script setup lang="ts">
interface Props {
  title: string
  count?: number
}

interface Emits {
  (e: 'update', value: string): void
}

const props = withDefaults(defineProps<Props>(), {
  count: 0
})

const emit = defineEmits<Emits>()
</script>

<template>
  <div>
    <h1>{{ title }}</h1>
    <p>Count: {{ count }}</p>
    <button @click="emit('update', 'new value')">
      Update
    </button>
  </div>
</template>
```

### Key Principles

- **TypeScript first**: Always use TypeScript for type safety
- **Composition API**: Use `<script setup>` syntax
- **Props interfaces**: Define clear interfaces for component props
- **Emits interfaces**: Type your component events
- **Default values**: Use `withDefaults()` for optional props

## TypeScript Configuration

The project uses strict TypeScript configuration as defined in `tsconfig.json`:

```json
{
  "compilerOptions": {
    "strict": true,
    "target": "ESNext",
    "module": "ESNext",
    "moduleResolution": "bundler",
    "jsx": "preserve",
    "jsxImportSource": "vue"
  }
}
```

### Type Definitions

Create type definitions in `resources/js/types/`:

```typescript
// resources/js/types/index.d.ts
export interface User {
  id: number
  name: string
  email: string
  created_at: string
}

export interface Club {
  id: number
  name: string
  description?: string
  logo_path?: string
  owner: User
  members: User[]
  created_at: string
  updated_at: string
}

export interface PaginatedData<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}
```

## Component Naming and Structure

### File Naming

- **Component files**: PascalCase (e.g., `ClubCard.vue`, `UserProfile.vue`)
- **Utility files**: camelCase (e.g., `useAuth.ts`, `formatDate.ts`)
- **Type files**: camelCase (e.g., `types.ts`, `apiTypes.ts`)

### Component Organization

```
resources/js/
├── components/
│   ├── ui/              # shadcn/ui components
│   ├── forms/           # Form components
│   ├── layout/          # Layout components
│   └── features/        # Feature-specific components
├── pages/               # Inertia page components
├── layouts/             # Vue layouts
├── composables/         # Vue composables
└── types/               # TypeScript type definitions
```

## UI Components with shadcn/ui

The project uses shadcn/ui components with Tailwind CSS for styling.

### Using shadcn/ui Components

```vue
<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Create Club</CardTitle>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="handleSubmit">
        <Input v-model="form.name" placeholder="Club name" />
        <Button type="submit">Create Club</Button>
      </form>
    </CardContent>
  </Card>
</template>
```

### Customizing Components

Create custom variants for shadcn/ui components:

```typescript
// resources/js/components/ui/button/index.ts
import { cva } from 'class-variance-authority'

export const buttonVariants = cva(
  'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90',
        destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
        outline: 'border border-input hover:bg-accent hover:text-accent-foreground',
        secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        ghost: 'hover:bg-accent hover:text-accent-foreground',
        link: 'underline-offset-4 hover:underline text-primary',
      },
      size: {
        default: 'h-10 py-2 px-4',
        sm: 'h-9 px-3 rounded-md',
        lg: 'h-11 px-8 rounded-md',
        icon: 'h-10 w-10',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
)
```

## Inertia.js Integration

Components should work seamlessly with Inertia.js for full-stack integration.

### Page Components

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Club } from '@/types'

interface Props {
  club: Club
}

defineProps<Props>()
</script>

<template>
  <Head title="Club Details" />

  <div>
    <h1>{{ club.name }}</h1>
    <p>{{ club.description }}</p>
  </div>
</template>
```

### Form Handling

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  description: '',
  logo: null as File | null,
})

const submit = () => {
  form.post('/clubs', {
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
    <Input v-model="form.name" :error="form.errors.name" />
    <Textarea v-model="form.description" :error="form.errors.description" />
    <Input type="file" @change="form.logo = $event.target.files[0]" />
    <Button type="submit" :disabled="form.processing">
      Create Club
    </Button>
  </form>
</template>
```

## Composables

Create reusable composables for common functionality.

### Authentication Composable

```typescript
// resources/js/composables/useAuth.ts
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { User } from '@/types'

export function useAuth() {
  const page = usePage()

  const user = computed<User | null>(() => page.props.auth?.user || null)
  const isAuthenticated = computed(() => !!user.value)

  return {
    user,
    isAuthenticated,
  }
}
```

### API Composable

```typescript
// resources/js/composables/useApi.ts
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export function useApi() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  const get = async (url: string) => {
    loading.value = true
    error.value = null

    try {
      await router.get(url)
    } catch (err) {
      error.value = 'An error occurred'
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    get,
  }
}
```

## State Management

For simple state, use Vue's reactive system. For complex state, consider Pinia.

### Reactive State

```vue
<script setup lang="ts">
import { ref, reactive } from 'vue'

// Simple state
const count = ref(0)
const user = ref<User | null>(null)

// Complex state
const form = reactive({
  name: '',
  email: '',
  errors: {} as Record<string, string>,
})

// Computed properties
const isValid = computed(() => form.name.length > 0 && form.email.includes('@'))
</script>
```

### Pinia Store (if needed)

```typescript
// resources/js/stores/clubs.ts
import { defineStore } from 'pinia'
import type { Club } from '@/types'

export const useClubsStore = defineStore('clubs', () => {
  const clubs = ref<Club[]>([])
  const loading = ref(false)

  const fetchClubs = async () => {
    loading.value = true
    try {
      // API call
      clubs.value = await api.get('/clubs')
    } finally {
      loading.value = false
    }
  }

  return {
    clubs,
    loading,
    fetchClubs,
  }
})
```

## Error Handling

### Global Error Handling

```typescript
// resources/js/app.ts
import { createApp } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
    return pages[`./pages/${name}.vue`]
  },
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

## Performance Optimization

### Lazy Loading

```vue
<script setup lang="ts">
import { defineAsyncComponent } from 'vue'

const HeavyComponent = defineAsyncComponent(() => import('./HeavyComponent.vue'))
</script>
```

### Computed Properties

```vue
<script setup lang="ts">
import { computed } from 'vue'

const expensiveValue = computed(() => {
  // Expensive calculation
  return data.value.filter(item => item.active).map(item => item.name)
})
</script>
```

### Memoization

```typescript
import { useMemo } from 'vue'

const memoizedValue = useMemo(() => {
  return expensiveCalculation(data.value)
}, [data])
```

## Testing

### Component Testing

```typescript
// tests/components/ClubCard.test.ts
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import ClubCard from '@/components/ClubCard.vue'

describe('ClubCard', () => {
  it('displays club information', () => {
    const club = {
      id: 1,
      name: 'Test Club',
      description: 'Test Description',
    }

    const wrapper = mount(ClubCard, {
      props: { club },
    })

    expect(wrapper.text()).toContain('Test Club')
    expect(wrapper.text()).toContain('Test Description')
  })
})
```

## Code Quality

### ESLint Configuration

The project uses ESLint for TypeScript and Vue linting:

```bash
# Run linting
npm run lint

# Fix auto-fixable issues
npm run lint -- --fix
```

### Prettier Configuration

Use Prettier for code formatting:

```bash
# Format code
npm run format

# Check formatting
npm run format:check
```

### Type Checking

```bash
# Type check TypeScript files
npm run type-check
```

## Accessibility

### ARIA Attributes

```vue
<template>
  <button
    aria-label="Close modal"
    aria-describedby="modal-description"
    @click="close"
  >
    ×
  </button>
</template>
```

### Keyboard Navigation

```vue
<script setup lang="ts">
import { onMounted } from 'vue'

onMounted(() => {
  const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'Escape') {
      close()
    }
  }

  document.addEventListener('keydown', handleKeydown)

  onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown)
  })
})
</script>
```

## Best Practices

1. **Type everything**: Use TypeScript interfaces for all data structures
2. **Component composition**: Break down complex components into smaller ones
3. **Reactive data**: Use Vue's reactivity system effectively
4. **Performance**: Lazy load components and optimize re-renders
5. **Accessibility**: Implement proper ARIA attributes and keyboard navigation
6. **Testing**: Write tests for complex logic and user interactions
7. **Error boundaries**: Handle errors gracefully
8. **Code splitting**: Use dynamic imports for better performance
