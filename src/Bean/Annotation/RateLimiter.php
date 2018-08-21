<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/7/23
 * Time: 10:45
 */

namespace Swoft\RateLimiter\Bean\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("ALL")
 * Class RateLimiter
 * @package Swoft\RateLimiter\Bean\Annotation
 */
class RateLimiter
{
    /**
     * 限制次数
     * @var int|null
     */
    private $limit;

    /**
     * 冷却时间
     * @var int|null
     */
    private $time;

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
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time)
    {
        $this->time = $time;
    }

    public function __construct(array $values)
    {
        if (isset($values['limit'])) {
            $this->limit = $values['limit'];
        }

        if (isset($values['time'])) {
            $this->time = $values['time'];
        }
    }
}