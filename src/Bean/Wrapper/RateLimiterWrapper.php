<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/7/23
 * Time: 10:55
 */

namespace Swoft\RateLimiter\Bean\Wrapper;

use Swoft\RateLimiter\Bean\Annotation\RateLimiter;
use Swoft\Bean\Wrapper\AbstractWrapper;

/**
 * Class RateLimiterWrapper
 * @package Swoft\RateLimiter\Bean\Wrapper
 */
class RateLimiterWrapper extends AbstractWrapper
{
    /**
     * 解析哪些类注解
     * @var array
     */
    protected $classAnnotations = [
        RateLimiter::class,
    ];

    /**
     * 解析哪些方法注解
     * @var array
     */
    protected $methodAnnotations = [
        RateLimiter::class,
    ];

    /**
     * 是否解析类注解
     * @param array $annotations
     * @return bool
     */
    public function isParseClassAnnotations(array $annotations): bool
    {
        return true;
    }

    /**
     * 是否解析属性注解
     * @param array $annotations
     * @return bool
     */
    public function isParsePropertyAnnotations(array $annotations): bool
    {
        return false;
    }

    /**
     * 是否解析方法注解
     * @param array $annotations
     * @return bool
     */
    public function isParseMethodAnnotations(array $annotations): bool
    {
        return true;
    }

}