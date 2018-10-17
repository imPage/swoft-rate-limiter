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
use Swoft\RateLimiter\Exception\RateLimiterException;
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
    private $methodAnnotation;

    /**
     * @Around()
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function Around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        [$this->classAnnotation, $this->methodAnnotation] = $this->getAnnotations($proceedingJoinPoint);

        $callback = $this->getAnnotationProperty('callback');
        $limit = $this->getAnnotationProperty('limit');
        $capacity = $this->getAnnotationProperty('capacity');
        // TODO 后续可以改成自定义限流范围
        $path = str_replace('/', ':', request()->getUri()->getPath());

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
            throw new RateLimiterException("Request Rate Limite");
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
        return $this->methodAnnotation->$method()
            ?? $this->classAnnotation->$method()
            ?? App::getProperties()->get('rateLimiter')[$field];
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return RateLimiter[]
     */
    private function getAnnotations(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $collector = RateLimiterCollector::getCollector()[get_class($proceedingJoinPoint->getTarget())];
        return [
            $collector['classAnnotation'],
            $collector[$proceedingJoinPoint->getMethod()],
        ];
    }
}