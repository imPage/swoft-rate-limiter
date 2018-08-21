# 如何使用
## 安装组件
>composer require zcmzc/swoft-swoft-rate-limite
## 配置文件
在/config/properties/app 中添加配置:
```php
    'components' => [
        'custom' => [
            "Swoft\\RateLimiter\\",
        ],
    ],
    'rateLimiter' => [
        // QPS = limit / time
        'limit' => 10,
        'time'  => 1,
        // 参照 sunspikes\php-ratelimiter\config\config.php
        'config' => [
            'driver' => 'file',
            'file' => [
                'cache_dir' => \Swoft\App::getAlias('@runtime/rateLimiter'),
            ],
        ]
    ],
``` 
## 注解调用
新建控制器`App\Controllers\RateLimiterController`
```php
<?php

namespace App\Controllers;

use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\RateLimiter\Bean\Annotation\RateLimiter;

/**
 * @RateLimiter(limit=3, time=1)
 * @Controller("rate-limiter")
 */
class RateLimiterController
{
    /**
     * @RequestMapping()
     * @RateLimiter(limit=4, time=1)
     */
    public function test()
    {
        return ["QPS 4"];
    }

    /**
     * @RequestMapping()
     * @RateLimiter()
     */
    public function test2()
    {
        return ["QPS 3"];
    }
}
```
优先级为`方法注解`>`类注解`>`config/app`

没找到好用的限流器, 有空写一个