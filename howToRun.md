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

删除mix前端脚手架

```bash
# remove existing frontend scaffold
rm -rf package.json webpack.mix.js yarn.lock resources/assets
```

修改或创建vue.config.js

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

修改编译命令

```json
"scripts": {
  "serve": "vue-cli-service serve",
- "build": "vue-cli-service build",
+ "build": "rm -rf ../public/{js,css,img} && vue-cli-service build --no-clean",
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

### 权限

使用spatie/laravel-permission

```bash
composer require spatie/laravel-permission
```

You should publish [the migration](https://github.com/spatie/laravel-permission/blob/master/database/migrations/create_permission_tables.php.stub) and [the `config/permission.php` config file](https://github.com/spatie/laravel-permission/blob/master/config/permission.php) with:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

After the config and migration have been published and configured,  you can create the role- and permission-tables by running the  migrations:

```bash
php artisan migrate
# or
php artisan migrate --seed
```

First, add the `Spatie\Permission\Traits\HasRoles` trait to your `User` model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
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

修改 config/auth.php，将 `api guard` 的 `driver` 改为 `jwt`。

```
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

...

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

user 模型需要继承 `Tymon\JWTAuth\Contracts\JWTSubject` 接口，并实现接口的两个方法 `getJWTIdentifier()` 和 `getJWTCustomClaims()`。

*app\Models\User.php*

```php
<?php

namespace App\Models;

use Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

class User extends Authenticatable implements MustVerifyEmailContract, JWTSubject

.
.
.
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

新增api路由

```
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});
```

Then create the `AuthController`, either manually or by running the artisan command:

```
php artisan make:controller AuthController
```

Then add the following:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [];
        $credentials['name'] = $request->input('username');
        $credentials['password'] = $request->input('password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(JWTAuth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
```

处理jwt-auth报错 Method Illuminate\Auth\SessionGuard::factory does not exist

解决方案:

修改验证控制器,将auth()方法替换为 JWTAuth Facades方法,同时在构造方法中指定middleware 和 guard

```
$this->middleware('auth:api', ['except' => ['login']]);
```

现在在postman中测试可用，注意数据库中users表必须有数据。