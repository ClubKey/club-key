# Testing Guidelines

This guide covers testing strategies and best practices for the Laravel Club Rewards project using Pest PHP for backend testing and Vitest for frontend testing.

## Overview

Testing is a crucial part of our development workflow. We use Pest PHP for backend testing and Vitest for frontend testing with Vue Test Utils.

## Backend Testing with Pest PHP

### Test Structure

Tests are organized in the `tests/` directory:

```
tests/
├── Feature/           # Feature tests (end-to-end)
├── Unit/             # Unit tests (isolated components)
└── Pest.php          # Pest configuration
```

### Feature Tests

Feature tests test the application from the user's perspective, including HTTP requests and database interactions.

#### Basic Feature Test

```php
<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Club;

test('user can create a club', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/clubs', [
            'name' => 'Test Club',
            'description' => 'A test club',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('clubs', [
        'name' => 'Test Club',
        'description' => 'A test club',
    ]);
});
```

#### Inertia Feature Test

```php
<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Club;

test('user can view clubs index page', function () {
    $user = User::factory()->create();
    $clubs = Club::factory()->count(3)->create();

    $response = $this->actingAs($user)
        ->get('/clubs');

    $response->assertInertia(fn ($page) => $page
        ->component('Clubs/Index')
        ->has('clubs', 3)
        ->where('clubs.0.name', $clubs->first()->name)
    );
});
```

#### Form Validation Test

```php
<?php

declare(strict_types=1);

use App\Models\User;

test('club creation validates required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/clubs', []);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseMissing('clubs', []);
});
```

### Unit Tests

Unit tests test individual components in isolation.

#### Action Testing

```php
<?php

declare(strict_types=1);

use App\Actions\CreateClubAction;
use App\Models\User;
use App\Models\Club;

test('create club action creates club with valid data', function () {
    $user = User::factory()->create();
    $action = new CreateClubAction();

    $data = [
        'name' => 'Test Club',
        'description' => 'A test club',
        'owner_id' => $user->id,
    ];

    $club = $action->handle($data);

    expect($club)->toBeInstanceOf(Club::class)
        ->and($club->name)->toBe('Test Club')
        ->and($club->description)->toBe('A test club')
        ->and($club->owner_id)->toBe($user->id);
});
```

#### Model Testing

```php
<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;

test('club belongs to owner', function () {
    $owner = User::factory()->create();
    $club = Club::factory()->create(['owner_id' => $owner->id]);

    expect($club->owner)->toBeInstanceOf(User::class)
        ->and($club->owner->id)->toBe($owner->id);
});

test('club can have multiple members', function () {
    $club = Club::factory()->create();
    $members = User::factory()->count(3)->create();

    $club->members()->attach($members->pluck('id'));

    expect($club->members)->toHaveCount(3);
});
```

### Database Testing

#### Using Factories

```php
<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;

test('club factory creates valid club', function () {
    $club = Club::factory()->create();

    expect($club)->toBeInstanceOf(Club::class)
        ->and($club->name)->not->toBeEmpty()
        ->and($club->description)->not->toBeEmpty();
});

test('club factory with specific data', function () {
    $club = Club::factory()->create([
        'name' => 'Custom Club',
        'description' => 'Custom description',
    ]);

    expect($club->name)->toBe('Custom Club')
        ->and($club->description)->toBe('Custom description');
});
```

#### Database Assertions

```php
<?php

declare(strict_types=1);

use App\Models\Club;

test('club is stored in database', function () {
    $club = Club::factory()->create();

    $this->assertDatabaseHas('clubs', [
        'id' => $club->id,
        'name' => $club->name,
    ]);
});

test('club is soft deleted', function () {
    $club = Club::factory()->create();

    $club->delete();

    $this->assertSoftDeleted('clubs', [
        'id' => $club->id,
    ]);
});
```

## Frontend Testing with Vitest

### Setup and Configuration

Vitest is configured in your `vite.config.ts` and provides fast, native ESM testing for Vue components.

#### Vitest Configuration

```typescript
// vite.config.ts
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, './resources/js'),
    },
  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./resources/js/test/setup.ts'],
  },
})
```

#### Test Setup File

