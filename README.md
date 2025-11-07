# MangaDiyari

MangaDiyari is a Laravel-based application scaffold created for managing manga collections. The project ships with authentication screens, a simple dashboard, and a Bootstrap powered layout ready for further development.

## Getting started

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Install frontend dependencies (optional):

   ```bash
   npm install
   ```

3. Configure your environment by copying the example file:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Update the database credentials in `.env` to match your MySQL or PostgreSQL setup and run the migrations:

   ```bash
   php artisan migrate
   ```

5. Serve the application:

   ```bash
   php artisan serve
   ```

The default UI already includes registration and login forms. After logging in you will land on a simple dashboard view.
