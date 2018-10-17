<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/8/21
 * Time: 16:02
 */

namespace Swoft\RateLimiter\Handler;

use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\storage\FileStorage;
use bandwidthThrottle\tokenBucket\TokenBucket;
use Swoft\App;
use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;
use Swoft\RateLimiter\Storage\CoRedisStorage;
use Swoft\Redis\Redis;

/**
 * @Bean()
 * Class RateLimiterHandler
 * @package Swoft\RateLimiter\Handler
 */
class RateLimiterHandler
{
    /**
     * @var TokenBucket[]
     */
    private $buckets;

    /**
     * @Inject()
     * @var Redis
     */
    private $redis;

    /**
     * @param string $path
     * @param int $limit
     * @param int $capacity
     * @return TokenBucket
     * @throws \bandwidthThrottle\tokenBucket\storage\StorageException
     */
    public function build(string $path, int $limit, int $capacity)
    {
        $storage = new CoRedisStorage($path, $this->redis);
        $rate = new Rate($limit, Rate::SECOND);
        $bucket = new TokenBucket($capacity, $rate, $storage);
        $bucket->bootstrap($limit);
        $this->setBucket($path, $bucket);
        return $bucket;
    }

    /**
     * @param string $path
     * @return TokenBucket|null
     */
    public function getBucket(string $path)
    {
        return $this->buckets[$path];
    }

    public function setBucket(string $path, TokenBucket $bucket)
    {
        $this->buckets[$path] = $bucket;
    }
}