```typescript
// resources/js/test/setup.ts
import { vi } from 'vitest'
import { config } from '@vue/test-utils'

// Mock Inertia
vi.mock('@inertiajs/vue3', () => ({
  useForm: vi.fn(() => ({
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    processing: false,
    errors: {},
  })),
  usePage: vi.fn(() => ({
    props: {
      auth: { user: null },
      flash: {},
    },
  })),
  Head: vi.fn(),
  Link: vi.fn(),
  router: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
  },
}))

// Mock Ziggy
vi.mock('ziggy-js', () => ({
  route: vi.fn((name: string, params?: any) => `/${name}`),
}))

// Global test configuration
config.global.stubs = {
  'router-link': true,
  'router-view': true,
}
```

### Component Testing with Vue Test Utils

#### Advanced Testing Patterns

```typescript
// tests/components/ClubCard.test.ts
import { mount } from '@vue/test-utils'
import { describe, it, expect, beforeEach, vi } from 'vitest'
import ClubCard from '@/components/ClubCard.vue'

describe('ClubCard', () => {
  beforeEach(() => {
    // Reset mocks before each test
    vi.clearAllMocks()
  })

  it('displays club information', () => {
    const club = {
      id: 1,
      name: 'Test Club',
      description: 'Test Description',
      owner: {
        id: 1,
        name: 'John Doe',
      },
    }

    const wrapper = mount(ClubCard, {
      props: { club },
    })

    expect(wrapper.text()).toContain('Test Club')
    expect(wrapper.text()).toContain('Test Description')
    expect(wrapper.text()).toContain('John Doe')
  })

  it('emits click event when clicked', async () => {
    const club = { id: 1, name: 'Test Club' }
    const wrapper = mount(ClubCard, {
      props: { club },
    })

    await wrapper.trigger('click')

    expect(wrapper.emitted('click')).toBeTruthy()
    expect(wrapper.emitted('click')?.[0]).toEqual([club.id])
  })

  it('handles async operations', async () => {
    const mockApiCall = vi.fn().mockResolvedValue({ data: 'success' })
    const wrapper = mount(ClubCard, {
      props: { club: { id: 1, name: 'Test Club' } },
      global: {
        provide: {
          api: mockApiCall,
        },
      },
    })

    await wrapper.find('[data-testid="load-button"]').trigger('click')

    expect(mockApiCall).toHaveBeenCalledWith(1)
    expect(wrapper.text()).toContain('success')
  })
})
```

#### Testing with Composables

```typescript
// tests/composables/useAuth.test.ts
import { describe, it, expect, vi } from 'vitest'
import { useAuth } from '@/composables/useAuth'

// Mock Inertia
vi.mock('@inertiajs/vue3', () => ({
  usePage: vi.fn(() => ({
    props: {
      auth: {
        user: {
          id: 1,
          name: 'Test User',
          email: 'test@example.com',
        },
      },
    },
  })),
}))

describe('useAuth', () => {
  it('returns user information', () => {
    const { user, isAuthenticated } = useAuth()

    expect(user.value).toEqual({
      id: 1,
      name: 'Test User',
      email: 'test@example.com',
    })
    expect(isAuthenticated.value).toBe(true)
  })

  it('handles unauthenticated user', () => {
    vi.mocked(usePage).mockReturnValue({
      props: { auth: { user: null } },
    })

    const { user, isAuthenticated } = useAuth()

    expect(user.value).toBe(null)
    expect(isAuthenticated.value).toBe(false)
  })
})
```

#### Testing with Inertia Forms

```typescript
// tests/components/CreateClubForm.test.ts
import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { useForm } from '@inertiajs/vue3'
import CreateClubForm from '@/components/CreateClubForm.vue'

// Mock Inertia useForm
const mockForm = {
  name: '',
  description: '',
  post: vi.fn(),
  processing: false,
  errors: {},
}

vi.mock('@inertiajs/vue3', () => ({
  useForm: vi.fn(() => mockForm),
}))

describe('CreateClubForm', () => {
  it('submits form with correct data', async () => {
    const wrapper = mount(CreateClubForm)

    await wrapper.find('[data-testid="name-input"]').setValue('Test Club')
    await wrapper.find('[data-testid="description-input"]').setValue('Test Description')
    await wrapper.find('form').trigger('submit')

    expect(mockForm.post).toHaveBeenCalledWith('/clubs', {
      onSuccess: expect.any(Function),
      onError: expect.any(Function),
    })
  })

  it('shows validation errors', async () => {
    mockForm.errors = { name: 'Name is required' }
    const wrapper = mount(CreateClubForm)

    expect(wrapper.text()).toContain('Name is required')
  })
})
```

