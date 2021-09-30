<?php

declare(strict_types=1);

namespace Zend\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

use function Laminas\ContainerConfigTest\TestAsset\function_factory_with_name as laminas_function_factory_with_name;

/**
 * @deprecated Use Laminas\ContainerConfigTest\TestAsset\function_factory_with_name instead
 */
function function_factory_with_name(ContainerInterface $container, string $name) : FactoryService
{
    laminas_function_factory_with_name(...func_get_args());
}
