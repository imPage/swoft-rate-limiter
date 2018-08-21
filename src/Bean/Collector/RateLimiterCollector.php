<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/7/23
 * Time: 11:55
 */

namespace Swoft\RateLimiter\Bean\Collector;

use Swoft\RateLimiter\Bean\Annotation\RateLimiter;
use Swoft\Bean\CollectorInterface;

/**
 * Class RateLimiterCollector
 * @package Swoft\RateLimiter\Bean\Collector
 */
class RateLimiterCollector implements CollectorInterface
{
    private static $collector = [];

    public static function collect(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    )
    {
        if ($objectAnnotation instanceof RateLimiter) {
            self::$collector[$className][$methodName ?: 'classAnnotation'] = $objectAnnotation;
        }
    }

    public static function getCollector()
    {
        return self::$collector;
    }

}