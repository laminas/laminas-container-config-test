<?php

declare(strict_types=1);

namespace LaminasTest\ContainerConfigTest;

use Aura\Di\Container;
use Laminas\AuraDi\Config\Config;
use Laminas\AuraDi\Config\ContainerFactory;
use Laminas\ContainerConfigTest\AbstractMezzioContainerConfigTest;
use Psr\Container\ContainerInterface;

class AuraDiTest extends AbstractMezzioContainerConfigTest
{
    /**
     * @return Container
     */
    public function createContainer(array $config): ContainerInterface
    {
        $factory = new ContainerFactory();

        return $factory(new Config(['dependencies' => $config]));
    }
}
