<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/8/21
 * Time: 10:07
 */

namespace Swoft\RateLimiter\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunspikes\Ratelimit\Cache\Adapter\DesarrollaCacheAdapter;
use Sunspikes\Ratelimit\Cache\Factory\DesarrollaCacheFactory;
use Sunspikes\Ratelimit\RateLimiter;
use Sunspikes\Ratelimit\Throttle\Factory\ThrottlerFactory;
use Sunspikes\Ratelimit\Throttle\Hydrator\HydratorFactory;
use Sunspikes\Ratelimit\Throttle\Settings\ElasticWindowSettings;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\RateLimiter\Handler\RateLimiterHandler;

/**
 * @Bean()
 * Class RateLimiterMiddleware
 * @package App\Middlewares
 */
class RateLimiterMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $config = App::getProperties()->get('rateLimiter');

        /* @var RateLimiterHandler $rateLimiter */
        $rateLimiter = App::getBean(RateLimiterHandler::class);
        $result = $rateLimiter->access($request->getUri()->getPath(), $config['limit'], $config['time']);

        if (! $result){
            return response()->withContent("rateLimiter");
        }

        return $handler->handle($request);
    }
}