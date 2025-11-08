# MangaDiyari

MangaDiyari is a Laravel 10 starter kit for building manga cataloguing and tracking tools. It ships with user authentication, a responsive Bootstrap powered layout, and a dashboard scaffold that you can extend with your own domain logic. The repository is intended to be a solid foundation for experiments or production-ready projects that need a traditional multi-page Laravel stack.

## Requirements

- PHP ^8.1 with the following PHP extensions enabled: `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, and `tokenizer`.
- Composer 2.5 or newer.
- A supported database (MySQL, MariaDB, or PostgreSQL).
- Node.js 18+ and npm 9+ for compiling the frontend assets with Vite.

## Installation

1. Install the PHP dependencies:

   ```bash
   composer install
   ```

2. Install the frontend dependencies (optional, only required if you will compile assets locally):

   ```bash
   npm install
   ```

3. Bootstrap your environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Update the `.env` configuration with your database credentials and any other services you plan to use.

5. Run the database migrations:

   ```bash
   php artisan migrate
   ```

6. (Optional) Add development data by creating seeders in `database/seeders` and running:

   ```bash
   php artisan db:seed
   ```

## Local development

- Start the Laravel HTTP server:

  ```bash
  php artisan serve
  ```

- In a separate terminal, launch the Vite development server to automatically rebuild frontend assets:

  ```bash
  npm run dev
  ```

Visit the URL shown by `php artisan serve` (defaults to <http://127.0.0.1:8000>) to sign up or log in. The starter views live under `resources/views` and can be customised to suit your project. Frontend assets are located in `resources/js` and `resources/css` and compiled to `public/` by Vite.

## Testing & quality tools

- Run the automated test suite:

  ```bash
  php artisan test
  ```

  Alternatively, you can call PHPUnit directly with `./vendor/bin/phpunit`.

- Apply the Laravel Pint code style fixer:

  ```bash
  ./vendor/bin/pint
  ```

## Deployment notes

When preparing for production:

1. Ensure that `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
2. Run database migrations on the target environment.
3. Build optimised frontend assets:

   ```bash
   npm run build
   ```

4. Cache your configuration and routes for faster boot times:

   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

Refer to the [official Laravel deployment documentation](https://laravel.com/docs/deployment) for additional guidance on queue workers, storage links, and server configuration.
