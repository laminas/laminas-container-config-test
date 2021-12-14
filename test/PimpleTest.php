<?php

declare(strict_types=1);

namespace LaminasTest\ContainerConfigTest;

use Laminas\ContainerConfigTest\AbstractMezzioContainerConfigTest;
use Laminas\ContainerConfigTest\SharedTestTrait;
use Laminas\Pimple\Config\Config;
use Laminas\Pimple\Config\ContainerFactory;
use Pimple\Psr11\Container;
use Psr\Container\ContainerInterface;

class PimpleTest extends AbstractMezzioContainerConfigTest
{
    use SharedTestTrait;

    /**
     * @param array<string,mixed> $config
     * @return Container
     */
    public function createContainer(array $config): ContainerInterface
    {
        $factory = new ContainerFactory();

        return $factory(new Config(['dependencies' => $config]));
    }
}
