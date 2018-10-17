<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/10/17
 * Time: 11:06
 */

namespace Swoft\RateLimiter\Storage;

use malkusch\lock\mutex\RedisMutex;

class CoRedisMutex extends RedisMutex
{/**
 * Sets the Redis connections.
 *
 * @param Client[] $clients The Redis clients.
 * @param string   $name    The lock name.
 * @param int      $timeout The time in seconds a lock expires, default is 3.
 *
 * @throws \LengthException The timeout must be greater than 0.
 */
    public function __construct(array $clients, $name, $timeout = 3)
    {
        parent::__construct($clients, $name, $timeout);
    }

    /**
     * @internal
     */
    protected function add($client, $key, $value, $expire)
    {
        try {
            if ($result = $client->setNx($key, $value)){
                $result = $client->expire($key, $expire);
            }
            return $result;
        } catch (PredisException $e) {
            $message = sprintf(
                "Failed to acquire lock for key '%s' at %s",
                $key,
                $this->getRedisIdentifier($client)
            );
            throw new LockAcquireException($message, 0, $e);
        }
    }

    /**
     * @internal
     */
    protected function evalScript($client, $script, $numkeys, array $arguments)
    {
        // TODO key 的前缀和value 的序列化...直接爆炸
        try {
            [$key, $value] = $arguments;
            if ($result = $client->get($key) == $value){
                $result = $client->del($key);
            }
            return $result;
        } catch (PredisException $e) {
            $message = sprintf(
                "Failed to release lock at %s",
                $this->getRedisIdentifier($client)
            );
            throw new LockReleaseException($message, 0, $e);
        }
    }

    /**
     * @internal
     */
    protected function getRedisIdentifier($client)
    {
        return (string) $client->getConnection();
    }
}