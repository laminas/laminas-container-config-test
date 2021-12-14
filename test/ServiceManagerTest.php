<?php

declare(strict_types=1);

namespace LaminasTest\ContainerConfigTest;

use Laminas\ContainerConfigTest\AbstractMezzioContainerConfigTest;
use Laminas\ContainerConfigTest\SharedTestTrait;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

class ServiceManagerTest extends AbstractMezzioContainerConfigTest
{
    use SharedTestTrait;

    /**
     * @return ServiceManager
     */
    public function createContainer(array $config): ContainerInterface
    {
        return new ServiceManager($config);
    }
}
