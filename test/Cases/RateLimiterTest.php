<?php
/**
 * Created by PhpStorm.
 * User: zcm
 * Date: 2018/11/8
 * Time: 12:24
 */

namespace SwoftTest\RateLimiter;

class RateLimiterTest extends AbstractTestCase
{
    public function testRateLimiter()
    {
        $success = 'success';
        $callback = 'callback';

        $res = $this->get('/rate-limiter/callback');
        $this->assertEquals($success, $res);

        $res = $this->get('/rate-limiter/callback');
        $this->assertEquals($callback, $res);

        sleep(1);

        $res = $this->get('/rate-limiter/callback');
        $this->assertEquals($success, $res);

        $res = $this->get('/rate-limiter/callback');
        $this->assertEquals($callback, $res);
    }
}