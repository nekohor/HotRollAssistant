# 项目说明书



## 项目构建

### laravel初始化

```bash
composer create-project --prefer-dist laravel/laravel HotRollAssistant

```

### git init

```bash
cd HotRollAssistant
git init
git add .
git commit -m "first commit"
git remote add origin https://github.com/nekohor/HotRollAssistant.git
git push -u origin master
```

### 配置参数env

数据库名称和密码等

### frontend初始化

clone vue-element-admin

```
# mkdir frontend
# cd frontend
git clone -b i18n git@github.com:PanJiaChen/vue-element-admin.git
mv vue-element-admin frontend
```

add .npmrc用于解决node-sass安装问题

```
touch .npmrc

# 文件中内容
sass_binary_site=https://npm.taobao.org/mirrors/node-sass/
registry=https://registry.npm.taobao.org
```

删除前端项目中的git相关文件，back to parent directory，将frontend中的gitignore移动到主项目的gitignore当中

```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log

# Editor directories and files
.idea
.vscode
*.suo
*.ntvs*
*.njsproj
*.sln
*.local

###### front end ######
frontend/.DS_Store
frontend/node_modules/
frontend/dist/
frontend/npm-debug.log*
frontend/yarn-debug.log*
frontend/yarn-error.log*
frontend/**/*.log

frontend/tests/**/coverage/
frontend/tests/e2e/reports
frontend/selenium-debug.log

# Editor directories and files
frontend/.idea
frontend/.vscode
frontend/*.suo
frontend/*.ntvs*
frontend/*.njsproj
frontend/*.sln
frontend/*.local

frontend/package-lock.json
frontend/yarn.lock
```

安装依赖

```bash
npm install --registry=https://registry.npm.taobao.org
```

## 改造

### 前端改造

删除后端文件夹中的mix前端脚手架

```bash
# remove existing frontend scaffold
rm -rf package.json webpack.mix.js yarn.lock resources/assets
```

在frontend中修改或创建vue.config.js

```js
module.exports = {
  // proxy API requests to Valet during development
  devServer: {
    proxy: 'http://laracon.test'
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: '../public',

  // modify the location of the generated HTML file.
  // make sure to do this only in production.
  indexPath: process.env.NODE_ENV === 'production'
    ? '../resources/views/index.blade.php'
    : 'index.html'
}
```

修改编译命令，增加前缀`rm -rf ../public/{js,css,img} && `

```json
"scripts": {
  "serve": "vue-cli-service serve",
- "build": "vue-cli-service build",
+ "build": "rm -rf ../public/{js,css,img} && vue-cli-service build --no-clean",
+ "build:stage": "rm -rf ../public/{js,css,img} && vue-cli-service build --mode staging",
  "lint": "vue-cli-service lint"
},
```

### 后端改造

新增单页面路由

**routes/web.php**

```php
<?php

Route::get('/{any}', 'SpaController@index')->where('any', '.*');
```

新增控制器

**app/Http/Controllers/SpaController.php**

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpaController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
```

## 后端安装相关插件

后端相关的模型、权限、资源等周边的自定义代码文件全部放在`App\Backend`下面。

在`App\Backend\Models`下自定义User模型，以代替框架原来的模型 App\User。

### 权限

使用spatie/laravel-permission，安装：

```bash
composer require spatie/laravel-permission
```

You should publish [the migration](https://github.com/spatie/laravel-permission/blob/master/database/migrations/create_permission_tables.php.stub) and [the `config/permission.php` config file](https://github.com/spatie/laravel-permission/blob/master/config/permission.php) with:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Create `UsersTableSeeder` seeder and add seed data into it. When adding seed data, you can use custom helper class eg. `class URP`.
```bash
php artisan make:seeder UsersTableSeeder
```

And add followings into `DatabaseSeeder::run()`.
```php
$this->call(UsersTableSeeder::class);
```

If you want to regard `name` as a unique index, modify the migration of `User`.
```php
// $table->string('name');
// $table->string('email')->unique();
$table->string('name')->unique();
$table->string('email');
```

After the config and migration have been published and configured,  you can create the role- and permission-tables by running the  migrations.
```bash
php artisan migrate
# or with seed data
php artisan migrate --seed
```

> 注意：对控制器的修改可以放在数据库迁移之后进行。

使用`spatie/laravel-permission`

Create three controllers: `UserController`, `RoleController`, `PermissionController` in`App\Backend\Models`

First, add the `Spatie\Permission\Traits\HasRoles` trait to your `User` model:

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
```

