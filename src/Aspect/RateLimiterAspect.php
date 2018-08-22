<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/8/7
 * Time: 16:12
 */

namespace Swoft\RateLimiter\Aspect;

use Swoft\Aop\ProceedingJoinPoint;
use Swoft\App;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\PointAnnotation;
use Swoft\RateLimiter\Bean\Annotation\RateLimiter;
use Swoft\RateLimiter\Bean\Collector\RateLimiterCollector;
use Swoft\RateLimiter\Handler\RateLimiterHandler;

/**
 * @Aspect()
 * @PointAnnotation(
 *      include={
 *          RateLimiter::class
 *      }
 *  )
 * Class RateLimiterAspect
 * @package Swoft\RateLimiter\Aspect
 */
class RateLimiterAspect
{
    private $classAnnotation;
    private $annotation;

    /**
     * @Around()
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \bandwidthThrottle\tokenBucket\storage\StorageException
     */
    public function Around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        list($this->classAnnotation, $this->annotation) = $this->getAnnotations($proceedingJoinPoint);

        $callback = $this->getAnnotationProperty('callback');
        $limit = $this->getAnnotationProperty('limit');
        $capacity = $this->getAnnotationProperty('capacity');
        $path = str_replace('/', '_', request()->getUri()->getPath());

        /* @var RateLimiterHandler $rateLimiter */
        $rateLimiter = App::getBean(RateLimiterHandler::class);

        $bucket = $rateLimiter->getBucket($path);
        if (! $bucket){
            $bucket = $rateLimiter->build($path, $limit, $capacity);
        }

        if ($bucket->consume(1,$seconds)){
            return $proceedingJoinPoint->proceed();
        }
        if (! $callback || ! is_callable($callback)){
            return response()->withContent("limit {$seconds}");
        }
        return call_user_func($callback, $seconds, $proceedingJoinPoint);
    }

    /**
     * 根据优先级取注解属性
     * @param string $field
     * @return mixed
     */
    public function getAnnotationProperty(string $field)
    {
        $method = 'get'.ucwords($field);
        return $this->annotation->$method()
            ?? $this->classAnnotation->$method()
            ?? App::getProperties()->get('rateLimiter')[$field];
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return RateLimiter[]
     */
    private function getAnnotations(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $collector = RateLimiterCollector::getCollector();
        return [
            $collector[get_class($proceedingJoinPoint->getTarget())]['classAnnotation'],
            $collector[get_class($proceedingJoinPoint->getTarget())][$proceedingJoinPoint->getMethod()],
        ];
    }
}