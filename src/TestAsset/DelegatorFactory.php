<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

class DelegatorFactory
{
    public function __invoke(ContainerInterface $container, $name, callable $callback)
    {
        return new Delegator($name, $callback);
    }
}
