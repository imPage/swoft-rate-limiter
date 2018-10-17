# 如何使用
## 安装组件
>composer require zcmzc/swoft-swoft-rate-limiter
## 配置文件
在/config/properties/app 中添加配置:
```php
    'components' => [
        'custom' => [
            "Swoft\\RateLimiter\\",
        ],
    ],
    'rateLimiter' => [
        // 每秒产生Token 数量
        'limit' => 1,
        // 桶容量
        'capacity'  => 2,
        // 限流回调
        'callback' => function($seconds, \Swoft\Aop\ProceedingJoinPoint $proceedingJoinPoint){
            return response()->withContent($seconds);
        }
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
 * @RateLimiter(limit=1, capacity=3)
 * @Controller("rate-limiter")
 */
class RateLimiterController
{
    /**
     * @RequestMapping()
     * @RateLimiter()
     */
    public function test()
    {
        return ["QPS 1, 峰值3"];
    }

    /**
     * @RequestMapping()
     * @RateLimiter(limit=2, capacity=4)
     */
    public function test2()
    {
        return ["QPS 2, 峰值4"];
    }

    /**
     * @RequestMapping()
     * @RateLimiter(limit=1, capacity=1, callback={RateLimiterController::class, "getCallback"})
     */
    public function test3()
    {
        return ["QPS 1, 峰值1"];
    }

    /**
     * @param $seconds // 下次Token 生成时间
     * @param \Swoft\Aop\ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     */
    public static function getCallback ($seconds, \Swoft\Aop\ProceedingJoinPoint $proceedingJoinPoint)
    {
        usleep(intval($seconds * 1000 * 1000));
        return $proceedingJoinPoint->proceed();
    }
}
```
优先级为`方法注解`>`类注解`>`config/app`