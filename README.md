# Expense tracker

This is a simple Expense Tracker built with Laravel, Livewire and Laravel Jetstream.

![Screenshot 2022-11-09 at 11-55-45 Laravel](https://user-images.githubusercontent.com/31145894/200824431-446aea23-d541-40e6-a92c-e888cdb6c9c3.png)

![Screenshot 2022-11-09 at 11-56-06 Laravel](https://user-images.githubusercontent.com/31145894/200824507-6b90f2dc-46bd-4c26-ab50-6a8c87afa670.png)

![Screenshot 2022-11-09 at 11-56-15 Laravel](https://user-images.githubusercontent.com/31145894/200824540-2cfb4129-ca89-420d-a926-53a781602d59.png)

![Screenshot 2022-11-09 at 11-56-24 Laravel](https://user-images.githubusercontent.com/31145894/200824567-eb9d2970-916d-449f-a387-d29f394d4c69.png)

## Requirements

The following tools are required in order to install and run the project locally.

-   PHP 8.1
-   Composer
-   PHP intl extension

## Installation

1. Clone the repo

```bash
# with https
git clone https://github.com/sissokho/expense-tracker.git
# with ssh
git clone git@github.com:sissokho/expense-tracker.git
```

2. Navigate into the project's root directory

```bash
cd expense-tracker
```

3. Copy .env.example file to .env

```bash
cp .env.example .env
```

4. Create a local database with a name of your choice

5. Edit .env file and set your database connection details

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xxx
DB_USERNAME=xxx
DB_PASSWORD=xxx
```

6. Install composer dependencies

```bash
composer install
```

7. Generate application key

```bash
php artisan key:generate
```

8. Run database migrations and seed the database

```bash
php artisan migrate --seed
```

9. Install npm dependencies

```bash
npm install
```

10. Run the Vite development server

```bash
npm run dev
```

11. Run the dev server

```bash
php artisan serve
```

It is ready! You can login into a test account using the following credentials:

-   **Username**: test@gmail.com
-   **Password**: password

### Github Authentication

To get Github authentication to work locally, you'll need to [register a new OAuth application on Github](https://github.com/settings/applications/new). Use `http://localhost:8000/oauth/callback` for the callback url. When you've created the app, fill in the ID and secret in your `.env` file in the env variables below. You should now be able to authenticate with Github.

```
GITHUB_ID=
GITHUB_SECRET=
GITHUB_URL=http://localhost:8000/oauth/callback
```

## Testing

You can run PHPUnit tests, PHPStan/Larastan static analysis and inspect the code for style errors without changing the files (with Laravel Pint):

```bash
composer test
```

However, you can run these tests separately.

-   Static analysis:

```bash
composer test:types
# or
./vendor/bin/phpstan analyse
```

-   PHPUnit tests:

```bash
composer test:unit
# or
php artisan test
```

-   Code inspection:

```bash
composer test:lint
# or
./vendor/bin/pint --test -v
```

To fix code style issues, run the following command:

```bash
composer lint
# or
./vendor/bin/pint -v
```

## License

The MIT License (MIT). Please see [License File](./LICENSE.md) for more information.
