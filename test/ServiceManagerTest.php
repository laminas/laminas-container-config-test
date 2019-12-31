<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ContainerConfigTest;

use Laminas\ContainerConfigTest\AbstractMezzioContainerConfigTest;
use Laminas\ContainerConfigTest\SharedTestTrait;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

class ServiceManagerTest extends AbstractMezzioContainerConfigTest
{
    use SharedTestTrait;

    public function createContainer(array $config) : ContainerInterface
    {
        return new ServiceManager($config);
    }
}
