<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

function function_factory_with_name(ContainerInterface $container, string $name) : FactoryService
{
    return new FactoryService(func_get_args());
}
