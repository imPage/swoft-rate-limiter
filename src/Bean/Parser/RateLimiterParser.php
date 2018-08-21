<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/7/23
 * Time: 12:00
 */

namespace Swoft\RateLimiter\Bean\Parser;

use Swoft\RateLimiter\Bean\Collector\RateLimiterCollector;
use Swoft\Bean\Collector;
use Swoft\Bean\Parser\AbstractParser;

/**
 * Class RateLimiterParser
 * @package Swoft\RateLimiter\Bean\Parser
 */
class RateLimiterParser extends AbstractParser
{
    public function parser(
        string $className,
        $objectAnnotation = null,
        string $propertyName = '',
        string $methodName = '',
        $propertyValue = null
    )
    {
        Collector::$methodAnnotations[$className][$methodName][] = get_class($objectAnnotation);
        RateLimiterCollector::collect($className, $objectAnnotation, $propertyName, $methodName, $propertyValue);
    }
}