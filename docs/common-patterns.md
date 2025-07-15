# Common Patterns Guide

This guide provides practical examples of common patterns used throughout the Laravel Club Rewards project.

## Backend Patterns

### CRUD Operations

#### Complete CRUD Controller

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateClubAction;
use App\Actions\UpdateClubAction;
use App\Actions\DeleteClubAction;
use App\Http\Requests\ClubRequest;
use App\Models\Club;
use Inertia\Inertia;
use Inertia\Response;

final class ClubController extends Controller
{
    public function __construct(
        private readonly CreateClubAction $createClubAction,
        private readonly UpdateClubAction $updateClubAction,
        private readonly DeleteClubAction $deleteClubAction,
    ) {}

    public function index(): Response
    {
        $clubs = Club::with(['owner', 'members'])
            ->when(request('search'), fn($query, $search) =>
                $query->where('name', 'like', "%{$search}%")
            )
            ->paginate(15);

        return Inertia::render('Clubs/Index', [
            'clubs' => $clubs,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Clubs/Create', [
            'users' => User::all(),
        ]);
    }

    public function store(ClubRequest $request): Response
    {
        $club = $this->createClubAction->handle($request->validated());

        return redirect()->route('clubs.show', $club)
            ->with('message', 'Club created successfully!');
    }

    public function show(Club $club): Response
    {
        $this->authorize('view', $club);

        return Inertia::render('Clubs/Show', [
            'club' => $club->load(['owner', 'members', 'rewards']),
        ]);
    }

    public function edit(Club $club): Response
    {
        $this->authorize('update', $club);

        return Inertia::render('Clubs/Edit', [
            'club' => $club,
            'users' => User::all(),
        ]);
    }

    public function update(ClubRequest $request, Club $club): Response
    {
        $this->authorize('update', $club);

        $this->updateClubAction->handle($club, $request->validated());

        return redirect()->route('clubs.show', $club)
            ->with('message', 'Club updated successfully!');
    }

    public function destroy(Club $club): Response
    {
        $this->authorize('delete', $club);

        $this->deleteClubAction->handle($club);

        return redirect()->route('clubs.index')
            ->with('message', 'Club deleted successfully!');
    }
}
```

#### Action Classes

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Club;
use App\Enums\ClubRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class CreateClubAction
{
    public function handle(array $data): Club
    {
        return DB::transaction(function () use ($data): Club {
            // Handle file upload
            if (isset($data['logo']) && $data['logo']) {
                $path = $data['logo']->store('clubs', 'public');
                $data['logo_path'] = $path;
            }
            unset($data['logo']);

            $club = Club::create($data);

            // Add creator as admin member
            $club->members()->attach(auth()->id(), [
                'role' => ClubRole::ADMIN->value,
            ]);

            return $club;
        });
    }
}

final readonly class UpdateClubAction
{
    public function handle(Club $club, array $data): Club
    {
        return DB::transaction(function () use ($club, $data): Club {
            // Handle file upload
            if (isset($data['logo']) && $data['logo']) {
                // Delete old logo
                if ($club->logo_path) {
                    Storage::disk('public')->delete($club->logo_path);
                }

                $path = $data['logo']->store('clubs', 'public');
                $data['logo_path'] = $path;
            }
            unset($data['logo']);

            $club->update($data);

            return $club;
        });
    }
}

final readonly class DeleteClubAction
{
    public function handle(Club $club): void
    {
        DB::transaction(function () use ($club): void {
            // Delete logo file
            if ($club->logo_path) {
                Storage::disk('public')->delete($club->logo_path);
            }

            // Delete related data
            $club->members()->detach();
            $club->rewards()->delete();

            $club->delete();
        });
    }
}
```

### Search and Filtering

#### Advanced Search Action

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Club;
use Illuminate\Database\Eloquent\Builder;

