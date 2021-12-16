<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

use Psr\Container\ContainerInterface;

class DelegatorFactory
{
    /**
     * @param mixed $name
     * @return Delegator
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback)
    {
        return new Delegator($name, $callback);
    }
}
