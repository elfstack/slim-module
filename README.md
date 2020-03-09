# Slim-Module
A Module System for Slim Framework

## 目录
- [Slim-Module](#slim-module)
  - [目录](#%e7%9b%ae%e5%bd%95)
  - [安装](#%e5%ae%89%e8%a3%85)
  - [引入项目](#%e5%bc%95%e5%85%a5%e9%a1%b9%e7%9b%ae)
  - [ModuleMetaInterface](#modulemetainterface)
  - [MetaInfo (todo)](#metainfo-todo)
  - [注册模块](#%e6%b3%a8%e5%86%8c%e6%a8%a1%e5%9d%97)
  - [声明模块路由和服务 (todo)](#%e5%a3%b0%e6%98%8e%e6%a8%a1%e5%9d%97%e8%b7%af%e7%94%b1%e5%92%8c%e6%9c%8d%e5%8a%a1-todo)
  - [模块间调用服务](#%e6%a8%a1%e5%9d%97%e9%97%b4%e8%b0%83%e7%94%a8%e6%9c%8d%e5%8a%a1)

## 安装
```
composer require elfstack/slim-module
```

## 引入项目
```php
$app = new \Slim\App();
$container = $app->getContainer();

$manager = new \ElfStack\SlimModule\Manager($config);
$container['module'] = function () use ($manager) {
    return $manager;
};

$app->run();
```

## ModuleMetaInterface
```php
interface ModuleMetaInterface {
    // 返回模块元信息
    static public function info(): \ElfStack\SlimModule\MetaInfo;
}
```

## MetaInfo (todo)
- [ ] 更新此部分文档（config）

```php
$metainfo = [
    'namespace' => 'App\Modules\Auth',  // optional, default ''
    'api_prefix' => '',                 // optional, default ''
    'apis' => [                         // optional, default []
        'GET /users' => 'Controller:list',                  // recommended
        'GET /users' => 'App\Modules\Auth\Controller:list', // full namespace works even with `namespace` set
        'GET /users' => Controller::class.':list',          // slim style
    ],
    'services' => [                     // optional, default []
        'getAllUser' => 'Service:getAllUser',               // handler has the same style to routes
    ];
];
```

## 注册模块
假设要注册若个模块
```php
$manager->register([
    'Auth',
    'Contact',
    ...
]);
```

下面展示了所有支持的注册格式
```php
use ElfStack\SlimModules\Factory\MetaInfo;
$manager->register([
    'Auth',                         // 搜寻 (module_prefix)\Auth\Meta.php
    'authenticate' => 'Auth',       // 使用 `authenticate` 覆盖 Meta 中提供的默认模块短名称
    App\Modules\Auth\Meta::class,   // 完整指定实现 ModuleMetaInterface 的类
    new MetaInfo('auth', $metainfo),   // 直接传递 MetaInfo 对象
]);
```

## 声明模块路由和服务 (todo)
- [ ] 更新文档，新的路由和服务注册方法

假设文件组织如下：
```yaml
# any directory that collect modules
Modules:
    Auth:
        Meta.php
        ...
```

```php
// Meta.php
namespace App\Modules\Auth;
use ElfStack\SlimModules\Interfaces\ModuleMetaInterface;

class Meta implements ModuleMetaInterface
{
    static public function info()
    {
        $metainfo = ...;
        return new MetaInfo('auth', $metainfo);
    }
}
```

## 模块间调用服务
模块间调用服务（注册为 `services`）时，在 `Manager` 上使用 `call` 方法。
```php
// 调用
$ret = $manager->call('auth.getAllUser', $args, ...);
```