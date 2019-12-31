<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

class FactoryStatic
{
    public static function create(ContainerInterface $container, string $name) : Service
    {
        return new Service();
    }

    public static function withName() : FactoryService
    {
        return new FactoryService(func_get_args());
    }
}
