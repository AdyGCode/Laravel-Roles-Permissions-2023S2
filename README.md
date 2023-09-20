Create new laravel project

```shell
composer create laravel/laravel Laravel-Roles-Permissions
```

cd into new folder

```shell
cd Laravel-Roles-Permissions
```

Create the database and user for the example

open a MySQl/MariaDB database management system (eg HeidiDB, dbBeaver, phpMyAdmin) and use SQL to create the tables:

```mysql
CREATE DATABASE `laravel_roles_permissions` /*!40100 COLLATE 'utf8mb4_general_ci' */;
CREATE USER 'laravel_roles_permissions'@'localhost' IDENTIFIED BY 'Password1';
GRANT USAGE ON *.* TO 'laravel_roles_permissions'@'localhost';
GRANT EXECUTE, SELECT, SHOW VIEW, ALTER, ALTER ROUTINE, CREATE, CREATE ROUTINE, CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, EVENT, INDEX, INSERT, REFERENCES, TRIGGER, UPDATE, LOCK TABLES  ON `laravel\_roles\_permissions`.* TO 'laravel_roles_permissions'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

Edit the .env to include these settings
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_roles_permissions
DB_USERNAME=laravel_roles_permissions
DB_PASSWORD=Password1
```

add laravel Breeze
```shell
composer require laravel/breeze --dev
php artisan breeze:install blade --pest
```

Open a new terminal and run the tailwind compilation, and leave it running
```shell
npm install && npm update && npm run dev
```

Back in the first terminal...

Publish the breeze config and other items
```shell
php artisan vendor:publish --tag=laravel-errors
php artisan vendor:publish --tag=laravel-mail
php artisan vendor:publish --tag=laravel-notifications
php artisan vendor:publish --tag=laravel-pagination
php artisan vendor:publish --tag=sanctum-config
php artisan vendor:publish --tag=sanctum-migrations
```

You may want to also publish the following when working with Mac developers:

```shell
php artisan vendor:publish --tag=sail
php artisan vendor:publish --tag=sail-bin
php artisan vendor:publish --tag=sail-database
php artisan vendor:publish --tag=sail-docker
```

---

Install Spatie's Laravel Permissions package

```shell
composer require spatie/laravel-permission
```

Publish the config etc
```shell
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```
or use:
```shell
php artisan vendor:publish --tag=permission-config --tag=permission-migrations
```


Our example for this will have a products table, so create the model, migration and other items
```shell
php artisan make:model Product -ars
```

Edit the Product Model

```php
    protected $fillable = [
        'name', 'detail'
    ];
```

Edit the Product Migration
```php
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail');
            $table->timestamps();

            $table->index(['name'], 'product_name_index');
        });
    }
```

Run the migratrions

```shell
php artisan migrate:fresh --step --seed
```


---
Add the Roles capability to the User model

In the "use" section at the top of the User Model, ensure the HasRoles trait is added:

```php
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
```

Also add the HasRoles to the use line inside the class definition:

```php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
...
```

--- 
Middleware!

Time to add the middleware to the `app/Http/Kernel.php` file.

Locate the middleware section and the middleware gourps section.

Between these add the following route middleware entry:
```php

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];

```

---
Add the Web Routes to ensure logged in to use the 
