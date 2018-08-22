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
use Swoft\Bean\Annotation\Value;

/**
 * @Bean()
 * Class RateLimiterHandler
 * @package Swoft\RateLimiter\Handler
 */
class RateLimiterHandler
{
    /**
     * @Value(name="${config.rateLimiter.cache_dir}")
     * @var string
     */
    private $cache_dir;

    /**
     * @var TokenBucket[]
     */
    private $buckets;

    /**
     * @param string $path
     * @param int $limit
     * @param int $capacity
     * @return TokenBucket
     * @throws \bandwidthThrottle\tokenBucket\storage\StorageException
     */
    public function build(string $path, int $limit, int $capacity)
    {
        // 后续抽象Storage出来
        $storage = new FileStorage($this->cache_dir.'/'.$path);
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