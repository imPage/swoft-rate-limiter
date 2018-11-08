<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

return [
    'version' => '1.0',
    'autoInitBean' => true,
    'beanScan' => [
        'Swoft\\RateLimiter' => BASE_PATH . '/../src',
        'SwoftTest\\Testing'=> BASE_PATH .'/Testing',
    ],
    'cache' => require dirname(__FILE__) . DS . 'cache.php',
];
