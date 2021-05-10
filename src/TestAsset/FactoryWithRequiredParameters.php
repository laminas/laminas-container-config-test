<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

class FactoryWithRequiredParameters
{
    public function __construct(array $params)
    {
    }

    public function __invoke(ContainerInterface $container, string $name)
    {
    }
}
