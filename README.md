# Slim-Module
A Module System for Slim Framework

## 使用方法
### 安装
```
composer require elfstack/slim-module
```

### 引入项目
```php
$app = new \Slim\App();
$container = $app->getContainer();

$manager = new \ElfStack\SlimModule\Manager($config);
$container['module'] = function () use ($manager) {
    return $manager;
};

$app->run();
```

### 创建并注册模块
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

，假设文件组织如下：
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

### 模块间调用服务
```php
$ret = $manager->call('auth.getAllUser', $args, ...);
```