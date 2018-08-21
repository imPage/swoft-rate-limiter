<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/8/21
 * Time: 16:02
 */

namespace Swoft\RateLimiter\Handler;

use Sunspikes\Ratelimit\Cache\Adapter\DesarrollaCacheAdapter;
use Sunspikes\Ratelimit\Cache\Factory\DesarrollaCacheFactory;
use Sunspikes\Ratelimit\RateLimiter;
use Sunspikes\Ratelimit\Throttle\Factory\ThrottlerFactory;
use Sunspikes\Ratelimit\Throttle\Hydrator\HydratorFactory;
use Sunspikes\Ratelimit\Throttle\Settings\ElasticWindowSettings;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;

/**
 * @Bean()
 * Class RateLimiterHandler
 * @package Swoft\RateLimiter\Handler
 */
class RateLimiterHandler
{
    /**
     * @param int $limit
     * @param int $time
     * @return RateLimiter
     */
    public function build(int $limit, int $time)
    {
        $cacheAdapter = new DesarrollaCacheAdapter(
            (new DesarrollaCacheFactory(null, App::getProperties()->get('rateLimiter')['config'])
            )->make());
        $setting = new ElasticWindowSettings($limit, $time);
        return new RateLimiter(new ThrottlerFactory($cacheAdapter), new HydratorFactory(), $setting);
    }

    /**
     * @param string $path
     * @param int $limit
     * @param int $time
     * @return bool
     */
    public function access(string $path, int $limit, int $time)
    {
        $rateLimiter = $this->build($limit, $time);

        return $rateLimiter->get($path)->access();
    }
}