自定义`Role`和`Permission`这两个模型，并继承于`\Spatie\Permission\Models\Role`和`\Spatie\Permission\Models\Permission`。
```php
class Role extends \Spatie\Permission\Models\Role
{
    /**
     * Check whether current role is admin
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->name === Urp::ROLE_ADMIN;
    }
}

class Permission extends \Spatie\Permission\Models\Permission
{
    /**
     * To exclude permission management from the list
     *
     * @param $query
     * @return Builder
     */
    public function scopeAllowed($query)
    {
        return $query->where('name', '!=', Urp::PERMISSION_PERMISSION_MANAGE);
    }
}

```

And Create three resources `UserResource`, `RoleResource`, `PermissionResource`  in `App\Backend\Http\Resources`
```bash
php artisan make:resource UserResource
php artisan make:resource RoleResource
php artisan make:resource PermissionResource

# move the three resources to `App\Backend\Resources`
```

Create three controllers: `UserController`, `RoleController`, `PermissionController` in `App\Backend\Http\Controllers`
```bash
php artisan make:controller UserController
php artisan make:controller RoleController
php artisan make:controller PermissionController

# move the three controllers to `App\Backend\Controllers`
```

### JWT认证

使用tymon/jwt-auth

```bash
composer require tymon/jwt-auth

composer require tymon/jwt-auth:1.0.0-rc.5
# 或者
composer require tymon/jwt-auth ~1.0
```

publish the package tymon/jwt-auth config file `config/jwt.php`:

```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

Generate secret key

```
php artisan jwt:secret
```

修改 config/auth.php，如下所示。
```php
// guard to api
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

// driver to jwt
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],

// custom model with auth
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Backend\Models\User::class,
    ],

    // 'users' => [
    //     'driver' => 'database',
    //     'table' => 'users',
    // ],
],

```

User模型需要继承 `Tymon\JWTAuth\Contracts\JWTSubject` 接口，并实现接口的两个方法 `getJWTIdentifier()` 和 `getJWTCustomClaims()`，并设置`$guard_name = 'api'`
自建User模型于`App\Backend\Models`。
```php
<?php

namespace App\Backend\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Role[] $roles
 *
 * @method static User create(array $user)
 * @package App
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set permissions guard to API by default
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * @inheritdoc
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @inheritdoc
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        foreach ($this->roles  as $role) {
            if ($role->isAdmin()) {
                return true;
            }
        }

        return false;
    }
}
```

Then create the `AuthController`, either manually or by running the artisan command:
```
php artisan make:controller AuthController
```

> 处理jwt-auth报错 Method Illuminate\Auth\SessionGuard::factory does not exist
解决方案:修改验证控制器,将auth()方法替换为 JWTAuth Facades方法,同时在构造方法中指定middleware 和 guard
```php
$this->middleware('auth:api', ['except' => ['login']]);
```

若使用guard函数，则需在AuthController控制器中建立方法guard()
```php
/**
 * @return mixed
 */
private function guard()
{
    return Auth::guard();
}
```

若构造方法里有auth api中间件的设定，则在routes文件中不用设定，相关路由放在组内同一层次。
```php
Route::group([

    'middleware' => 'api',
    'prefix' => 'user'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});
```

或者auth的api路由设置如下：

```php
Route::group(['middleware' => 'api'], function () {
    Route::post('auth/login', '\App\Backend\Http\Controllers\AuthController@login');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('auth/user', '\App\Backend\Http\Controllers\AuthController@user');
        Route::post('auth/logout', '\App\Backend\Http\Controllers\AuthController@logout');
    });
});
```

现在在postman中测试可用，注意数据库中users表必须有数据。