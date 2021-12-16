<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

use function func_get_args;

class FactoryStatic
{
    public static function create(ContainerInterface $container, string $name): Service
    {
        return new Service();
    }

    public static function withName(): FactoryService
    {
        return new FactoryService(func_get_args());
    }
}