final readonly class SearchClubsAction
{
    public function handle(array $filters): Builder
    {
        return Club::query()
            ->with(['owner', 'members'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status) {
                $query->where('status', $status);
            })
            ->when($filters['owner_id'] ?? null, function (Builder $query, int $ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->when($filters['member_id'] ?? null, function (Builder $query, int $memberId) {
                $query->whereHas('members', function (Builder $q) use ($memberId) {
                    $q->where('user_id', $memberId);
                });
            })
            ->when($filters['sort'] ?? null, function (Builder $query, string $sort) {
                [$column, $direction] = explode(',', $sort);
                $query->orderBy($column, $direction);
            }, function (Builder $query) {
                $query->latest();
            });
    }
}
```

### Authorization Patterns

#### Policy Implementation

```php
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Club;
use App\Models\User;
use App\Enums\ClubRole;

final class ClubPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view clubs list
    }

    public function view(User $user, Club $club): bool
    {
        return $club->members()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can create clubs
    }

    public function update(User $user, Club $club): bool
    {
        return $club->members()
            ->where('user_id', $user->id)
            ->where('role', ClubRole::ADMIN->value)
            ->exists();
    }

    public function delete(User $user, Club $club): bool
    {
        return $club->owner_id === $user->id;
    }

    public function manageMembers(User $user, Club $club): bool
    {
        return $club->members()
            ->where('user_id', $user->id)
            ->where('role', ClubRole::ADMIN->value)
            ->exists();
    }
}
```

## Frontend Patterns

### Page Components

#### Index Page with Search and Pagination

```vue
<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { debounce } from 'lodash'
import type { Club, PaginatedData } from '@/types'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import ClubCard from '@/components/ClubCard.vue'
import Pagination from '@/components/Pagination.vue'

interface Props {
  clubs: PaginatedData<Club>
  filters: {
    search?: string
    status?: string
    sort?: string
  }
}

const props = defineProps<Props>()

const search = ref(props.filters.search || '')
const status = ref(props.filters.status || '')
const sort = ref(props.filters.sort || 'created_at,desc')

