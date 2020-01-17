# 项目说明书



## 项目构建

laravel初始化与git init

```bash
composer create-project --prefer-dist laravel/laravel HotRollAssistant

cd HotRollAssistant
git init
git add .
git commit -m "first commit"
git remote add origin https://github.com/nekohor/HotRollAssistant.git
git push -u origin master

```

frontend clone

```
mkdir frontend
cd frontend
git clone https://github.com/PanJiaChen/vue-element-admin.git

```

add .npmrc

```
touch .npmrc
```

add followings into .npmrc

```
sass_binary_site=https://npm.taobao.org/mirrors/node-sass/
registry=https://registry.npm.taobao.org
```


