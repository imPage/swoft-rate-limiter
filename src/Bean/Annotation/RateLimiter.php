<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/7/23
 * Time: 10:45
 */

namespace Swoft\RateLimiter\Bean\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use phpDocumentor\Reflection\Types\Callable_;

/**
 * @Annotation
 * @Target("ALL")
 * Class RateLimiter
 * @package Swoft\RateLimiter\Bean\Annotation
 */
class RateLimiter
{
    /**
     * 每秒生成
     * @var int|null
     */
    private $limit;

    /**
     * 桶容量
     * @var int|null
     */
    private $capacity;

    /**
     * 限流回调
     * @var array|null
     */
    private $callback;

    /**
     * @return array|null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param array $callback
     */
    public function setCallback(array $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int|null
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param int|null $capacity
     */
    public function setCapacity(int $capacity)
    {
        $this->capacity = $capacity;
    }

    public function __construct(array $values)
    {
        if (isset($values['limit'])) {
            $this->limit = $values['limit'];
        }

        if (isset($values['capacity'])) {
            $this->capacity = $values['capacity'];
        }

        if (isset($values['callback'])) {
            $this->callback = $values['callback'];
        }
    }
}