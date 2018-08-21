<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/8/7
 * Time: 16:12
 */

namespace Swoft\RateLimiter\Aspect;

use Sunspikes\Ratelimit\Cache\Adapter\DesarrollaCacheAdapter;
use Sunspikes\Ratelimit\Cache\Factory\DesarrollaCacheFactory;
use Sunspikes\Ratelimit\RateLimiter;
use Sunspikes\Ratelimit\Throttle\Factory\ThrottlerFactory;
use Sunspikes\Ratelimit\Throttle\Hydrator\HydratorFactory;
use Sunspikes\Ratelimit\Throttle\Settings\ElasticWindowSettings;
use Swoft\Aop\ProceedingJoinPoint;
use Swoft\App;
use Swoft\Bean\Annotation\Around;
use Swoft\Bean\Annotation\Aspect;
use Swoft\Bean\Annotation\PointAnnotation;
use Swoft\RateLimiter\Bean\Annotation\RateLimiter as RateLimiterAnnotation;
use Swoft\RateLimiter\Bean\Collector\RateLimiterCollector;
use Swoft\RateLimiter\Handler\RateLimiterHandler;

/**
 * @Aspect()
 * @PointAnnotation(
 *      include={
 *          RateLimiterAnnotation::class
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
     */
    public function Around(ProceedingJoinPoint $proceedingJoinPoint)
    {
        list($this->classAnnotation, $this->annotation) = $this->getAnnotations($proceedingJoinPoint);

        $limit = $this->getAnnotationProperty('limit');
        $time = $this->getAnnotationProperty('time');

        /* @var RateLimiterHandler $rateLimiter */
        $rateLimiter = App::getBean(RateLimiterHandler::class);
        $result = $rateLimiter->access(request()->getUri()->getPath(), $limit, $time);

        if (! $result){
            return response()->withContent("rateLimiter");
        }
        return $proceedingJoinPoint->proceed();
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
     * @return RateLimiterAnnotation[]
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