<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

class Delegator1Factory
{
    public function __invoke(ContainerInterface $container, $name, callable $callback)
    {
        $service = $callback();
        $service->inject(static::class);

        return $service;
    }
}
