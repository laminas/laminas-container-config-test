<?php
/**
 * @see       https://github.com/zendframework/zend-container-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerTest\TestAsset;

use Psr\Container\ContainerInterface;

class FactoryStatic
{
    public static function create(ContainerInterface $container, string $name) : Service
    {
        return new Service();
    }

    public static function withName() : array
    {
        return func_get_args();
    }
}
