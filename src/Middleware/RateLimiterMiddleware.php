<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/7/26
 * Time: 15:41
 */

namespace Swoft\RateLimiter\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\RateLimiter\Handler\RateLimiterHandler;
use Swoft\RateLimiter\Mapping\RateLimiterHandlerInterface;
use Swoft\Http\Message\Middleware\MiddlewareInterface;
use Swoft\Http\Message\Server\Request;
use Swoft\Http\Message\Server\Response;
use Swoft\Http\Server\AttributeEnum;

/**
 * @Bean()
 * Class RateLimiterMiddleware
 * @package Swoft\RateLimiter\Middleware
 */
class RateLimiterMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Swoft\Exception\Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /* @var RateLimiterHandler $RateLimiterHandler*/
        $RateLimiterHandler = App::getBean(RateLimiterHandler::class); // 因底层bug, 应注入RateLimiterHandlerInterface

        /* @var Request $request*/
        $parsedBody = $RateLimiterHandler->decrypt($request->raw());
        if ($parsedBody){
            $request = $request->withParsedBody($parsedBody);
        }

        /* @var Response $response*/
        $response = $handler->handle($request);

        /* @var Response $response*/
        $data = $response->getAttributes()[AttributeEnum::RESPONSE_ATTRIBUTE]; //  因底层bug, 应为 $response->getBody()->getContents()
        $RateLimiterData = $RateLimiterHandler->RateLimiter($data);

        return $response->withAttribute(AttributeEnum::RESPONSE_ATTRIBUTE, $RateLimiterData); //  因底层bug, 应为 $response->withContent(base64_encode($RateLimiterData))
    }
}