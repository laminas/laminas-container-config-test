<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

use function Laminas\ContainerConfigTest\TestAsset\factoryWithName as laminas_factoryWithName;

/**
 * @deprecated Use Laminas\ContainerConfigTest\TestAsset\factoryWithName instead
 */
function factoryWithName(ContainerInterface $container, string $name) : FactoryService
{
    laminas_factoryWithName(...func_get_args());
}
