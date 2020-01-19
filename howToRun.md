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
    : 'index.html',
}
```

修改编译命令，增加前缀`rm -rf ../public/{js,css,img} && `

```json
"scripts": {
  "serve": "vue-cli-service serve",
- "build": "vue-cli-service build",
+ "build:prod": "rm -rf ../public/{js,css,img} && vue-cli-service build --no-clean",
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

新增单页面控制器

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

#### 安装spatie/laravel-permission

使用spatie/laravel-permission，安装：
```bash
composer require spatie/laravel-permission
```

You should publish [the migration](https://github.com/spatie/laravel-permission/blob/master/database/migrations/create_permission_tables.php.stub) and [the `config/permission.php` config file](https://github.com/spatie/laravel-permission/blob/master/config/permission.php) with:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

#### 设置迁移
发布软件的过程中已经新增了create_permission_tables迁移。

之后修改User的相关迁移选择合适的用户登录索引。
If you want to regard `name` as a unique index, modify the migration of `User`.
```php
// $table->string('name');
// $table->string('email')->unique();
$table->string('name')->unique();
$table->string('email');
```

迁移设置中新增3个权限迁移，修改1个user迁移：
第一个新增迁移是`create_permission_tables`，用于新建相关的roles和permissions表，由`Spatie\Permission`在发布时自动完成新增。
第二个新增迁移是`add_simple_role_to_users`, 用于在users中简单增加一个role字段，这里我们用role来标识user的相关权限，用role1来对权限进行分组。
第三个新增迁移是`setup_roles_permissions`, 用于设定相关的roles和permissions参数，也可以放在数据填充中处理。
第四个是一个user迁移的修改，修改登录时所需的属性索引，是使用email还是name任君选择。


#### 新建数据填充
新建seeder。Create `UsersTableSeeder` seeder and add seed data into it. When adding seed data, you can use custom helper class eg. `class URP`.
```bash
php artisan make:seeder UsersTableSeeder
```
在UsersTableSeeder中编写：
```php
<?php

use App\Backend\Models\User;
use App\Backend\Permission\Urp;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@back.end',
            'password' => Hash::make('backend'),
        ]);
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@back.end',
            'password' => Hash::make('backend'),
        ]);
        $editor = User::create([
            'name' => 'Editor',
            'email' => 'editor@back.end',
            'password' => Hash::make('backend'),
        ]);
        $user = User::create([
            'name' => 'User',
            'email' => 'editor@back.end',
            'password' => Hash::make('backend'),
        ]);
        $visitor = User::create([
            'name' => 'Visitor',
            'email' => 'visitor@back.end',
            'password' => Hash::make('backend'),
        ]);

        $adminRole = Role::findByName(Urp::ROLE_ADMIN);
        $managerRole = Role::findByName(Urp::ROLE_MANAGER);
        $editorRole = Role::findByName(Urp::ROLE_EDITOR);
        $userRole = Role::findByName(Urp::ROLE_USER);
        $visitorRole = Role::findByName(Urp::ROLE_VISITOR);
        $admin->syncRoles($adminRole);
        $manager->syncRoles($managerRole);
        $editor->syncRoles($editorRole);
        $user->syncRoles($userRole);
        $visitor->syncRoles($visitorRole);
    }
}
```
And add followings into `DatabaseSeeder::run()`.
```php
$this->call(UsersTableSeeder::class);
```


#### 运行数据库迁移

After the config and migration have been published and configured,  you can create the role- and permission-tables by running the  migrations.
```bash
php artisan migrate
# or with seed data
php artisan migrate --seed
```

迁移过后你会发现model_has_permissions这个表示空的，因为我们没有给user这个model直接赋予权限，而是通过role的方式进行中间代理。

#### 自定义控制器

> 注意：对控制器的修改可以放在数据库迁移之后进行，seed文件编写需要放在迁移之前。

Create three controllers: `UserController`, `RoleController`, `PermissionController` in `App\Backend\Http\Controllers`
```bash
php artisan make:controller UserController
php artisan make:controller RoleController
php artisan make:controller PermissionController

# move the three controllers to `App\Backend\Controllers`
```


#### 自定义模型

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

#### 自定义资源

And Create three resources `UserResource`, `RoleResource`, `PermissionResource`  in `App\Backend\Http\Resources`
```bash
php artisan make:resource UserResource
php artisan make:resource RoleResource
php artisan make:resource PermissionResource

# move the three resources to `App\Backend\Resources`
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

User模型需要继承 `Tymon\JWTAuth\Contracts\JWTSubject` 接口，并实现接口的两个方法 `getJWTIdentifier()` 和 `getJWTCustomClaims()`
自建User模型于`App\Backend\Models`, 因为没有使用默认的User 所以需要添加`$guard_name = 'api'`
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


## 前端适配工作

### 调试服务器和mock的替换
在frontend中vue.config.js修改`devServer`
```js
module.exports = {
  // proxy API requests to Valet during development
  devServer: {
    proxy: 'http://localhost'
  },
}
```

在main.js中去除mockXHR
```js
// if (process.env.NODE_ENV === 'production') {
//   const { mockXHR } = require('../mock')
//   mockXHR()
// }
```

如果使用线上数据，mock文件夹中的相关mock api js移除即可。

目前怀疑实时api数据与mock数据之间的相关冲突，存在websocket连接的错误。

### api相关修改
在src/api中新增相关js文件, 若使用vue-element-admin集成方案，src/api中必须包含qiniu和remote-search

src/utils中替换request.js, 其中使用Bearer的token验证方式。
```js
// Request intercepter
service.interceptors.request.use(
  config => {
    const token = getToken()
    if (token) {
      config.headers['Authorization'] = 'Bearer ' + getToken() // Set JWT token
    }

    return config
  },
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)
```

### store相关修改

在src/store中新增相关js文件。

#### errorLog

保留errorLog.js的store文件，并在store/getters.js中新增errorLogs属性。
```js
const getters = {
  sidebar: state => state.app.sidebar,
  language: state => state.app.language,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  userId: state => state.user.id,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  introduction: state => state.user.introduction,
  roles: state => state.user.roles,
  permissions: state => state.user.permissions,
  permission_routes: state => state.permission.routes,
  addRoutes: state => state.permission.addRoutes,
  errorLogs: state => state.errorLog.logs
}
```

#### generateRoutes

因为在store/module/permission.js中generateRoutes需要两个参数，因此需修改所有有关这个函数的调用方式。
```js
const actions = {
  generateRoutes({ commit }, { roles, permissions }) {
    return new Promise(resolve => {
      let accessedRoutes
      console.log(roles)
      if (roles.includes('admin')) {
        accessedRoutes = asyncRoutes
      } else {
        accessedRoutes = filterAsyncRoutes(asyncRoutes, roles, permissions)
      }

      commit('SET_ROUTES', accessedRoutes)
      resolve(accessedRoutes)
    })
  }
}

```

modify src/permission.js, add permission in dispatch parameter
```js
const accessRoutes = await store.dispatch('permission/generateRoutes', { roles, permissions })

router.addRoutes(accessRoutes)

next({ ...to, replace: true })
```
也可以通过Promise的then实现
```js
store.dispatch('permission/generateRoutes', { roles, permissions }).then(response => {
  // dynamically add accessible routes
  router.addRoutes(response);

  // hack method to ensure that addRoutes is complete
  // set the replace: true, so the navigation will not leave a history record
  next({ ...to, replace: true });
});
```

### store/index

注意store/index中高的相关变化，如camelCase



## excel编写的规则录入

在env配置和config/database配置中新增db_data的数据库配置, 也可使用默认数据库。




## 新增Oracle数据库的medoo

Install the package
```bash
composer require repat/laravel-medoo
```

在env配置和config/database配置中新增mes和qms的数据库配置

实现自己的ServiceProvider
Add the Service Provider to the providers array in app/config/app.php
```php
repat\LaravelMedoo\MedooServiceProvider::class,
```

实现自己的Facades  如：DBMES  DBQMS等
Add alias in app/config/app.php
```php
'DBMES' => repat\LaravelMedoo\MedooFacade::class,
'DBQMS' => repat\LaravelMedoo\MedooFacade::class,
```
