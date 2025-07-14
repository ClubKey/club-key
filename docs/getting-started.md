# Getting Started

Welcome to the Club Rewards project! This guide will help you set up and start developing with our modern Laravel 12 + Vue 3 + TypeScript stack.

## Prerequisites

- **PHP 8.2+** with required extensions
- **Node.js 18+** and npm
- **Composer** for PHP dependencies
- **Git** for version control

## Quick Start

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd club-rewards
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install Node.js dependencies**

   ```bash
   npm install
   ```

4. **Environment setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start development servers**

   ```bash
   # Start all services (Laravel, Queue, Vite)
   composer run dev

   # Or start individually:
   php artisan serve
   npm run dev
   php artisan queue:work
   ```

## Project Structure

```
club-rewards/
├── app/
│   ├── Actions/          # Business logic using Laravel Action pattern
│   ├── Http/
│   │   ├── Controllers/  # HTTP controllers returning Inertia responses
│   │   ├── Requests/     # Form request validation
│   │   └── Middleware/   # Custom middleware
│   ├── Models/           # Eloquent models with relationships
│   ├── Policies/         # Authorization policies
│   └── Enums/            # PHP enums for status/roles
├── resources/js/
│   ├── components/       # Vue 3 components
│   ├── pages/           # Inertia page components
│   ├── layouts/         # Vue layouts
│   └── types/           # TypeScript type definitions
├── database/
│   ├── migrations/      # Database schema
│   ├── seeders/         # Database seeders
│   └── factories/       # Model factories
└── routes/              # Application routes
```

## Key Technologies

- **Laravel 12**: Latest Laravel framework with strict types
- **Inertia.js**: SPA-like experience without API complexity
- **Vue 3 + TypeScript**: Frontend with Composition API
- **Tailwind CSS**: Utility-first CSS framework
- **shadcn/ui**: Modern UI component library

## Development Workflow

1. **Code Formatting**: Run `./vendor/bin/pint` for PHP and `npm run format` for frontend
2. **Testing**: Use `php artisan test` for Pest PHP tests
3. **Type Checking**: Run `npm run type-check` for TypeScript validation
4. **Linting**: Use `npm run lint` for ESLint checks

## Next Steps

- Read the [PHP Development Guide](php-development.md) for backend patterns
- Check the [Frontend Development Guide](frontend-development.md) for Vue/TypeScript
- Review [Inertia.js Patterns](inertia-patterns.md) for full-stack integration
- Explore [Testing Guidelines](testing.md) for quality assurance

## Need Help?

- Check the [Troubleshooting Guide](troubleshooting.md)
- Review [Common Patterns](common-patterns.md) for code examples
- Consult the [API Reference](api-reference.md) for detailed documentation
