# PHP Development Guide

This guide covers PHP development patterns and standards for the Laravel Club Rewards project.

## Strict Types

**All PHP files must start with strict types declaration:**

```php
<?php

declare(strict_types=1);

namespace App\Actions;
```

This ensures type safety and prevents implicit type conversions.

## Laravel Action Pattern

Business logic should be encapsulated in Action classes following the single responsibility principle.

### Action Class Structure

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Club;
use App\Enums\ClubRole;
use Illuminate\Support\Facades\DB;

final readonly class CreateClubAction
{
    public function __construct(
        private readonly ClubService $service
    ) {}

    public function handle(array $data): Club
    {
        return DB::transaction(function () use ($data): Club {
            // Implementation here
            return $this->service->create($data);
        });
    }
}
```

### Key Principles

- **Final readonly classes**: Prevents inheritance and mutation
- **Single `handle()` method**: Clear entry point for the action
- **Dependency injection**: Inject services in constructor
- **Database transactions**: Wrap database operations in transactions
- **Typed return values**: Always specify return types

### Transaction Handling

Actions should use database transactions to ensure data consistency:

```php
public function handle(array $data): Club
{
    return DB::transaction(function () use ($data): Club {
        $club = Club::create($data);
        $club->members()->attach(auth()->id(), ['role' => ClubRole::ADMIN]);
        return $club;
    });
}
```

**Important**: When calling actions within other actions, the transaction is not committed until all actions are
completed.

## Naming Conventions

### Classes and Files

- **Class names**: PascalCase (e.g., `CreateClubAction`)
- **File names**: PascalCase (e.g., `CreateClubAction.php`)
- **Namespaces**: Follow PSR-4 autoloading standards

### Methods and Variables

- **Methods**: camelCase (e.g., `handle()`, `createClub()`)
- **Variables**: camelCase (e.g., `clubData`, `userCount`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_RETRY_ATTEMPTS`)

### Database

- **Tables**: snake_case, plural (e.g., `clubs`, `club_members`)
- **Columns**: snake_case (e.g., `created_at`, `logo_path`)
- **Foreign keys**: `{table}_id` (e.g., `club_id`, `user_id`)

## Controllers

Controllers should be thin and delegate business logic to Actions.

### Controller Pattern

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Clubs\CreateClubAction;use App\Http\Requests\Clubs\ClubRequest;use Inertia\Inertia;use Inertia\Response;

final class ClubController extends Controller
{
    public function __construct(
        private readonly CreateClubAction $createClubAction
    ) {}

    public function store(ClubRequest $request): Response
    {
        $club = $this->createClubAction->handle($request->validated());

        return Inertia::render('Clubs/Show', [
            'club' => $club
        ]);
    }
}
```

### Best Practices

- Use dependency injection for Actions
- Return `Inertia\Response` for Inertia pages
- Validate input using Form Requests
- Keep controllers thin and focused

## Form Requests

Use Form Requests for validation and authorization.

### Form Request Pattern

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Club name is required.',
            'logo.image' => 'Logo must be an image file.',
        ];
    }
}
```

## Models

Models should define relationships, casts, and business logic.

### Model Pattern

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
```

### Best Practices

- Use proper type hints for relationships
- Define `$fillable` and `$casts` properties
- Use enums for status fields and roles
- Implement model factories for testing

## Enums

Use PHP enums for status fields and roles.

### Enum Example

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum ClubRole: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case GUEST = 'guest';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MEMBER => 'Member',
            self::GUEST => 'Guest',
        };
    }
}
```

## Error Handling

### Exception Handling

```php
try {
    $result = $this->action->handle($data);
} catch (ValidationException $e) {
    return back()->withErrors($e->errors());
} catch (AuthorizationException $e) {
    abort(403, $e->getMessage());
}
```

### Custom Exceptions

```php
<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class ClubNotFoundException extends Exception
{
    public function __construct(string $clubId)
    {
        parent::__construct("Club with ID {$clubId} not found.");
    }
}
```

## Code Quality

### Laravel Pint

Use Laravel Pint for code formatting:

```bash
# Format all PHP files
./vendor/bin/pint

# Check formatting without changes
./vendor/bin/pint --test
```

### Static Analysis

Consider using PHPStan or Psalm for static analysis:

```bash
# Install PHPStan
composer require --dev phpstan/phpstan

# Run analysis
./vendor/bin/phpstan analyse app/
```

## Testing

### Action Testing

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\Clubs\CreateClubAction;use App\Models\Club;use Tests\TestCase;

final class CreateClubActionTest extends TestCase
{
    public function test_it_creates_club_with_valid_data(): void
    {
        $action = new CreateClubAction();
        $data = ['name' => 'Test Club', 'description' => 'Test Description'];

        $club = $action->handle($data);

        $this->assertInstanceOf(Club::class, $club);
        $this->assertEquals('Test Club', $club->name);
    }
}
```

## Security

- Validate all user input using Form Requests
- Use Laravel's built-in CSRF protection
- Implement proper authorization using Policies
- Sanitize data before database operations
- Use Laravel's security features like rate limiting

## Performance

- Use Laravel's query optimization features
- Implement proper caching strategies
- Use eager loading to prevent N+1 queries
- Optimize database queries and indexes
- Use Laravel's queue system for background jobs
