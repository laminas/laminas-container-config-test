<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

function function_factory(ContainerInterface $container, string $name): Service
{
    return new Service();
}