const updateFilters = debounce(() => {
  router.get(route('clubs.index'), {
    search: search.value,
    status: status.value,
    sort: sort.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 300)

watch([search, status, sort], updateFilters)

const createClub = () => {
  router.get(route('clubs.create'))
}
</script>

<template>
  <Head title="Clubs" />

  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Clubs</h1>
      <Button @click="createClub">Create Club</Button>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <Input
        v-model="search"
        placeholder="Search clubs..."
        class="w-full"
      />

      <Select v-model="status">
        <SelectTrigger>
          <SelectValue placeholder="All Status" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">All Status</SelectItem>
          <SelectItem value="active">Active</SelectItem>
          <SelectItem value="inactive">Inactive</SelectItem>
        </SelectContent>
      </Select>

      <Select v-model="sort">
        <SelectTrigger>
          <SelectValue placeholder="Sort by" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="created_at,desc">Newest First</SelectItem>
          <SelectItem value="created_at,asc">Oldest First</SelectItem>
          <SelectItem value="name,asc">Name A-Z</SelectItem>
          <SelectItem value="name,desc">Name Z-A</SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Clubs Grid -->
    <div v-if="clubs.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ClubCard
        v-for="club in clubs.data"
        :key="club.id"
        :club="club"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <p class="text-gray-500 text-lg">No clubs found.</p>
      <Button @click="createClub" class="mt-4">Create your first club</Button>
    </div>

    <!-- Pagination -->
    <Pagination
      v-if="clubs.last_page > 1"
      :current-page="clubs.current_page"
      :last-page="clubs.last_page"
      :total="clubs.total"
    />
  </div>
</template>
```

#### Form Component with Validation

```vue
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { Club, User } from '@/types'

interface Props {
  club?: Club
  users: User[]
}

const props = defineProps<Props>()

const form = useForm({
  name: props.club?.name ?? '',
  description: props.club?.description ?? '',
  owner_id: props.club?.owner_id?.toString() ?? '',
  logo: null as File | null,
})

const submit = () => {
  if (props.club) {
    form.put(route('clubs.update', props.club.id), {
      onSuccess: () => {
        // Success handling
      },
      onError: (errors) => {
        // Error handling
      },
    })
  } else {
    form.post(route('clubs.store'), {
      onSuccess: () => {
        // Success handling
      },
      onError: (errors) => {
        // Error handling
      },
    })
  }
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.logo = target.files[0]
  }
}
</script>

<template>
  <Head :title="club ? 'Edit Club' : 'Create Club'" />

  <Card class="max-w-2xl mx-auto">
    <CardHeader>
      <CardTitle>{{ club ? 'Edit Club' : 'Create Club' }}</CardTitle>
    </CardHeader>
    <CardContent>
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Name Field -->
        <div>
          <label for="name" class="block text-sm font-medium mb-2">Name</label>
          <Input
            id="name"
            v-model="form.name"
            :error="form.errors.name"
            placeholder="Enter club name"
            required
          />
          <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
            {{ form.errors.name }}
          </p>
        </div>

        <!-- Description Field -->
        <div>
          <label for="description" class="block text-sm font-medium mb-2">Description</label>
          <Textarea
            id="description"
            v-model="form.description"
            :error="form.errors.description"
            placeholder="Enter club description"
            rows="4"
          />
          <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Owner Field -->
        <div>
          <label for="owner" class="block text-sm font-medium mb-2">Owner</label>
          <Select v-model="form.owner_id">
            <SelectTrigger>
              <SelectValue placeholder="Select owner" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="user in users"
                :key="user.id"
                :value="user.id.toString()"
              >
                {{ user.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <p v-if="form.errors.owner_id" class="text-red-500 text-sm mt-1">
            {{ form.errors.owner_id }}
          </p>
        </div>

        <!-- Logo Field -->
        <div>
          <label for="logo" class="block text-sm font-medium mb-2">Logo</label>
          <Input
            id="logo"
            type="file"
            @change="handleFileChange"
            accept="image/*"
            :error="form.errors.logo"
          />
          <p v-if="form.errors.logo" class="text-red-500 text-sm mt-1">
            {{ form.errors.logo }}
          </p>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
          <Button
            type="button"
            variant="outline"
            @click="$inertia.visit(route('clubs.index'))"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            :disabled="form.processing"
          >
            <span v-if="form.processing">Saving...</span>
            <span v-else>{{ club ? 'Update Club' : 'Create Club' }}</span>
          </Button>
        </div>
      </form>
    </CardContent>
  </Card>
</template>
```

### Reusable Components

#### Data Table Component

```vue
<script setup lang="ts">
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

interface Column {
  key: string
  label: string
  sortable?: boolean
  render?: (value: any, row: any) => string
}

interface Props {
  data: any[]
  columns: Column[]
  sortable?: boolean
  searchable?: boolean
  actions?: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  edit: [row: any]
  delete: [row: any]
  sort: [column: string, direction: string]
}>()

const search = ref('')
const sortColumn = ref('')
const sortDirection = ref('asc')

const filteredData = computed(() => {
  let result = props.data

  if (search.value) {
    result = result.filter(row =>
      Object.values(row).some(value =>
        String(value).toLowerCase().includes(search.value.toLowerCase())
      )
    )
  }

  if (sortColumn.value && sortDirection.value) {
    result = result.sort((a, b) => {
      const aVal = a[sortColumn.value]
      const bVal = b[sortColumn.value]

      if (sortDirection.value === 'asc') {
        return aVal > bVal ? 1 : -1
      } else {
        return aVal < bVal ? 1 : -1
      }
    })
  }

  return result
})

const handleSort = (column: string) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }

  emit('sort', column, sortDirection.value)
}
</script>

<template>
  <div class="space-y-4">
    <!-- Search -->
    <div v-if="searchable" class="flex justify-between items-center">
      <Input
        v-model="search"
        placeholder="Search..."
        class="w-64"
      />
    </div>

    <!-- Table -->
    <div class="border rounded-lg">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead
              v-for="column in columns"
              :key="column.key"
              :class="{ 'cursor-pointer': column.sortable }"
              @click="column.sortable && handleSort(column.key)"
            >
              <div class="flex items-center space-x-2">
                <span>{{ column.label }}</span>
                <span v-if="column.sortable && sortColumn === column.key">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </span>
              </div>
            </TableHead>
            <TableHead v-if="actions">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow
            v-for="row in filteredData"
            :key="row.id"
          >
            <TableCell
              v-for="column in columns"
              :key="column.key"
            >
              <span v-if="column.render">
                {{ column.render(row[column.key], row) }}
              </span>
              <span v-else>
                {{ row[column.key] }}
              </span>
            </TableCell>
            <TableCell v-if="actions">
              <div class="flex space-x-2">
                <Button
                  size="sm"
                  variant="outline"
                  @click="emit('edit', row)"
                >
                  Edit
                </Button>
                <Button
                  size="sm"
                  variant="destructive"
                  @click="emit('delete', row)"
                >
                  Delete
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>
</template>
```

### Composables

#### API Composable

```typescript
// resources/js/composables/useApi.ts
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export function useApi() {
  const loading = ref(false)
  const error = ref<string | null>(null)

  const get = async (url: string, params = {}) => {
    loading.value = true
    error.value = null

    try {
      await router.get(url, params, {
        preserveState: true,
        replace: true,
      })
    } catch (err) {
      error.value = 'An error occurred while fetching data'
      console.error('API Error:', err)
    } finally {
      loading.value = false
    }
  }

  const post = async (url: string, data = {}) => {
    loading.value = true
    error.value = null

    try {
      await router.post(url, data)
    } catch (err) {
      error.value = 'An error occurred while saving data'
      console.error('API Error:', err)
    } finally {
      loading.value = false
    }
  }

  const put = async (url: string, data = {}) => {
    loading.value = true
    error.value = null

    try {
      await router.put(url, data)
    } catch (err) {
      error.value = 'An error occurred while updating data'
      console.error('API Error:', err)
    } finally {
      loading.value = false
    }
  }

  const del = async (url: string) => {
    loading.value = true
    error.value = null

    try {
      await router.delete(url)
    } catch (err) {
      error.value = 'An error occurred while deleting data'
      console.error('API Error:', err)
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    get,
    post,
    put,
    delete: del,
  }
}
```

#### Toast Composable

```typescript
// resources/js/composables/useToast.ts
import { ref } from 'vue'

interface Toast {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
  duration?: number
}

const toasts = ref<Toast[]>([])

export function useToast() {
  const addToast = (toast: Omit<Toast, 'id'>) => {
    const id = Math.random().toString(36).substr(2, 9)
    const newToast = { ...toast, id }

    toasts.value.push(newToast)

    // Auto remove after duration
    if (toast.duration !== 0) {
      setTimeout(() => {
        removeToast(id)
      }, toast.duration || 5000)
    }
  }

  const removeToast = (id: string) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const success = (message: string, duration?: number) => {
    addToast({ type: 'success', message, duration })
  }

  const error = (message: string, duration?: number) => {
    addToast({ type: 'error', message, duration })
  }

  const warning = (message: string, duration?: number) => {
    addToast({ type: 'warning', message, duration })
  }

  const info = (message: string, duration?: number) => {
    addToast({ type: 'info', message, duration })
  }

  return {
    toasts,
    addToast,
    removeToast,
    success,
    error,
    warning,
    info,
  }
}
```

## Error Handling Patterns

### Global Error Handler

```typescript
// resources/js/app.ts
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)

    // Global error handler
    app.config.errorHandler = (err, instance, info) => {
      console.error('Global error:', err, info)

      const { error } = useToast()
      error('An unexpected error occurred. Please try again.')
    }

    app.mount(el)
  },
})
```

### Form Error Handling

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

const { success, error } = useToast()

const form = useForm({
  name: '',
  email: '',
})

const submit = () => {
  form.post(route('users.store'), {
    onSuccess: () => {
      success('User created successfully!')
      form.reset()
    },
    onError: (errors) => {
      error('Please fix the errors below.')
    },
  })
}
</script>
```

## Performance Patterns

### Lazy Loading

```vue
<script setup lang="ts">
import { defineAsyncComponent } from 'vue'

const HeavyComponent = defineAsyncComponent(() => import('./HeavyComponent.vue'))
const ModalComponent = defineAsyncComponent(() => import('./ModalComponent.vue'))
</script>
```

### Virtual Scrolling

```vue
<script setup lang="ts">
import { VirtualList } from 'vue-virtual-scroll-list'

const items = ref(Array.from({ length: 10000 }, (_, i) => ({ id: i, name: `Item ${i}` })))
</script>

<template>
  <VirtualList
    :data-key="'id'"
    :data-sources="items"
    :data-component="ItemComponent"
    :estimate-size="50"
  />
</template>
```

These patterns provide a solid foundation for building maintainable and scalable features in your Laravel Club Rewards project.
