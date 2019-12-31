<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

use function Laminas\ContainerConfigTest\TestAsset\factory as laminas_factory;

/**
 * @deprecated Use Laminas\ContainerConfigTest\TestAsset\factory instead
 */
function factory(ContainerInterface $container, string $name) : Service
{
    laminas_factory(...func_get_args());
}
