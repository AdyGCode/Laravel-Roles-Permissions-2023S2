Create new laravel project
```shell
composer create laravel/laravel Laravel-Roles-Permissions
```
cd into new folder and initialise git repository.
```shell
cd Laravel-Roles-Permissions
git init .
```
Create new .gitignore (or down)
Create the database and user for the example
open a MySQl/MariaDB database management system (eg HeidiDB, dbBeaver, phpMyAdmin) and use SQL to create the tables:
```mysql
CREATE DATABASE `laravel_roles_permissions` /*!40100 COLLATE 'utf8mb4_general_ci' */;
CREATE USER 'laravel_roles_permissions'@'localhost' IDENTIFIED BY 'Password1';
GRANT USAGE ON *.* TO 'laravel_roles_permissions'@'localhost';
GRANT EXECUTE, SELECT, SHOW VIEW, ALTER, ALTER ROUTINE, CREATE, CREATE ROUTINE,
    CREATE TEMPORARY TABLES, CREATE VIEW, DELETE, DROP, EVENT, INDEX, INSERT,
    REFERENCES, TRIGGER, UPDATE, LOCK TABLES
    ON `laravel\_roles\_permissions`.*
    TO 'laravel_roles_permissions'@'localhost'
    WITH GRANT OPTION;
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
## Laravel Pint
a way to make your code formatting to standards and more
install using:

```shell
composer require laravel/pint --dev
```

Head to https://www.jetbrains.com/help/phpstorm/using-laravel-pint.html to find out how to configure PhpStorm to use Pint!

To cover your whole application use:

```shell
.\vendor\bin\pint
```

Using Pint will make your code look more standardised, but it will not fix your coding errors!

## User Interface - Laravel Breeze (Blade based)

Add laravel Breeze

```shell
composer require laravel/breeze --dev
php artisan breeze:install blade --pest
```

Open a new terminal and run the tailwind compilation, and leave it running

```shell
npm install && npm update && npm run dev
```

Back in the first terminal... Publish the breeze config and other items
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

## Users

The users table is created by default within Laravel. We will need a user Controller, Seeder, ...

```shell
php artisan make:controller UserController --resource
php artisan make:controller UserSeeder
```

User Controller:

```php

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::latest()->paginate(5);

        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        /* TODO: Move the validation to StoreUserResponse */
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        /* TODO: Move the validation to UpdateUserResponse */

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
        ]);

        $input = $request->all();

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, ['password']);
        }

        $user = User::find($id);
        $user->update($input);

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}

```

Now for the seed data for the Users...

```php
public function run(): void
    {
        $seedUsers = [
            [
                'name' => 'Ad Ministrator',
                'email' => 'ad.ministrator@example.com',
                'password' => 'Password1',
                'roles' => ['admin', 'member', 'staff'],
            ],
            [
                'name' => 'STUDENT_GIVEN_NAME',
                'email' => 'STUDENT_GIVEN_NAME@example.com',
                'email_verified_at' => now(),
                'password' => 'Secret1',
                'roles' => ['admin', 'staff', 'member'],
            ],
            [
                'name' => 'Annie Wun',
                'email' => 'annie.wun@example.com',
                'password' => 'Password1',
                'roles' => ['member'],
            ],
            [
                'name' => 'Andy Mann',
                'email' => 'andy.mann@example.com',
                'password' => 'Password1',
                'roles' => ['staff', 'member'],
            ],
        ];
        foreach ($seedUsers as $newUser) {
            $newUser['password'] = Hash::make($newUser['password']);
            $user = User::create([
                'name' => $newUser['name'],
                'email' => $newUser['email'],
                'password' => $newUser['password'],
            ]);
        }
    }

```

## Roles

The Roles will need to be created...

```shell
php artisan make:controller RoleController --resource
php artisan make:controller RoleSeeder
```

Role Controller

```php
<?php
    
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
    
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
         $this->middleware('permission:role-create', ['only' => ['create','store']]);
         $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $roles = Role::orderBy('id','DESC')->paginate(5);
        return view('roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);
    
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
    
        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
    
        return view('roles.show',compact('role','rolePermissions'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        return view('roles.edit',compact('role','permission','rolePermissions'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
    
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
    
        $role->syncPermissions($request->input('permission'));
    
        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        DB::table("roles")->where('id',$id)->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }
}
```

## Products

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
            $table->text('detail')->nullable();
            $table->string('size', 6)->nullable();
            $table->string('colour', 64)->nullable();
            $table->timestamps();
            $table->index(['name'], 'product_name_index');
            $table->index(['colour'], 'product_colour_index');
        });
    }
```

The Product seeder will look like this (for the run method):

```php

    public function run(): void
    {
        $seedProducts = [
            [
                'name' => 'T-Shirt',
                'detail' => 'Unisex T-Shirt, Plain',
                'size' => 'XL',
                'colour' => 'black',
            ],
            [
                'name' => 'T-Shirt',
                'detail' => 'Unisex T-Shirt, Plain',
                'size' => 'S',
                'colour' => 'green',
            ],
            [
                'name' => 'T-Shirt',
                'detail' => 'Unisex T-Shirt, Plain',
                'size' => 'XL',
                'colour' => 'lime',
            ],
        ];
        foreach ($seedProducts as $seedProduct) {
            $product = Product::create($seedProduct);
        }
    }
```

Product Controller

```php
<?php
    
namespace App\Http\Controllers;
    
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
    
class ProductController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $products = Product::latest()->paginate(5);
        return view('products.index',compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('products.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        Product::create($request->all());
    
        return redirect()->route('products.index')
                        ->with('success','Product created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product): View
    {
        return view('products.show',compact('product'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product): View
    {
        return view('products.edit',compact('product'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
         request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
    
        $product->update($request->all());
    
        return redirect()->route('products.index')
                        ->with('success','Product updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
    
        return redirect()->route('products.index')
                        ->with('success','Product deleted successfully');
    }
}
```

### Database Seeder

The Database Seeder now reads:

```php
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
        ]);
    }
```

Run the migrations and Seed the database...

```shell
php artisan migrate:fresh --step --seed
```

---

## Add Role to user Model

Add the Role capability to the User by editing the User model ... In the "use"
section at the top of the User Model, ensure the HasRoles trait is added:

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

## Middleware!
Time to add the middleware to the `app/Http/Kernel.php` file.
Locate the middleware section and the middleware groups section.
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
Add the Web Routes to ensure logged in to use the users, products and other functions.
```php
Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
});
```
---