#### Basic Component Test

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
      owner: {
        id: 1,
        name: 'John Doe',
      },
    }

    const wrapper = mount(ClubCard, {
      props: { club },
    })

    expect(wrapper.text()).toContain('Test Club')
    expect(wrapper.text()).toContain('Test Description')
    expect(wrapper.text()).toContain('John Doe')
  })

  it('emits click event when clicked', async () => {
    const club = { id: 1, name: 'Test Club' }
    const wrapper = mount(ClubCard, {
      props: { club },
    })

    await wrapper.trigger('click')

    expect(wrapper.emitted('click')).toBeTruthy()
    expect(wrapper.emitted('click')?.[0]).toEqual([club.id])
  })
})
```

#### Form Component Test

```typescript
// tests/components/CreateClubForm.test.ts
import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import CreateClubForm from '@/components/CreateClubForm.vue'

describe('CreateClubForm', () => {
  it('submits form with correct data', async () => {
    const mockSubmit = vi.fn()
    const wrapper = mount(CreateClubForm, {
      props: {
        onSubmit: mockSubmit,
      },
    })

    await wrapper.find('[data-testid="name-input"]').setValue('Test Club')
    await wrapper.find('[data-testid="description-input"]').setValue('Test Description')
    await wrapper.find('form').trigger('submit')

    expect(mockSubmit).toHaveBeenCalledWith({
      name: 'Test Club',
      description: 'Test Description',
    })
  })

  it('shows validation errors', async () => {
    const wrapper = mount(CreateClubForm, {
      props: {
        errors: {
          name: 'Name is required',
        },
      },
    })

    expect(wrapper.text()).toContain('Name is required')
  })
})
```

#### Inertia Page Test

```typescript
// tests/pages/Clubs/Index.test.ts
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import ClubsIndex from '@/pages/Clubs/Index.vue'

describe('Clubs Index Page', () => {
  it('renders clubs list', () => {
    const clubs = [
      { id: 1, name: 'Club 1' },
      { id: 2, name: 'Club 2' },
    ]

    const wrapper = mount(ClubsIndex, {
      props: {
        clubs,
      },
    })

    expect(wrapper.findAll('[data-testid="club-item"]')).toHaveLength(2)
  })

  it('filters clubs by search', async () => {
    const wrapper = mount(ClubsIndex, {
      props: {
        clubs: [
          { id: 1, name: 'Football Club' },
          { id: 2, name: 'Basketball Club' },
        ],
      },
    })

    await wrapper.find('[data-testid="search-input"]').setValue('Football')

    expect(wrapper.text()).toContain('Football Club')
    expect(wrapper.text()).not.toContain('Basketball Club')
  })
})
```

### Testing Utilities

#### Custom Test Helpers

```typescript
// tests/helpers/index.ts
import { mount } from '@vue/test-utils'
import type { ComponentMountingOptions } from '@vue/test-utils'

export function mountWithInertia(
  component: any,
  options: ComponentMountingOptions<any> = {}
) {
  return mount(component, {
    global: {
      stubs: {
        'router-link': true,
        'router-view': true,
      },
    },
    ...options,
  })
}

export function createMockUser(overrides = {}) {
  return {
    id: 1,
    name: 'Test User',
    email: 'test@example.com',
    ...overrides,
  }
}

export function createMockClub(overrides = {}) {
  return {
    id: 1,
    name: 'Test Club',
    description: 'Test Description',
    owner: createMockUser(),
    ...overrides,
  }
}

export function createMockForm(overrides = {}) {
  return {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    processing: false,
    errors: {},
    ...overrides,
  }
}
```

## Test Configuration

### Pest Configuration

```php
<?php

// tests/Pest.php
uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Unit');

beforeEach(function () {
    // Setup for each test
});

