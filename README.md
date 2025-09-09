# ClubKey
A modern Laravel 12 application for managing club memberships and rewards using Inertia.js with Vue 3 and TypeScript.

## 🚀 Quick Start

```bash
# Clone the repository
git clone https://github.com/cwray-tech/club-rewards.git
cd club-rewards

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development servers
composer run dev
```

Visit `http://localhost:8000` to see the application.

## 📚 Documentation

### Getting Started

- **[Getting Started Guide](docs/getting-started.md)** - Complete setup and installation guide
- **[Project Structure](docs/getting-started.md#project-structure)** - Overview of the codebase organization

### Development Guides

- **[PHP Development Guide](docs/php-development.md)** - Backend development patterns and standards
- **[Frontend Development Guide](docs/frontend-development.md)** - Vue 3 + TypeScript development patterns
- **[Inertia.js Patterns](docs/inertia-patterns.md)** - Full-stack integration patterns
- **[Testing Guidelines](docs/testing.md)** - Testing strategies and best practices

### Reference Documentation

- **[Common Patterns](docs/common-patterns.md)** - Practical examples and reusable patterns
- **[Troubleshooting Guide](docs/troubleshooting.md)** - Solutions for common issues

## 🛠 Tech Stack

- **Backend**: Laravel 12 with PHP 8.2+
- **Frontend**: Vue 3 + TypeScript + Inertia.js
- **Styling**: Tailwind CSS + shadcn/ui
- **Database**: MySQL/PostgreSQL
- **Testing**: Pest PHP + Vue Testing Library
- **Build Tool**: Vite

## 🏗 Architecture

This project follows modern Laravel patterns:

- **Laravel Action Pattern** - Business logic in dedicated Action classes
- **Strict Types** - All PHP files use `declare(strict_types=1);`
- **Inertia.js** - SPA-like experience without API complexity
- **TypeScript** - Full type safety for frontend code
- **Component-Based UI** - Reusable Vue components with shadcn/ui

## 📁 Project Structure

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
└── docs/                # Project documentation
```

## 🔧 Development Workflow

### Code Quality

```bash
# PHP formatting
./vendor/bin/pint

# Frontend formatting
npm run format

# TypeScript checking
npm run type-check

# Linting
npm run lint
```

### Testing

```bash
# Backend tests
php artisan test

# Frontend tests
npm run test

# Test coverage
php artisan test --coverage
```

### Database

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset database
php artisan migrate:fresh --seed
```

## 🚀 Deployment

### Prerequisites

- PHP 8.2+
- Node.js 18+
- MySQL/PostgreSQL
- Redis (optional, for caching)

### Production Setup

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Environment configuration
cp .env.example .env
# Edit .env with production values

# Database setup
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🤝 Contributing

1. Follow the coding standards outlined in the documentation
2. Write tests for new features
3. Use the Action pattern for business logic
4. Implement proper TypeScript types
5. Follow the established naming conventions

### Development Standards

- **PHP**: Use strict types, follow PSR-12, implement Actions
- **Vue**: Use Composition API with TypeScript, follow component patterns
- **Testing**: Write feature and unit tests with Pest PHP
- **Documentation**: Update relevant docs when adding features

## 📖 Key Features

- **Club Management** - Create and manage clubs with member roles
- **Visit Tracking** - Track member visits to club locations
- **Reward System** - Implement points-based reward levels
- **User Authentication** - Secure user registration and login
- **Role-Based Access** - Admin, member, and guest roles
- **File Uploads** - Club logo and image management
- **Real-time Updates** - Inertia.js for seamless UX

## 🔒 Security

- Laravel's built-in security features
- CSRF protection enabled
- Form request validation
- Authorization policies
- Secure file uploads
- SQL injection prevention

## 📈 Performance

- Database query optimization
- Eager loading for relationships
- Asset optimization with Vite
- Caching strategies
- Pagination for large datasets

## 🐛 Troubleshooting

Having issues? Check the [Troubleshooting Guide](docs/troubleshooting.md) for common solutions.

Common issues:

- [Strict Types Errors](docs/troubleshooting.md#strict-types-error)
- [Inertia Response Issues](docs/troubleshooting.md#inertia-response-issues)
- [TypeScript Errors](docs/troubleshooting.md#typescript-errors)
- [Database Issues](docs/troubleshooting.md#database-migration-issues)
- [Inertia Testing Issues](docs/troubleshooting.md#inertia-testing-issues)

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

- **Documentation**: Check the [docs/](docs/) directory
- **Issues**: Create an issue in the repository
- **Discussions**: Use GitHub Discussions for questions

---

Built with ❤️ using Laravel, Vue.js, and TypeScript.
