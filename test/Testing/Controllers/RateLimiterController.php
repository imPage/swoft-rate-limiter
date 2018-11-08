<?php

namespace SwoftTest\Testing\Controllers;

use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\RateLimiter\Bean\Annotation\RateLimiter;

/**
 * Class RateLimiterController
 * @Controller(prefix="/rate-limiter")
 * @RateLimiter(limit=1, capacity=1)
 */
class RateLimiterController
{
    /**
     * 限流回调
     * @RequestMapping(route="callback")
     * @RateLimiter(callback={RateLimiterController::class, "getCallback"})
     */
    public function callback()
    {
        return "success";
    }

    /**
     * @param $seconds // 下次Token 生成时间
     * @param \Swoft\Aop\ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     */
    public static function getCallback ($seconds, \Swoft\Aop\ProceedingJoinPoint $proceedingJoinPoint)
    {
        return "callback";
    }
}