afterEach(function () {
    // Cleanup after each test
});
```

### Test Case Setup

```php
<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup
    }
}
```

## Testing Best Practices

### 1. Test Naming

Use descriptive test names that explain the behavior being tested:

```php
// Good
test('user cannot access admin panel without admin role', function () {
    // Test implementation
});

// Bad
test('admin test', function () {
    // Test implementation
});
```

### 2. Arrange-Act-Assert Pattern

Structure tests using the AAA pattern:

```php
test('user can create a club', function () {
    // Arrange
    $user = User::factory()->create();
    $clubData = ['name' => 'Test Club'];

    // Act
    $response = $this->actingAs($user)
        ->post('/clubs', $clubData);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('clubs', $clubData);
});
```

### 3. Test Isolation

Each test should be independent and not rely on other tests:

```php
test('each test is independent', function () {
    // This test should not depend on the state of other tests
    $user = User::factory()->create();
    expect($user->id)->toBe(1); // Fresh database for each test
});
```

### 4. Use Factories

Use factories to create test data:

```php
test('club has members', function () {
    $club = Club::factory()->create();
    $members = User::factory()->count(3)->create();

    $club->members()->attach($members->pluck('id'));

    expect($club->members)->toHaveCount(3);
});
```

### 5. Test Edge Cases

Don't just test happy paths:

```php
test('club creation fails with invalid data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/clubs', ['name' => '']); // Empty name

    $response->assertSessionHasErrors(['name']);
});

test('user cannot access other user club', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $club = Club::factory()->create(['owner_id' => $user2->id]);

    $response = $this->actingAs($user1)
        ->get("/clubs/{$club->id}");

    $response->assertForbidden();
});
```

## Running Tests

### Command Line

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ClubTest.php

# Run tests with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel

# Run frontend tests
npm run test

# Run frontend tests in watch mode
npm run test:watch

# Run frontend tests with coverage
npm run test:coverage

# Run specific test file
npm run test tests/components/ClubCard.test.ts
```

### Continuous Integration

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'

    - name: Install dependencies
      run: composer install --prefer-dist --no-progress

    - name: Run tests
      run: php artisan test
```

## Coverage

### Backend Coverage

```bash
# Generate coverage report
php artisan test --coverage --min=80

# View coverage in browser
php artisan test --coverage --coverage-html=coverage
```

### Frontend Coverage

```bash
# Run tests with coverage
npm run test:coverage

# View coverage report
open coverage/lcov-report/index.html

# Run tests with UI
npm run test:ui
```

### Vitest Features

Vitest provides several powerful features for frontend testing:

#### Watch Mode

```bash
# Run tests in watch mode (default)
npm run test

# Run tests in watch mode with UI
npm run test:ui
```

#### Coverage Reports

```bash
# Generate coverage report
npm run test:coverage

# Coverage with specific reporters
npm run test -- --coverage --reporter=html --reporter=text
```

#### Test Filtering

```bash
# Run tests matching a pattern
npm run test -- --run ClubCard

# Run tests in a specific file
npm run test tests/components/ClubCard.test.ts

# Run tests with specific tags
npm run test -- --run --reporter=verbose
```

#### Environment Configuration

```typescript
// vite.config.ts
export default defineConfig({
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./resources/js/test/setup.ts'],
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      exclude: [
        'node_modules/',
        'resources/js/test/',
        '**/*.d.ts',
      ],
    },
  },
})
```

## Performance Testing

### Database Performance

```php
test('clubs index loads efficiently', function () {
    $clubs = Club::factory()->count(100)->create();

    $start = microtime(true);

    $response = $this->get('/clubs');

    $end = microtime(true);
    $duration = $end - $start;

    expect($duration)->toBeLessThan(1.0); // Should load in under 1 second
    $response->assertOk();
});
```

### Memory Usage

```php
test('large dataset does not cause memory issues', function () {
    $startMemory = memory_get_usage();

    $clubs = Club::factory()->count(1000)->create();

    $endMemory = memory_get_usage();
    $memoryUsed = $endMemory - $startMemory;

    expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024); // Less than 50MB
});
```

This comprehensive testing guide ensures your Laravel Club Rewards project maintains high quality and reliability through thorough testing practices.
