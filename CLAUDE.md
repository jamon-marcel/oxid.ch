# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Architecture Overview

This is a Laravel 11 application with a Vue.js 2 frontend for the admin panel. The application manages an architecture portfolio website with both public frontend and admin backend interfaces.

### Key Structure
- **Frontend**: Public-facing website with project galleries, discourse articles, team info, and contact forms
- **Backend**: Vue.js 2 admin panel for content management (accessed via `/admin`)
- **Models**: Core entities include Project, Discourse, Team, Profile, Job, News, and various image/document models
- **Image Management**: Heavy use of image processing with cropping, caching, and multiple formats using Intervention Image
- **API**: RESTful API endpoints in `routes/api.php` for admin panel data management

### Key Technologies
- Laravel 11 with PHP 8.2+
- Vue.js 2 with Vue Router and Vuex for admin panel
- Laravel Mix for asset compilation
- Bootstrap 4 for admin styling
- JWT authentication for API
- Algolia Scout for search functionality
- Image processing and caching system

## Development Commands

### Frontend Assets
```bash
# Development build with file watching
npm run dev
# or
npm run watch

# Production build
npm run prod

# Development with hot reload
npm run hot
```

### Laravel Commands
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear application cache
php artisan cache:clear

# Generate application key
php artisan key:generate

# Queue worker (if using queues)
php artisan queue:work

# Custom command to clear images
php artisan app:clear-images
```

### Testing
```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=Feature
./vendor/bin/phpunit --testsuite=Unit

# Alternative using artisan
php artisan test
```

### Code Quality
```bash
# Laravel Pint for code formatting
./vendor/bin/pint

# Check code style without fixing
./vendor/bin/pint --test
```

## Key Configuration

### Environment Setup
- Copy `.env.example` to `.env` and configure database, Algolia keys, and JWT secret
- Image cache configuration in `config/image-cache.php`
- Custom content settings in `config/content.php`

### Database
- Uses MySQL/MariaDB
- Extensive migration history with image and document relationships
- Seeders available for Discourse, Projects, and Team data

### Image System
- Custom image filtering and caching via `marceli-to/image-cache` package
- Image templates defined in `app/Filters/Image/Template/`
- Cropping coordinates stored in database for responsive images
- Public uploads stored in `storage/app/public/uploads/`

### Admin Panel
- Single-page Vue.js application served at `/admin`
- API-driven with JWT authentication
- Drag-and-drop file uploads using vue2-dropzone
- Image cropping with vue-advanced-cropper
- TinyMCE integration for rich text editing

### Frontend Structure
- Blade templates in `resources/views/frontend/`
- SCSS organized by components and views
- Separate builds for main site and "busu" variant
- Responsive image system with multiple breakpoints

## Important Notes

- Admin routes catch-all in `web.php` serves the Vue SPA
- API routes are prefixed and return JSON for admin consumption
- Image processing is CPU-intensive; consider queue usage in production
- The application supports multiple themes/variants (evident from "busu" assets)
- Search functionality requires Algolia configuration
- JWT tokens used for admin authentication (not Laravel's default session auth)