<?php

declare(strict_types=1);

namespace Zend\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

use function Laminas\ContainerConfigTest\TestAsset\function_factory as laminas_function_factory;

/**
 * @deprecated Use Laminas\ContainerConfigTest\TestAsset\function_factory instead
 */
function function_factory(ContainerInterface $container, string $name) : Service
{
    laminas_function_factory(...func_get_args());
}
