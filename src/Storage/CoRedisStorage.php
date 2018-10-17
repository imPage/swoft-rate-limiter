<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/10/16
 * Time: 17:16
 */

namespace Swoft\RateLimiter\Storage;

use bandwidthThrottle\tokenBucket\storage\scope\GlobalScope;
use bandwidthThrottle\tokenBucket\storage\Storage;
use bandwidthThrottle\tokenBucket\storage\StorageException;
use bandwidthThrottle\tokenBucket\util\DoublePacker;
use RedisException;
use Psr\SimpleCache\CacheInterface;

class CoRedisStorage implements Storage,GlobalScope
{
    /**
     * @var Mutex The mutex.
     */
    private $mutex;

    /**
     * @var Redis The redis API.
     */
    private $redis;

    /**
     * @var string The key.
     */
    private $key;

    public function __construct($name, CacheInterface $redis)
    {
        $this->key   = $name;
        $this->redis = $redis;
        $this->mutex = new CoRedisMutex([$redis], $name);
    }

    public function bootstrap($microtime)
    {
        $this->setMicrotime($microtime);
    }

    public function isBootstrapped()
    {
        try {
            return $this->redis->exists($this->key);
        } catch (RedisException $e) {
            throw new StorageException("Failed to check for key existence", 0, $e);
        }
    }

    public function remove()
    {
        try {
            if (!$this->redis->del($this->key)) {
                throw new StorageException("Failed to delete key");
            }
        } catch (RedisException $e) {
            throw new StorageException("Failed to delete key", 0, $e);
        }
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function setMicrotime($microtime)
    {
        try {
            $data = DoublePacker::pack($microtime);

            if (!$this->redis->set($this->key, $data)) {
                throw new StorageException("Failed to store microtime");
            }
        } catch (RedisException $e) {
            throw new StorageException("Failed to store microtime", 0, $e);
        }
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function getMicrotime()
    {
        try {
            $data = $this->redis->get($this->key);
            if ($data === false) {
                throw new StorageException("Failed to get microtime");
            }
            return DoublePacker::unpack($data);
        } catch (RedisException $e) {
            throw new StorageException("Failed to get microtime", 0, $e);
        }
    }

    public function getMutex()
    {
        return $this->mutex;
    }

    public function letMicrotimeUnchanged()
    {
    }
}