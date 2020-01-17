# 项目说明书



## 项目构建

### laravel初始化与

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

### frontend初始化

```
# mkdir frontend
# cd frontend
git clone https://github.com/PanJiaChen/vue-element-admin.git
mv vue-element-admin frontend
```

删除前端项目中的git相关文件

add .npmrc

```
touch .npmrc
```

add followings into .npmrc

```
sass_binary_site=https://npm.taobao.org/mirrors/node-sass/
registry=https://registry.npm.taobao.org
```

back to parent directory

将frontend中的gitignore移动到主项目的gitignore当中

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

